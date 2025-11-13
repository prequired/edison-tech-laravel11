<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Blog Post Status Enum
 *
 * Defines the different statuses a blog post can have.
 */
enum BlogPostStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Scheduled = 'scheduled';

    /**
     * Get the human-readable label for the status.
     */
    public function label(): string
    {
        return match($this) {
            self::Draft => 'Draft',
            self::Published => 'Published',
            self::Scheduled => 'Scheduled',
        };
    }

    /**
     * Get the badge color class for the status.
     */
    public function badge(): string
    {
        return match($this) {
            self::Draft => 'secondary',
            self::Published => 'success',
            self::Scheduled => 'info',
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
