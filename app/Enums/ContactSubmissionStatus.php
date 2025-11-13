<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Contact Submission Status Enum
 *
 * Defines the different statuses a contact submission can have.
 */
enum ContactSubmissionStatus: string
{
    case New = 'new';
    case InProgress = 'in_progress';
    case Responded = 'responded';
    case Closed = 'closed';

    /**
     * Get the human-readable label for the status.
     */
    public function label(): string
    {
        return match($this) {
            self::New => 'New',
            self::InProgress => 'In Progress',
            self::Responded => 'Responded',
            self::Closed => 'Closed',
        };
    }

    /**
     * Get the badge color class for the status.
     */
    public function badge(): string
    {
        return match($this) {
            self::New => 'info',
            self::InProgress => 'warning',
            self::Responded => 'primary',
            self::Closed => 'success',
        };
    }

    /**
     * Get all statuses as value => label array.
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
     * Get all status values.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
