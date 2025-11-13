<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateCompanyDTO;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

/**
 * Service class for handling company-related business logic.
 *
 * @package App\Services
 */
class CompanyService
{
    /**
     * Create a new CompanyService instance.
     */
    public function __construct(
        private readonly Company $companyModel,
    ) {
        // Set Stripe API key
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a new company.
     *
     * @param CreateCompanyDTO $dto
     * @return Company
     * @throws \Throwable
     */
    public function create(CreateCompanyDTO $dto): Company
    {
        return DB::transaction(function () use ($dto) {
            $company = Company::create([
                'name' => $dto->name,
                'slug' => Str::slug($dto->name),
                'email' => $dto->email,
                'phone' => $dto->phone,
                'website' => $dto->website,
                'address' => $dto->address,
                'city' => $dto->city,
                'state' => $dto->state,
                'country' => $dto->country,
                'postal_code' => $dto->postal_code,
                'tax_id' => $dto->tax_id,
                'logo' => $dto->logo,
                'notes' => $dto->notes,
                'is_active' => $dto->is_active,
                'stripe_customer_id' => $dto->stripe_customer_id,
            ]);

            return $company->fresh();
        });
    }

    /**
     * Update an existing company.
     *
     * @param Company $company
     * @param array<string, mixed> $data
     * @return Company
     * @throws \Throwable
     */
    public function update(Company $company, array $data): Company
    {
        return DB::transaction(function () use ($company, $data) {
            // Update slug if name changes
            if (isset($data['name']) && $data['name'] !== $company->name) {
                $data['slug'] = Str::slug($data['name']);
            }

            $company->update($data);

            // Update Stripe customer if needed
            if ($company->stripe_customer_id && (isset($data['name']) || isset($data['email']))) {
                $this->updateStripeCustomer($company);
            }

            return $company->fresh();
        });
    }

    /**
     * Create a Stripe customer for a company.
     *
     * @param Company $company
     * @param array<string, mixed> $additionalData
     * @return Company
     * @throws ApiErrorException
     * @throws \Throwable
     */
    public function createStripeCustomer(Company $company, array $additionalData = []): Company
    {
        return DB::transaction(function () use ($company, $additionalData) {
            if ($company->stripe_customer_id) {
                throw new \RuntimeException('Company already has a Stripe customer ID');
            }

            $customerData = array_merge([
                'name' => $company->name,
                'email' => $company->email,
                'phone' => $company->phone,
                'address' => [
                    'line1' => $company->address,
                    'city' => $company->city,
                    'state' => $company->state,
                    'postal_code' => $company->postal_code,
                    'country' => $company->country,
                ],
                'metadata' => [
                    'company_id' => $company->id,
                    'tax_id' => $company->tax_id,
                ],
            ], $additionalData);

            // Remove null values
            $customerData = array_filter($customerData, function ($value) {
                return $value !== null;
            });

            $stripeCustomer = Customer::create($customerData);

            $company->update([
                'stripe_customer_id' => $stripeCustomer->id,
            ]);

            return $company->fresh();
        });
    }

    /**
     * Update Stripe customer information.
     *
     * @param Company $company
     * @return void
     * @throws ApiErrorException
     */
    private function updateStripeCustomer(Company $company): void
    {
        if (!$company->stripe_customer_id) {
            return;
        }

        Customer::update(
            $company->stripe_customer_id,
            [
                'name' => $company->name,
                'email' => $company->email,
                'phone' => $company->phone,
                'address' => [
                    'line1' => $company->address,
                    'city' => $company->city,
                    'state' => $company->state,
                    'postal_code' => $company->postal_code,
                    'country' => $company->country,
                ],
            ]
        );
    }

    /**
     * Deactivate a company.
     *
     * @param Company $company
     * @return Company
     * @throws \Throwable
     */
    public function deactivate(Company $company): Company
    {
        return DB::transaction(function () use ($company) {
            $company->update([
                'is_active' => false,
            ]);

            // Optionally deactivate all users
            $company->users()->update([
                'is_active' => false,
            ]);

            return $company->fresh();
        });
    }

    /**
     * Activate a company.
     *
     * @param Company $company
     * @return Company
     * @throws \Throwable
     */
    public function activate(Company $company): Company
    {
        return DB::transaction(function () use ($company) {
            $company->update([
                'is_active' => true,
            ]);

            return $company->fresh();
        });
    }

    /**
     * Get company statistics.
     *
     * @param Company $company
     * @return array<string, mixed>
     */
    public function getStatistics(Company $company): array
    {
        return [
            'total_projects' => $company->projects()->count(),
            'active_projects' => $company->projects()->whereIn('status', ['planning', 'in_progress'])->count(),
            'completed_projects' => $company->projects()->where('status', 'completed')->count(),
            'total_users' => $company->users()->count(),
            'active_users' => $company->users()->where('is_active', true)->count(),
            'total_invoices' => $company->invoices()->count(),
            'paid_invoices' => $company->invoices()->where('status', 'paid')->count(),
            'total_revenue' => $company->invoices()->where('status', 'paid')->sum('total'),
            'outstanding_balance' => $company->invoices()->whereIn('status', ['sent', 'overdue', 'partial'])->sum('balance'),
        ];
    }

    /**
     * Get all active companies.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveCompanies(): \Illuminate\Database\Eloquent\Collection
    {
        return Company::where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
