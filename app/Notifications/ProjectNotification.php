<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Project $project,
        public readonly string $action,
        public readonly ?string $additionalInfo = null
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->getSubject())
            ->greeting($this->getGreeting())
            ->line($this->getBody())
            ->action('View Project', route('client.projects.show', $this->project))
            ->line('If you have any questions, please don\'t hesitate to contact us.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
            'action' => $this->action,
            'status' => $this->project->status->value,
            'additional_info' => $this->additionalInfo,
        ];
    }

    /**
     * Get the notification subject based on action.
     */
    private function getSubject(): string
    {
        return match ($this->action) {
            'created' => 'New Project Created: ' . $this->project->name,
            'status_changed' => 'Project Status Updated: ' . $this->project->name,
            'completed' => 'Project Completed: ' . $this->project->name,
            'milestone' => 'Project Milestone Reached: ' . $this->project->name,
            default => 'Project Update: ' . $this->project->name,
        };
    }

    /**
     * Get the greeting based on action.
     */
    private function getGreeting(): string
    {
        return match ($this->action) {
            'created' => 'Hello! We\'ve started a new project for you.',
            'status_changed' => 'Your project status has been updated.',
            'completed' => 'Great news! Your project has been completed.',
            'milestone' => 'We\'ve reached an important milestone.',
            default => 'Your project has been updated.',
        };
    }

    /**
     * Get the body message based on action.
     */
    private function getBody(): string
    {
        $statusLabel = $this->project->status->label();

        return match ($this->action) {
            'created' => "Project \"{$this->project->name}\" has been created and is now in {$statusLabel} status.",
            'status_changed' => "Project \"{$this->project->name}\" status has been changed to {$statusLabel}." . ($this->additionalInfo ? " {$this->additionalInfo}" : ''),
            'completed' => "Project \"{$this->project->name}\" has been successfully completed. We hope you're satisfied with the results!",
            'milestone' => "Project \"{$this->project->name}\" has reached a significant milestone." . ($this->additionalInfo ? " {$this->additionalInfo}" : ''),
            default => "Project \"{$this->project->name}\" has been updated.",
        };
    }
}
