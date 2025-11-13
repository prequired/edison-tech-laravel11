<?php

declare(strict_types=1);

namespace App\DTOs;

/**
 * Data Transfer Object for creating a new user.
 *
 * @package App\DTOs
 */
readonly class CreateUserDTO
{
    /**
     * Create a new CreateUserDTO instance.
     *
     * @param int $company_id The company ID
     * @param string $name The user's full name
     * @param string $email The user's email address
     * @param string $password The user's password (will be hashed)
     * @param string $role The user's role (e.g., 'admin', 'employee', 'client')
     * @param string|null $phone The user's phone number
     * @param string|null $avatar The user's avatar URL
     * @param bool $is_active Whether the user is active (default: true)
     * @param string|null $timezone The user's timezone
     * @param array|null $preferences User preferences
     */
    public function __construct(
        public int $company_id,
        public string $name,
        public string $email,
        public string $password,
        public string $role,
        public ?string $phone = null,
        public ?string $avatar = null,
        public bool $is_active = true,
        public ?string $timezone = null,
        public ?array $preferences = null,
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
            email: (string) $data['email'],
            password: (string) $data['password'],
            role: (string) $data['role'],
            phone: $data['phone'] ?? null,
            avatar: $data['avatar'] ?? null,
            is_active: $data['is_active'] ?? true,
            timezone: $data['timezone'] ?? null,
            preferences: $data['preferences'] ?? null,
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
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'is_active' => $this->is_active,
            'timezone' => $this->timezone,
            'preferences' => $this->preferences,
        ];
    }
}
