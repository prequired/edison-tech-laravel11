<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\ProjectStatusChanged;
use App\Models\Project;
use App\Models\ProjectStatusHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UpdateProjectStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly int $projectId,
        public readonly string $newStatus,
        public readonly ?int $changedByUserId = null,
        public readonly ?string $notes = null
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $project = Project::with(['company.users'])->findOrFail($this->projectId);
        $oldStatus = $project->status->value;

        DB::transaction(function () use ($project, $oldStatus) {
            // Update project status
            $project->update(['status' => $this->newStatus]);

            // Record status history
            ProjectStatusHistory::create([
                'project_id' => $project->id,
                'old_status' => $oldStatus,
                'new_status' => $this->newStatus,
                'changed_by' => $this->changedByUserId,
                'notes' => $this->notes,
                'changed_at' => now(),
            ]);

            // Notify client users
            $clientUsers = $project->company->users()
                ->where('role', 'client')
                ->where('is_active', true)
                ->get();

            foreach ($clientUsers as $user) {
                Mail::to($user->email)->send(
                    new ProjectStatusChanged($project, $oldStatus, $this->newStatus)
                );
            }
        });
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Failed to update project status', [
            'project_id' => $this->projectId,
            'new_status' => $this->newStatus,
            'error' => $exception->getMessage(),
        ]);
    }
}
