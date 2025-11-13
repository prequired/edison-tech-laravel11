<?php

declare(strict_types=1);

namespace App\DTOs;

/**
 * Data Transfer Object for creating a new company.
 *
 * @package App\DTOs
 */
readonly class CreateCompanyDTO
{
    /**
     * Create a new CreateCompanyDTO instance.
     *
     * @param string $name The company name
     * @param string $email The company email address
     * @param string|null $phone The company phone number
     * @param string|null $website The company website URL
     * @param string|null $address The company street address
     * @param string|null $city The company city
     * @param string|null $state The company state/province
     * @param string|null $country The company country
     * @param string|null $postal_code The company postal/zip code
     * @param string|null $tax_id The company tax ID
     * @param string|null $logo The company logo URL
     * @param string|null $notes Additional notes
     * @param bool $is_active Whether the company is active (default: true)
     * @param string|null $stripe_customer_id The Stripe customer ID
     */
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone = null,
        public ?string $website = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $country = null,
        public ?string $postal_code = null,
        public ?string $tax_id = null,
        public ?string $logo = null,
        public ?string $notes = null,
        public bool $is_active = true,
        public ?string $stripe_customer_id = null,
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
            name: (string) $data['name'],
            email: (string) $data['email'],
            phone: $data['phone'] ?? null,
            website: $data['website'] ?? null,
            address: $data['address'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            country: $data['country'] ?? null,
            postal_code: $data['postal_code'] ?? null,
            tax_id: $data['tax_id'] ?? null,
            logo: $data['logo'] ?? null,
            notes: $data['notes'] ?? null,
            is_active: $data['is_active'] ?? true,
            stripe_customer_id: $data['stripe_customer_id'] ?? null,
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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'tax_id' => $this->tax_id,
            'logo' => $this->logo,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
            'stripe_customer_id' => $this->stripe_customer_id,
        ];
    }
}
