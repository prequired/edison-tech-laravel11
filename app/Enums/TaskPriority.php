<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Task Priority Enum
 *
 * Defines the different priority levels a task can have.
 */
enum TaskPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Urgent = 'urgent';

    /**
     * Get the human-readable label for the priority.
     */
    public function label(): string
    {
        return match($this) {
            self::Low => 'Low',
            self::Medium => 'Medium',
            self::High => 'High',
            self::Urgent => 'Urgent',
        };
    }

    /**
     * Get the badge color class for the priority.
     */
    public function badge(): string
    {
        return match($this) {
            self::Low => 'info',
            self::Medium => 'primary',
            self::High => 'warning',
            self::Urgent => 'danger',
        };
    }

    /**
     * Get all priorities as value => label array.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn($case) => $case->label(), self::cases())
        );
    }

    /**
     * Get all priority values.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
