<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * User Role Enum
 *
 * Defines the different roles a user can have in the system.
 */
enum UserRole: string
{
    case Admin = 'admin';
    case Employee = 'employee';
    case Client = 'client';

    /**
     * Get the human-readable label for the role.
     */
    public function label(): string
    {
        return match($this) {
            self::Admin => 'Admin',
            self::Employee => 'Employee',
            self::Client => 'Client',
        };
    }

    /**
     * Get the badge color class for the role.
     */
    public function badge(): string
    {
        return match($this) {
            self::Admin => 'danger',
            self::Employee => 'info',
            self::Client => 'success',
        };
    }

    /**
     * Get all roles as value => label array.
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
     * Get all role values.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
