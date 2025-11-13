<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Contract Type Enum
 *
 * Defines the different types of contracts available.
 */
enum ContractType: string
{
    case FixedPrice = 'fixed_price';
    case Hourly = 'hourly';
    case Retainer = 'retainer';

    /**
     * Get the human-readable label for the type.
     */
    public function label(): string
    {
        return match($this) {
            self::FixedPrice => 'Fixed Price',
            self::Hourly => 'Hourly',
            self::Retainer => 'Retainer',
        };
    }

    /**
     * Get the badge color class for the type.
     */
    public function badge(): string
    {
        return match($this) {
            self::FixedPrice => 'primary',
            self::Hourly => 'info',
            self::Retainer => 'success',
        };
    }

    /**
     * Get all types as value => label array.
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
     * Get all type values.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
