<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateProjectDTO;
use App\Models\Project;
use App\Models\ProjectStatusHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service class for handling project-related business logic.
 *
 * @package App\Services
 */
class ProjectService
{
    /**
     * Create a new ProjectService instance.
     */
    public function __construct(
        private readonly Project $projectModel,
    ) {}

    /**
     * Create a new project.
     *
     * @param CreateProjectDTO $dto
     * @return Project
     * @throws \Throwable
     */
    public function create(CreateProjectDTO $dto): Project
    {
        return DB::transaction(function () use ($dto) {
            $project = Project::create([
                'company_id' => $dto->company_id,
                'created_by' => $dto->created_by,
                'name' => $dto->name,
                'slug' => Str::slug($dto->name),
                'description' => $dto->description,
                'status' => $dto->status,
                'type' => $dto->type,
                'budget' => $dto->budget,
                'estimated_hours' => $dto->estimated_hours,
                'actual_hours' => 0,
                'start_date' => $dto->start_date,
                'deadline' => $dto->deadline,
                'priority' => $dto->priority,
                'is_billable' => $dto->is_billable,
                'hourly_rate' => $dto->hourly_rate,
                'technologies' => $dto->technologies,
                'repository_url' => $dto->repository_url,
                'staging_url' => $dto->staging_url,
                'production_url' => $dto->production_url,
                'notes' => $dto->notes,
                'progress' => 0,
            ]);

            // Create initial status history
            ProjectStatusHistory::create([
                'project_id' => $project->id,
                'status' => $dto->status,
                'changed_by' => $dto->created_by,
                'notes' => 'Project created',
            ]);

            return $project->load('company', 'creator');
        });
    }

    /**
     * Update an existing project.
     *
     * @param Project $project
     * @param array<string, mixed> $data
     * @return Project
     * @throws \Throwable
     */
    public function update(Project $project, array $data): Project
    {
        return DB::transaction(function () use ($project, $data) {
            // Update slug if name changes
            if (isset($data['name']) && $data['name'] !== $project->name) {
                $data['slug'] = Str::slug($data['name']);
            }

            $project->update($data);

            return $project->fresh();
        });
    }

    /**
     * Update the status of a project.
     *
     * @param Project $project
     * @param string $status
     * @param int $changed_by
     * @param string|null $notes
     * @return Project
     * @throws \Throwable
     */
    public function updateStatus(Project $project, string $status, int $changed_by, ?string $notes = null): Project
    {
        return DB::transaction(function () use ($project, $status, $changed_by, $notes) {
            $oldStatus = $project->status;

            $project->update(['status' => $status]);

            // If status is completed, set completed_at
            if ($status === 'completed' && $project->completed_at === null) {
                $project->update(['completed_at' => now()]);
            }

            // Create status history record
            ProjectStatusHistory::create([
                'project_id' => $project->id,
                'status' => $status,
                'changed_by' => $changed_by,
                'notes' => $notes ?? "Status changed from {$oldStatus} to {$status}",
            ]);

            return $project->fresh();
        });
    }

    /**
     * Assign users to a project.
     *
     * @param Project $project
     * @param array<int, array<string, mixed>> $users Array of user IDs with optional roles
     * @return Project
     * @throws \Throwable
     */
    public function assignUsers(Project $project, array $users): Project
    {
        return DB::transaction(function () use ($project, $users) {
            // Prepare sync data with roles
            $syncData = [];
            foreach ($users as $userId => $data) {
                $syncData[$userId] = [
                    'role' => $data['role'] ?? 'member',
                ];
            }

            $project->users()->sync($syncData);

            return $project->fresh(['users']);
        });
    }

    /**
     * Calculate and update project progress based on completed tasks.
     *
     * @param Project $project
     * @return Project
     * @throws \Throwable
     */
    public function calculateProgress(Project $project): Project
    {
        return DB::transaction(function () use ($project) {
            $totalTasks = $project->tasks()->count();

            if ($totalTasks === 0) {
                $progress = 0;
            } else {
                $completedTasks = $project->tasks()
                    ->where('status', 'completed')
                    ->count();

                $progress = (int) round(($completedTasks / $totalTasks) * 100);
            }

            // Calculate actual hours from time entries
            $actualHours = $project->timeEntries()->sum('hours');

            $project->update([
                'progress' => $progress,
                'actual_hours' => $actualHours,
            ]);

            return $project->fresh();
        });
    }

    /**
     * Get all projects for a company.
     *
     * @param int $companyId
     * @return Collection<int, Project>
     */
    public function getCompanyProjects(int $companyId): Collection
    {
        return Project::where('company_id', $companyId)
            ->with(['company', 'creator', 'users'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get active projects for a company.
     *
     * @param int $companyId
     * @return Collection<int, Project>
     */
    public function getActiveProjects(int $companyId): Collection
    {
        return Project::where('company_id', $companyId)
            ->whereIn('status', ['planning', 'in_progress'])
            ->with(['company', 'creator'])
            ->orderBy('deadline', 'asc')
            ->get();
    }
}
