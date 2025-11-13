<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Payment Method Enum
 *
 * Defines the different payment methods available.
 */
enum PaymentMethod: string
{
    case Stripe = 'stripe';
    case BankTransfer = 'bank_transfer';
    case Check = 'check';
    case Cash = 'cash';
    case Other = 'other';

    /**
     * Get the human-readable label for the payment method.
     */
    public function label(): string
    {
        return match($this) {
            self::Stripe => 'Stripe',
            self::BankTransfer => 'Bank Transfer',
            self::Check => 'Check',
            self::Cash => 'Cash',
            self::Other => 'Other',
        };
    }

    /**
     * Get the badge color class for the payment method.
     */
    public function badge(): string
    {
        return match($this) {
            self::Stripe => 'primary',
            self::BankTransfer => 'info',
            self::Check => 'warning',
            self::Cash => 'success',
            self::Other => 'secondary',
        };
    }

    /**
     * Get all payment methods as value => label array.
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
     * Get all payment method values.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
