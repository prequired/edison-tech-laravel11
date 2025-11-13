<?php

declare(strict_types=1);

namespace App\DTOs;

/**
 * Data Transfer Object for logging a time entry.
 *
 * @package App\DTOs
 */
readonly class LogTimeEntryDTO
{
    /**
     * Create a new LogTimeEntryDTO instance.
     *
     * @param int $project_id The project ID
     * @param int|null $task_id The task ID (optional)
     * @param int $user_id The user ID
     * @param float $hours The number of hours
     * @param string|null $description The time entry description
     * @param bool $is_billable Whether the time is billable (default: true)
     * @param float|null $hourly_rate The hourly rate for billing
     * @param string|null $start_time The start time (Y-m-d H:i:s format)
     * @param string|null $end_time The end time (Y-m-d H:i:s format)
     * @param bool $is_invoiced Whether the time has been invoiced (default: false)
     * @param int|null $invoice_id The invoice ID if invoiced
     */
    public function __construct(
        public int $project_id,
        public ?int $task_id,
        public int $user_id,
        public float $hours,
        public ?string $description = null,
        public bool $is_billable = true,
        public ?float $hourly_rate = null,
        public ?string $start_time = null,
        public ?string $end_time = null,
        public bool $is_invoiced = false,
        public ?int $invoice_id = null,
    ) {}

    /**
     * Create a new instance from an array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function from(array $data): self
    {
        return new self(
            project_id: (int) $data['project_id'],
            task_id: isset($data['task_id']) ? (int) $data['task_id'] : null,
            user_id: (int) $data['user_id'],
            hours: (float) $data['hours'],
            description: $data['description'] ?? null,
            is_billable: $data['is_billable'] ?? true,
            hourly_rate: isset($data['hourly_rate']) ? (float) $data['hourly_rate'] : null,
            start_time: $data['start_time'] ?? null,
            end_time: $data['end_time'] ?? null,
            is_invoiced: $data['is_invoiced'] ?? false,
            invoice_id: isset($data['invoice_id']) ? (int) $data['invoice_id'] : null,
        );
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'project_id' => $this->project_id,
            'task_id' => $this->task_id,
            'user_id' => $this->user_id,
            'hours' => $this->hours,
            'description' => $this->description,
            'is_billable' => $this->is_billable,
            'hourly_rate' => $this->hourly_rate,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_invoiced' => $this->is_invoiced,
            'invoice_id' => $this->invoice_id,
        ];
    }
}
