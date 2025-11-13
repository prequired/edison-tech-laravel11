<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\LogTimeEntryDTO;
use App\Models\Project;
use App\Models\TimeEntry;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Service class for handling time tracking-related business logic.
 *
 * @package App\Services
 */
class TimeTrackingService
{
    /**
     * Create a new TimeTrackingService instance.
     */
    public function __construct(
        private readonly TimeEntry $timeEntryModel,
    ) {}

    /**
     * Start a timer for a user.
     *
     * @param int $projectId
     * @param int $userId
     * @param int|null $taskId
     * @param string|null $description
     * @return TimeEntry
     * @throws \Throwable
     */
    public function startTimer(int $projectId, int $userId, ?int $taskId = null, ?string $description = null): TimeEntry
    {
        return DB::transaction(function () use ($projectId, $userId, $taskId, $description) {
            // Check if there's already an active timer for this user
            $activeTimer = TimeEntry::where('user_id', $userId)
                ->whereNull('end_time')
                ->first();

            if ($activeTimer) {
                throw new \RuntimeException('User already has an active timer running');
            }

            $project = Project::findOrFail($projectId);

            $timeEntry = TimeEntry::create([
                'project_id' => $projectId,
                'task_id' => $taskId,
                'user_id' => $userId,
                'description' => $description,
                'start_time' => now(),
                'end_time' => null,
                'hours' => 0,
                'is_billable' => $project->is_billable,
                'hourly_rate' => $project->hourly_rate,
                'amount' => 0,
                'is_invoiced' => false,
            ]);

            return $timeEntry->fresh(['project', 'task', 'user']);
        });
    }

    /**
     * Stop a running timer.
     *
     * @param TimeEntry $timeEntry
     * @return TimeEntry
     * @throws \Throwable
     */
    public function stopTimer(TimeEntry $timeEntry): TimeEntry
    {
        return DB::transaction(function () use ($timeEntry) {
            if ($timeEntry->end_time !== null) {
                throw new \RuntimeException('Timer has already been stopped');
            }

            $endTime = now();
            $startTime = Carbon::parse($timeEntry->start_time);

            // Calculate hours (rounded to 2 decimal places)
            $hours = round($startTime->diffInMinutes($endTime) / 60, 2);

            // Calculate billable amount
            $amount = $timeEntry->is_billable && $timeEntry->hourly_rate
                ? round($hours * $timeEntry->hourly_rate, 2)
                : 0;

            $timeEntry->update([
                'end_time' => $endTime,
                'hours' => $hours,
                'amount' => $amount,
            ]);

            // Update project's actual hours
            $this->updateProjectActualHours($timeEntry->project_id);

            return $timeEntry->fresh(['project', 'task', 'user']);
        });
    }

    /**
     * Log hours manually.
     *
     * @param LogTimeEntryDTO $dto
     * @return TimeEntry
     * @throws \Throwable
     */
    public function logHours(LogTimeEntryDTO $dto): TimeEntry
    {
        return DB::transaction(function () use ($dto) {
            $project = Project::findOrFail($dto->project_id);

            // Use DTO hourly rate or fall back to project rate
            $hourlyRate = $dto->hourly_rate ?? $project->hourly_rate;

            // Calculate amount
            $amount = $dto->is_billable && $hourlyRate
                ? round($dto->hours * $hourlyRate, 2)
                : 0;

            // Set start and end times if provided
            $startTime = $dto->start_time ?? now();
            $endTime = $dto->end_time ?? now();

            $timeEntry = TimeEntry::create([
                'project_id' => $dto->project_id,
                'task_id' => $dto->task_id,
                'user_id' => $dto->user_id,
                'description' => $dto->description,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'hours' => $dto->hours,
                'is_billable' => $dto->is_billable,
                'hourly_rate' => $hourlyRate,
                'amount' => $amount,
                'is_invoiced' => $dto->is_invoiced,
                'invoice_id' => $dto->invoice_id,
            ]);

            // Update project's actual hours
            $this->updateProjectActualHours($dto->project_id);

            return $timeEntry->fresh(['project', 'task', 'user']);
        });
    }

    /**
     * Calculate billable amount for time entries.
     *
     * @param int $projectId
     * @param bool $uninvoicedOnly
     * @return float
     */
    public function calculateBillableAmount(int $projectId, bool $uninvoicedOnly = false): float
    {
        $query = TimeEntry::where('project_id', $projectId)
            ->where('is_billable', true);

        if ($uninvoicedOnly) {
            $query->where('is_invoiced', false);
        }

        return (float) $query->sum('amount');
    }

    /**
     * Get time entries for a project.
     *
     * @param int $projectId
     * @param array<string, mixed> $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProjectTimeEntries(int $projectId, array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = TimeEntry::where('project_id', $projectId)
            ->with(['user', 'task']);

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['task_id'])) {
            $query->where('task_id', $filters['task_id']);
        }

        if (isset($filters['is_billable'])) {
            $query->where('is_billable', $filters['is_billable']);
        }

        if (isset($filters['is_invoiced'])) {
            $query->where('is_invoiced', $filters['is_invoiced']);
        }

        if (isset($filters['start_date'])) {
            $query->whereDate('start_time', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('start_time', '<=', $filters['end_date']);
        }

        return $query->orderBy('start_time', 'desc')->get();
    }

    /**
     * Get active timer for a user.
     *
     * @param int $userId
     * @return TimeEntry|null
     */
    public function getActiveTimer(int $userId): ?TimeEntry
    {
        return TimeEntry::where('user_id', $userId)
            ->whereNull('end_time')
            ->with(['project', 'task'])
            ->first();
    }

    /**
     * Update project's actual hours.
     *
     * @param int $projectId
     * @return void
     */
    private function updateProjectActualHours(int $projectId): void
    {
        $totalHours = TimeEntry::where('project_id', $projectId)->sum('hours');

        Project::where('id', $projectId)->update([
            'actual_hours' => $totalHours,
        ]);
    }
}
