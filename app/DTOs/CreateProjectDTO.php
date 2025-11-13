<?php

declare(strict_types=1);

namespace App\DTOs;

/**
 * Data Transfer Object for creating a new project.
 *
 * @package App\DTOs
 */
readonly class CreateProjectDTO
{
    /**
     * Create a new CreateProjectDTO instance.
     *
     * @param int $company_id The company ID
     * @param string $name The project name
     * @param string|null $description The project description
     * @param float|null $budget The project budget
     * @param string|null $start_date The project start date (Y-m-d format)
     * @param string|null $deadline The project deadline (Y-m-d format)
     * @param string|null $type The project type
     * @param int|null $created_by The user ID who created the project
     * @param string $status The project status (default: 'planning')
     * @param string $priority The project priority (default: 'medium')
     * @param float|null $estimated_hours Estimated hours for the project
     * @param bool $is_billable Whether the project is billable (default: true)
     * @param float|null $hourly_rate The hourly rate for billing
     * @param array|null $technologies Technologies used in the project
     * @param string|null $repository_url Repository URL
     * @param string|null $staging_url Staging URL
     * @param string|null $production_url Production URL
     * @param string|null $notes Additional notes
     */
    public function __construct(
        public int $company_id,
        public string $name,
        public ?string $description = null,
        public ?float $budget = null,
        public ?string $start_date = null,
        public ?string $deadline = null,
        public ?string $type = null,
        public ?int $created_by = null,
        public string $status = 'planning',
        public string $priority = 'medium',
        public ?float $estimated_hours = null,
        public bool $is_billable = true,
        public ?float $hourly_rate = null,
        public ?array $technologies = null,
        public ?string $repository_url = null,
        public ?string $staging_url = null,
        public ?string $production_url = null,
        public ?string $notes = null,
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
            company_id: (int) $data['company_id'],
            name: (string) $data['name'],
            description: $data['description'] ?? null,
            budget: isset($data['budget']) ? (float) $data['budget'] : null,
            start_date: $data['start_date'] ?? null,
            deadline: $data['deadline'] ?? null,
            type: $data['type'] ?? null,
            created_by: isset($data['created_by']) ? (int) $data['created_by'] : null,
            status: $data['status'] ?? 'planning',
            priority: $data['priority'] ?? 'medium',
            estimated_hours: isset($data['estimated_hours']) ? (float) $data['estimated_hours'] : null,
            is_billable: $data['is_billable'] ?? true,
            hourly_rate: isset($data['hourly_rate']) ? (float) $data['hourly_rate'] : null,
            technologies: $data['technologies'] ?? null,
            repository_url: $data['repository_url'] ?? null,
            staging_url: $data['staging_url'] ?? null,
            production_url: $data['production_url'] ?? null,
            notes: $data['notes'] ?? null,
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
            'company_id' => $this->company_id,
            'name' => $this->name,
            'description' => $this->description,
            'budget' => $this->budget,
            'start_date' => $this->start_date,
            'deadline' => $this->deadline,
            'type' => $this->type,
            'created_by' => $this->created_by,
            'status' => $this->status,
            'priority' => $this->priority,
            'estimated_hours' => $this->estimated_hours,
            'is_billable' => $this->is_billable,
            'hourly_rate' => $this->hourly_rate,
            'technologies' => $this->technologies,
            'repository_url' => $this->repository_url,
            'staging_url' => $this->staging_url,
            'production_url' => $this->production_url,
            'notes' => $this->notes,
        ];
    }
}
