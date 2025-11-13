<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Invoice Status Enum
 *
 * Defines the different statuses an invoice can have.
 */
enum InvoiceStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Viewed = 'viewed';
    case Partial = 'partial';
    case Paid = 'paid';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';

    /**
     * Get the human-readable label for the status.
     */
    public function label(): string
    {
        return match($this) {
            self::Draft => 'Draft',
            self::Sent => 'Sent',
            self::Viewed => 'Viewed',
            self::Partial => 'Partial',
            self::Paid => 'Paid',
            self::Overdue => 'Overdue',
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
            self::Viewed => 'primary',
            self::Partial => 'warning',
            self::Paid => 'success',
            self::Overdue => 'danger',
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
