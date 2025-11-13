<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\ContactSubmission;
use App\Models\ProjectStatusHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CleanupOldDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 1;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $cleanedRecords = 0;

        // Clean up old activity logs (older than 90 days)
        $activityLogsDeleted = ActivityLog::where('created_at', '<', now()->subDays(90))->delete();
        $cleanedRecords += $activityLogsDeleted;

        // Clean up archived contact submissions (older than 180 days)
        $contactsDeleted = ContactSubmission::where('status', 'archived')
            ->where('updated_at', '<', now()->subDays(180))
            ->delete();
        $cleanedRecords += $contactsDeleted;

        // Clean up old project status history (older than 365 days)
        $statusHistoryDeleted = ProjectStatusHistory::where('changed_at', '<', now()->subDays(365))->delete();
        $cleanedRecords += $statusHistoryDeleted;

        // Clean up unverified newsletter subscribers (older than 30 days)
        $unverifiedSubscribers = \App\Models\NewsletterSubscriber::where('status', 'unverified')
            ->where('created_at', '<', now()->subDays(30))
            ->delete();
        $cleanedRecords += $unverifiedSubscribers;

        \Log::info('Cleanup job completed', [
            'total_records_deleted' => $cleanedRecords,
            'activity_logs' => $activityLogsDeleted,
            'contacts' => $contactsDeleted,
            'status_history' => $statusHistoryDeleted,
            'unverified_subscribers' => $unverifiedSubscribers,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Cleanup job failed', [
            'error' => $exception->getMessage(),
        ]);
    }
}
