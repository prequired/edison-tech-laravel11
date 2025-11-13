<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBulkEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 2;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 120;

    /**
     * Create a new job instance.
     *
     * @param  array<int, int>  $subscriberIds
     */
    public function __construct(
        public readonly array $subscriberIds,
        public readonly string $mailableClass,
        public readonly array $mailableData = []
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $subscribers = NewsletterSubscriber::whereIn('id', $this->subscriberIds)
            ->where('status', 'active')
            ->get();

        foreach ($subscribers as $subscriber) {
            try {
                /** @var Mailable $mailable */
                $mailable = new $this->mailableClass(...$this->mailableData);

                Mail::to($subscriber->email)->send($mailable);

                // Small delay to avoid rate limiting
                usleep(100000); // 0.1 seconds
            } catch (\Exception $e) {
                \Log::warning('Failed to send bulk email to subscriber', [
                    'subscriber_id' => $subscriber->id,
                    'email' => $subscriber->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Bulk email job failed', [
            'subscriber_count' => count($this->subscriberIds),
            'mailable' => $this->mailableClass,
            'error' => $exception->getMessage(),
        ]);
    }
}
