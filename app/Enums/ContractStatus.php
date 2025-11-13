<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Contract Status Enum
 *
 * Defines the different statuses a contract can have.
 */
enum ContractStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Signed = 'signed';
    case Active = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    /**
     * Get the human-readable label for the status.
     */
    public function label(): string
    {
        return match($this) {
            self::Draft => 'Draft',
            self::Sent => 'Sent',
            self::Signed => 'Signed',
            self::Active => 'Active',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    /**
     * Get the badge color class for the status.
     */
    public function badge(): string
    {
        return match($this) {
            self::Draft => 'secondary',
            self::Sent => 'info',
            self::Signed => 'primary',
            self::Active => 'success',
            self::Completed => 'success',
            self::Cancelled => 'danger',
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
