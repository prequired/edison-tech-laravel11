<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Company;
use App\Models\Contract;
use App\Models\Document;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Contract Model Unit Tests
 *
 * Tests the Contract model including:
 * - Factory creation
 * - Relationships (company, project, documents)
 * - Status handling
 * - Contract types
 * - Value and deposit management
 * - Signing workflow
 * - Date management
 * - Soft deletes
 */
class ContractTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test contract can be created using factory
     */
    public function test_contract_can_be_created_using_factory(): void
    {
        $contract = Contract::factory()->create();

        $this->assertInstanceOf(Contract::class, $contract);
        $this->assertDatabaseHas('contracts', [
            'id' => $contract->id,
        ]);
    }

    /**
     * Test contract belongs to company
     */
    public function test_contract_belongs_to_company(): void
    {
        $company = Company::factory()->create();
        $contract = Contract::factory()->create(['company_id' => $company->id]);

        $this->assertInstanceOf(Company::class, $contract->company);
        $this->assertEquals($company->id, $contract->company->id);
    }

    /**
     * Test contract belongs to project
     */
    public function test_contract_belongs_to_project(): void
    {
        $project = Project::factory()->create();
        $contract = Contract::factory()->create([
            'company_id' => $project->company_id,
            'project_id' => $project->id,
        ]);

        $this->assertInstanceOf(Project::class, $contract->project);
        $this->assertEquals($project->id, $contract->project->id);
    }

    /**
     * Test contract can be without project
     */
    public function test_contract_can_be_without_project(): void
    {
        $contract = Contract::factory()->create(['project_id' => null]);

        $this->assertNull($contract->project_id);
        $this->assertNull($contract->project);
    }

    /**
     * Test contract has unique contract number
     */
    public function test_contract_has_unique_contract_number(): void
    {
        $contract1 = Contract::factory()->create();
        $contract2 = Contract::factory()->create();

        $this->assertNotEquals($contract1->contract_number, $contract2->contract_number);
    }

    /**
     * Test contract has title
     */
    public function test_contract_has_title(): void
    {
        $contract = Contract::factory()->create([
            'title' => 'Website Development Contract',
        ]);

        $this->assertEquals('Website Development Contract', $contract->title);
    }

    /**
     * Test contract can have description
     */
    public function test_contract_can_have_description(): void
    {
        $contract = Contract::factory()->create([
            'description' => 'Full website redesign and development',
        ]);

        $this->assertEquals('Full website redesign and development', $contract->description);
    }

    /**
     * Test contract description can be null
     */
    public function test_contract_description_can_be_null(): void
    {
        $contract = Contract::factory()->create(['description' => null]);

        $this->assertNull($contract->description);
    }

    /**
     * Test different contract statuses
     */
    public function test_different_contract_statuses(): void
    {
        $statuses = ['draft', 'pending_signature', 'active', 'completed', 'cancelled', 'expired'];

        foreach ($statuses as $status) {
            $contract = Contract::factory()->create(['status' => $status]);
            $this->assertEquals($status, $contract->status);
        }
    }

    /**
     * Test different contract types
     */
    public function test_different_contract_types(): void
    {
        $types = ['fixed_price', 'hourly', 'retainer', 'milestone'];

        foreach ($types as $type) {
            $contract = Contract::factory()->create(['type' => $type]);
            $this->assertEquals($type, $contract->type);
        }
    }

    /**
     * Test contract value is cast to decimal
     */
    public function test_contract_value_is_cast_to_decimal(): void
    {
        $contract = Contract::factory()->create(['value' => 50000.00]);

        $this->assertEquals('50000.00', $contract->value);
        $this->assertIsString($contract->value);
    }

    /**
     * Test contract value precision
     */
    public function test_contract_value_precision(): void
    {
        $contract = Contract::factory()->create(['value' => 12345.678]);

        // Should be rounded to 2 decimal places
        $this->assertEquals('12345.68', $contract->value);
    }

    /**
     * Test deposit amount is cast to decimal
     */
    public function test_deposit_amount_is_cast_to_decimal(): void
    {
        $contract = Contract::factory()->create([
            'value' => 50000.00,
            'deposit_amount' => 10000.00,
        ]);

        $this->assertEquals('10000.00', $contract->deposit_amount);
        $this->assertIsString($contract->deposit_amount);
    }

    /**
     * Test deposit amount can be null
     */
    public function test_deposit_amount_can_be_null(): void
    {
        $contract = Contract::factory()->create(['deposit_amount' => null]);

        $this->assertNull($contract->deposit_amount);
    }

    /**
     * Test deposit paid flag is cast to boolean
     */
    public function test_deposit_paid_flag_is_cast_to_boolean(): void
    {
        $paidContract = Contract::factory()->create(['deposit_paid' => true]);
        $unpaidContract = Contract::factory()->create(['deposit_paid' => false]);

        $this->assertIsBool($paidContract->deposit_paid);
        $this->assertTrue($paidContract->deposit_paid);
        $this->assertFalse($unpaidContract->deposit_paid);
    }

    /**
     * Test start date is cast to date
     */
    public function test_start_date_is_cast_to_date(): void
    {
        $contract = Contract::factory()->create([
            'start_date' => '2025-02-01',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $contract->start_date);
        $this->assertEquals('2025-02-01', $contract->start_date->format('Y-m-d'));
    }

    /**
     * Test end date is cast to date
     */
    public function test_end_date_is_cast_to_date(): void
    {
        $contract = Contract::factory()->create([
            'start_date' => '2025-02-01',
            'end_date' => '2025-08-01',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $contract->end_date);
        $this->assertEquals('2025-08-01', $contract->end_date->format('Y-m-d'));
    }

    /**
     * Test end date can be null
     */
    public function test_end_date_can_be_null(): void
    {
        $contract = Contract::factory()->create(['end_date' => null]);

        $this->assertNull($contract->end_date);
    }

    /**
     * Test signed at is cast to date
     */
    public function test_signed_at_is_cast_to_date(): void
    {
        $contract = Contract::factory()->create([
            'signed_at' => '2025-01-15',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $contract->signed_at);
        $this->assertEquals('2025-01-15', $contract->signed_at->format('Y-m-d'));
    }

    /**
     * Test signed at can be null
     */
    public function test_signed_at_can_be_null(): void
    {
        $contract = Contract::factory()->create(['signed_at' => null]);

        $this->assertNull($contract->signed_at);
    }

    /**
     * Test contract has documents relationship
     */
    public function test_contract_has_documents_relationship(): void
    {
        $contract = Contract::factory()->create();

        Document::factory()->count(3)->create([
            'documentable_type' => Contract::class,
            'documentable_id' => $contract->id,
        ]);

        $this->assertCount(3, $contract->documents);
        $this->assertInstanceOf(Document::class, $contract->documents->first());
    }

    /**
     * Test contract can have signed document path
     */
    public function test_contract_can_have_signed_document_path(): void
    {
        $contract = Contract::factory()->create([
            'signed_document' => 'contracts/signed/contract-123-signed.pdf',
        ]);

        $this->assertEquals('contracts/signed/contract-123-signed.pdf', $contract->signed_document);
    }

    /**
     * Test contract can store signer information
     */
    public function test_contract_can_store_signer_information(): void
    {
        $contract = Contract::factory()->create([
            'signed_by_name' => 'John Doe',
            'signed_by_email' => 'john@example.com',
            'signed_ip_address' => '192.168.1.100',
            'signed_at' => now(),
        ]);

        $this->assertEquals('John Doe', $contract->signed_by_name);
        $this->assertEquals('john@example.com', $contract->signed_by_email);
        $this->assertEquals('192.168.1.100', $contract->signed_ip_address);
        $this->assertNotNull($contract->signed_at);
    }

    /**
     * Test contract can have terms
     */
    public function test_contract_can_have_terms(): void
    {
        $terms = 'Payment terms: Net 30. Cancellation requires 30 days notice.';
        $contract = Contract::factory()->create(['terms' => $terms]);

        $this->assertEquals($terms, $contract->terms);
    }

    /**
     * Test contract terms can be null
     */
    public function test_contract_terms_can_be_null(): void
    {
        $contract = Contract::factory()->create(['terms' => null]);

        $this->assertNull($contract->terms);
    }

    /**
     * Test contract can have notes
     */
    public function test_contract_can_have_notes(): void
    {
        $notes = 'Client requested expedited delivery';
        $contract = Contract::factory()->create(['notes' => $notes]);

        $this->assertEquals($notes, $contract->notes);
    }

    /**
     * Test contract notes can be null
     */
    public function test_contract_notes_can_be_null(): void
    {
        $contract = Contract::factory()->create(['notes' => null]);

        $this->assertNull($contract->notes);
    }

    /**
     * Test contract uses soft deletes
     */
    public function test_contract_uses_soft_deletes(): void
    {
        $contract = Contract::factory()->create();

        $contract->delete();

        $this->assertSoftDeleted('contracts', [
            'id' => $contract->id,
        ]);

        $this->assertNotNull($contract->fresh()->deleted_at);
    }

    /**
     * Test soft deleted contracts can be restored
     */
    public function test_soft_deleted_contracts_can_be_restored(): void
    {
        $contract = Contract::factory()->create();
        $contract->delete();

        $contract->restore();

        $this->assertNull($contract->fresh()->deleted_at);
        $this->assertDatabaseHas('contracts', [
            'id' => $contract->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test contract has timestamps
     */
    public function test_contract_has_timestamps(): void
    {
        $contract = Contract::factory()->create();

        $this->assertNotNull($contract->created_at);
        $this->assertNotNull($contract->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $contract->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $contract->updated_at);
    }

    /**
     * Test contract mass assignment
     */
    public function test_contract_mass_assignment(): void
    {
        $company = Company::factory()->create();
        $project = Project::factory()->create(['company_id' => $company->id]);

        $contract = Contract::create([
            'company_id' => $company->id,
            'project_id' => $project->id,
            'contract_number' => 'CNT-2025-001',
            'title' => 'Website Development',
            'description' => 'Complete website redesign',
            'status' => 'active',
            'type' => 'fixed_price',
            'value' => 50000.00,
            'deposit_amount' => 10000.00,
            'deposit_paid' => true,
            'start_date' => '2025-02-01',
            'end_date' => '2025-08-01',
        ]);

        $this->assertEquals('Website Development', $contract->title);
        $this->assertEquals('fixed_price', $contract->type);
        $this->assertEquals('50000.00', $contract->value);
    }

    /**
     * Test contract status transitions
     */
    public function test_contract_status_transitions(): void
    {
        $contract = Contract::factory()->create(['status' => 'draft']);

        // Draft -> Pending Signature
        $contract->update(['status' => 'pending_signature']);
        $this->assertEquals('pending_signature', $contract->fresh()->status);

        // Pending Signature -> Active
        $contract->update([
            'status' => 'active',
            'signed_at' => now(),
        ]);
        $this->assertEquals('active', $contract->fresh()->status);
        $this->assertNotNull($contract->signed_at);

        // Active -> Completed
        $contract->update(['status' => 'completed']);
        $this->assertEquals('completed', $contract->fresh()->status);
    }

    /**
     * Test contract with deposit workflow
     */
    public function test_contract_with_deposit_workflow(): void
    {
        $contract = Contract::factory()->create([
            'value' => 50000.00,
            'deposit_amount' => 10000.00,
            'deposit_paid' => false,
        ]);

        $this->assertEquals('10000.00', $contract->deposit_amount);
        $this->assertFalse($contract->deposit_paid);

        // Mark deposit as paid
        $contract->update(['deposit_paid' => true]);

        $this->assertTrue($contract->fresh()->deposit_paid);
    }

    /**
     * Test contract filtering by status
     */
    public function test_contract_filtering_by_status(): void
    {
        Contract::factory()->count(3)->create(['status' => 'active']);
        Contract::factory()->count(2)->create(['status' => 'completed']);
        Contract::factory()->count(1)->create(['status' => 'draft']);

        $activeContracts = Contract::where('status', 'active')->get();
        $completedContracts = Contract::where('status', 'completed')->get();

        $this->assertCount(3, $activeContracts);
        $this->assertCount(2, $completedContracts);
    }

    /**
     * Test contract filtering by type
     */
    public function test_contract_filtering_by_type(): void
    {
        Contract::factory()->count(2)->create(['type' => 'fixed_price']);
        Contract::factory()->count(3)->create(['type' => 'hourly']);
        Contract::factory()->count(1)->create(['type' => 'retainer']);

        $fixedPriceContracts = Contract::where('type', 'fixed_price')->get();
        $hourlyContracts = Contract::where('type', 'hourly')->get();

        $this->assertCount(2, $fixedPriceContracts);
        $this->assertCount(3, $hourlyContracts);
    }

    /**
     * Test contract total value calculation
     */
    public function test_contract_total_value_calculation(): void
    {
        Contract::factory()->create(['value' => 50000.00]);
        Contract::factory()->create(['value' => 30000.00]);
        Contract::factory()->create(['value' => 20000.00]);

        $totalValue = Contract::sum('value');

        $this->assertEquals(100000.00, $totalValue);
    }

    /**
     * Test unsigned contracts
     */
    public function test_unsigned_contracts(): void
    {
        Contract::factory()->count(3)->create(['signed_at' => null]);
        Contract::factory()->count(2)->create(['signed_at' => now()]);

        $unsignedContracts = Contract::whereNull('signed_at')->get();

        $this->assertCount(3, $unsignedContracts);
    }

    /**
     * Test expired contracts
     */
    public function test_expired_contracts(): void
    {
        Contract::factory()->create([
            'end_date' => now()->subMonths(2),
            'status' => 'active',
        ]);

        Contract::factory()->create([
            'end_date' => now()->addMonths(2),
            'status' => 'active',
        ]);

        $expiredContracts = Contract::where('end_date', '<', now())
            ->where('status', 'active')
            ->get();

        $this->assertCount(1, $expiredContracts);
    }

    /**
     * Test contract duration calculation
     */
    public function test_contract_duration_calculation(): void
    {
        $startDate = now();
        $endDate = now()->addMonths(6);

        $contract = Contract::factory()->create([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $durationInMonths = $contract->start_date->diffInMonths($contract->end_date);

        $this->assertEquals(6, $durationInMonths);
    }

    /**
     * Test contracts for specific company
     */
    public function test_contracts_for_specific_company(): void
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        Contract::factory()->count(4)->create(['company_id' => $company1->id]);
        Contract::factory()->count(2)->create(['company_id' => $company2->id]);

        $company1Contracts = Contract::where('company_id', $company1->id)->get();

        $this->assertCount(4, $company1Contracts);
    }
}
