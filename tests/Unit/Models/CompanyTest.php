<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Company;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Company Model Unit Tests
 *
 * Tests the Company model including:
 * - Relationships
 * - Soft deletes
 * - Casting
 * - Validation
 */
class CompanyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test company has many users
     */
    public function test_has_many_users(): void
    {
        $company = Company::factory()->create();

        User::factory()->count(5)->create([
            'company_id' => $company->id,
        ]);

        $this->assertCount(5, $company->users);
        $this->assertInstanceOf(User::class, $company->users->first());
    }

    /**
     * Test company has many projects
     */
    public function test_has_many_projects(): void
    {
        $company = Company::factory()->create();

        Project::factory()->count(3)->create([
            'company_id' => $company->id,
        ]);

        $this->assertCount(3, $company->projects);
        $this->assertInstanceOf(Project::class, $company->projects->first());
    }

    /**
     * Test company has many invoices
     */
    public function test_has_many_invoices(): void
    {
        $company = Company::factory()->create();

        Invoice::factory()->count(4)->create([
            'company_id' => $company->id,
        ]);

        $this->assertCount(4, $company->invoices);
        $this->assertInstanceOf(Invoice::class, $company->invoices->first());
    }

    /**
     * Test company has many payments
     */
    public function test_has_many_payments(): void
    {
        $company = Company::factory()->create();

        Payment::factory()->count(2)->create([
            'company_id' => $company->id,
        ]);

        $this->assertCount(2, $company->payments);
        $this->assertInstanceOf(Payment::class, $company->payments->first());
    }

    /**
     * Test company has many documents through morphMany
     */
    public function test_has_many_documents(): void
    {
        $company = Company::factory()->create();

        Document::factory()->count(3)->create([
            'documentable_type' => Company::class,
            'documentable_id' => $company->id,
        ]);

        $this->assertCount(3, $company->documents);
        $this->assertInstanceOf(Document::class, $company->documents->first());
    }

    /**
     * Test company name is required
     */
    public function test_company_name_is_required(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Company::create([
            'email' => 'test@example.com',
        ]);
    }

    /**
     * Test company can be created with all fields
     */
    public function test_company_creation_with_all_fields(): void
    {
        $company = Company::create([
            'name' => 'Test Company LLC',
            'email' => 'contact@testcompany.com',
            'phone' => '+1234567890',
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'zip' => '10001',
            'country' => 'USA',
            'website' => 'https://testcompany.com',
            'tax_id' => '12-3456789',
            'is_active' => true,
            'notes' => 'Important client',
        ]);

        $this->assertEquals('Test Company LLC', $company->name);
        $this->assertEquals('contact@testcompany.com', $company->email);
        $this->assertEquals('+1234567890', $company->phone);
        $this->assertTrue($company->is_active);
    }

    /**
     * Test is_active is boolean
     */
    public function test_is_active_is_boolean(): void
    {
        $company = Company::factory()->create(['is_active' => true]);

        $this->assertIsBool($company->is_active);
        $this->assertTrue($company->is_active);
    }

    /**
     * Test inactive company
     */
    public function test_inactive_company(): void
    {
        $company = Company::factory()->create(['is_active' => false]);

        $this->assertFalse($company->is_active);
    }

    /**
     * Test company soft deletes
     */
    public function test_uses_soft_deletes(): void
    {
        $company = Company::factory()->create();
        $companyId = $company->id;

        $company->delete();

        // Should still exist in database but with deleted_at timestamp
        $this->assertSoftDeleted('companies', ['id' => $companyId]);

        // Can be restored
        $company->restore();
        $this->assertDatabaseHas('companies', [
            'id' => $companyId,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test fillable attributes
     */
    public function test_fillable_attributes(): void
    {
        $data = [
            'name' => 'Fillable Test Company',
            'email' => 'fillable@test.com',
            'phone' => '+1234567890',
            'address' => '123 Test St',
            'city' => 'Test City',
            'state' => 'TS',
            'zip' => '12345',
            'country' => 'USA',
            'website' => 'https://test.com',
            'tax_id' => '99-9999999',
            'is_active' => true,
            'notes' => 'Test notes',
        ];

        $company = Company::create($data);

        $this->assertEquals('Fillable Test Company', $company->name);
        $this->assertEquals('fillable@test.com', $company->email);
        $this->assertTrue($company->is_active);
    }

    /**
     * Test website URL format
     */
    public function test_website_url_stored_correctly(): void
    {
        $company = Company::factory()->create([
            'website' => 'https://example.com',
        ]);

        $this->assertEquals('https://example.com', $company->website);
    }

    /**
     * Test tax ID storage
     */
    public function test_tax_id_stored_correctly(): void
    {
        $company = Company::factory()->create([
            'tax_id' => '12-3456789',
        ]);

        $this->assertEquals('12-3456789', $company->tax_id);
    }

    /**
     * Test address fields
     */
    public function test_address_fields_stored_correctly(): void
    {
        $company = Company::factory()->create([
            'address' => '123 Main Street',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'zip' => '90001',
            'country' => 'USA',
        ]);

        $this->assertEquals('123 Main Street', $company->address);
        $this->assertEquals('Los Angeles', $company->city);
        $this->assertEquals('CA', $company->state);
        $this->assertEquals('90001', $company->zip);
        $this->assertEquals('USA', $company->country);
    }

    /**
     * Test notes field
     */
    public function test_notes_can_store_long_text(): void
    {
        $longNotes = str_repeat('This is a long note. ', 50);

        $company = Company::factory()->create([
            'notes' => $longNotes,
        ]);

        $this->assertEquals($longNotes, $company->notes);
    }

    /**
     * Test timestamps are recorded
     */
    public function test_timestamps_are_recorded(): void
    {
        $company = Company::factory()->create();

        $this->assertNotNull($company->created_at);
        $this->assertNotNull($company->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $company->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $company->updated_at);
    }

    /**
     * Test email can be nullable
     */
    public function test_email_can_be_nullable(): void
    {
        $company = Company::factory()->create(['email' => null]);

        $this->assertNull($company->email);
    }

    /**
     * Test phone can be nullable
     */
    public function test_phone_can_be_nullable(): void
    {
        $company = Company::factory()->create(['phone' => null]);

        $this->assertNull($company->phone);
    }

    /**
     * Test company with no users
     */
    public function test_company_can_have_no_users(): void
    {
        $company = Company::factory()->create();

        $this->assertCount(0, $company->users);
    }

    /**
     * Test company with no projects
     */
    public function test_company_can_have_no_projects(): void
    {
        $company = Company::factory()->create();

        $this->assertCount(0, $company->projects);
    }

    /**
     * Test company default is_active value
     */
    public function test_is_active_defaults_to_true(): void
    {
        $company = Company::factory()->create();

        $this->assertTrue($company->is_active);
    }
}
