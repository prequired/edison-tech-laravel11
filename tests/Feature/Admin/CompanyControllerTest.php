<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Company Controller Feature Tests
 *
 * Tests the admin company controller CRUD operations including:
 * - Listing companies
 * - Creating companies
 * - Viewing company details
 * - Updating companies
 * - Deleting companies
 * - Authorization checks
 * - Search and filtering
 */
class CompanyControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->client = User::factory()->create(['role' => 'client', 'is_active' => true]);
    }

    /**
     * Test admin can view companies index
     */
    public function test_admin_can_view_companies_index(): void
    {
        Company::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.companies.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.companies.index');
        $response->assertViewHas('companies');
    }

    /**
     * Test client cannot access admin companies index
     */
    public function test_client_cannot_access_admin_companies_index(): void
    {
        $response = $this->actingAs($this->client)->get(route('admin.companies.index'));

        $response->assertStatus(403);
    }

    /**
     * Test guest cannot access admin companies index
     */
    public function test_guest_cannot_access_admin_companies_index(): void
    {
        $response = $this->get(route('admin.companies.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test admin can view create company form
     */
    public function test_admin_can_view_create_company_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.companies.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.companies.create');
        $response->assertSee('Create Company');
    }

    /**
     * Test admin can create company
     */
    public function test_admin_can_create_company(): void
    {
        $companyData = [
            'name' => 'New Test Company',
            'email' => 'test@newcompany.com',
            'phone' => '+1234567890',
            'address' => '123 Test St',
            'city' => 'New York',
            'state' => 'NY',
            'zip' => '10001',
            'country' => 'USA',
            'website' => 'https://newcompany.com',
            'tax_id' => '12-3456789',
            'is_active' => true,
            'notes' => 'New client',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.companies.store'), $companyData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('companies', [
            'name' => 'New Test Company',
            'email' => 'test@newcompany.com',
        ]);
    }

    /**
     * Test create company validation requires name
     */
    public function test_create_company_requires_name(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.companies.store'), [
                'email' => 'test@example.com',
            ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test admin can view company details
     */
    public function test_admin_can_view_company_details(): void
    {
        $company = Company::factory()->create([
            'name' => 'Detail Test Company',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.companies.show', $company));

        $response->assertStatus(200);
        $response->assertViewIs('admin.companies.show');
        $response->assertViewHas('company');
        $response->assertSee('Detail Test Company');
    }

    /**
     * Test admin can view edit company form
     */
    public function test_admin_can_view_edit_company_form(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.companies.edit', $company));

        $response->assertStatus(200);
        $response->assertViewIs('admin.companies.edit');
        $response->assertViewHas('company');
    }

    /**
     * Test admin can update company
     */
    public function test_admin_can_update_company(): void
    {
        $company = Company::factory()->create([
            'name' => 'Original Name',
        ]);

        $updateData = [
            'name' => 'Updated Company Name',
            'email' => 'updated@company.com',
            'phone' => '+0987654321',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.companies.update', $company), $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Updated Company Name',
            'email' => 'updated@company.com',
        ]);
    }

    /**
     * Test admin can delete company
     */
    public function test_admin_can_delete_company(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.companies.destroy', $company));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('companies', [
            'id' => $company->id,
        ]);
    }

    /**
     * Test companies index has search functionality
     */
    public function test_companies_index_can_search(): void
    {
        Company::factory()->create([
            'name' => 'Acme Corporation',
        ]);

        Company::factory()->create([
            'name' => 'Beta Industries',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.companies.index', ['search' => 'Acme']));

        $response->assertStatus(200);
        $response->assertSee('Acme Corporation');
        $response->assertDontSee('Beta Industries');
    }

    /**
     * Test companies index can filter by status
     */
    public function test_companies_index_can_filter_by_status(): void
    {
        Company::factory()->create([
            'name' => 'Active Company',
            'is_active' => true,
        ]);

        Company::factory()->create([
            'name' => 'Inactive Company',
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.companies.index', ['is_active' => '1']));

        $response->assertStatus(200);
        $response->assertSee('Active Company');
        $response->assertDontSee('Inactive Company');
    }

    /**
     * Test companies index is paginated
     */
    public function test_companies_index_is_paginated(): void
    {
        Company::factory()->count(20)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.companies.index'));

        $response->assertStatus(200);
        $response->assertViewHas('companies');

        $companies = $response->viewData('companies');
        $this->assertLessThanOrEqual(15, $companies->count());
    }

    /**
     * Test email validation
     */
    public function test_company_email_must_be_valid(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.companies.store'), [
                'name' => 'Test Company',
                'email' => 'invalid-email',
            ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test website URL validation
     */
    public function test_company_website_must_be_valid_url(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.companies.store'), [
                'name' => 'Test Company',
                'website' => 'not-a-url',
            ]);

        $response->assertSessionHasErrors('website');
    }

    /**
     * Test company can be created as inactive
     */
    public function test_company_can_be_created_as_inactive(): void
    {
        $companyData = [
            'name' => 'Inactive Company',
            'email' => 'inactive@company.com',
            'is_active' => false,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.companies.store'), $companyData);

        $response->assertRedirect();

        $this->assertDatabaseHas('companies', [
            'name' => 'Inactive Company',
            'is_active' => false,
        ]);
    }

    /**
     * Test company activation
     */
    public function test_admin_can_activate_company(): void
    {
        $company = Company::factory()->create(['is_active' => false]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.companies.activate', $company));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $company->refresh();
        $this->assertTrue($company->is_active);
    }

    /**
     * Test company deactivation
     */
    public function test_admin_can_deactivate_company(): void
    {
        $company = Company::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.companies.deactivate', $company));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $company->refresh();
        $this->assertFalse($company->is_active);
    }

    /**
     * Test company details show related users
     */
    public function test_company_details_show_related_users(): void
    {
        $company = Company::factory()->create();

        User::factory()->count(3)->create([
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.companies.show', $company));

        $response->assertStatus(200);
        $response->assertViewHas('company');

        $viewCompany = $response->viewData('company');
        $this->assertCount(3, $viewCompany->users);
    }

    /**
     * Test optional fields can be null
     */
    public function test_optional_fields_can_be_null(): void
    {
        $companyData = [
            'name' => 'Minimal Company',
            'email' => null,
            'phone' => null,
            'address' => null,
            'city' => null,
            'state' => null,
            'zip' => null,
            'country' => null,
            'website' => null,
            'tax_id' => null,
            'notes' => null,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.companies.store'), $companyData);

        $response->assertRedirect();

        $this->assertDatabaseHas('companies', [
            'name' => 'Minimal Company',
        ]);
    }
}
