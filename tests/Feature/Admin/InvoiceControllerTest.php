<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\InvoiceStatus;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Invoice Controller Feature Tests
 *
 * Tests the admin invoice controller CRUD operations including:
 * - Listing invoices
 * - Creating invoices with items
 * - Viewing invoice details
 * - Updating invoices
 * - Marking invoices as paid
 * - Sending invoices
 * - Authorization checks
 */
class InvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $client;
    private Company $company;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->client = User::factory()->create(['role' => 'client', 'is_active' => true]);
        $this->company = Company::factory()->create();
        $this->project = Project::factory()->create(['company_id' => $this->company->id]);
    }

    /**
     * Test admin can view invoices index
     */
    public function test_admin_can_view_invoices_index(): void
    {
        Invoice::factory()->count(3)->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.invoices.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.invoices.index');
        $response->assertViewHas('invoices');
    }

    /**
     * Test client cannot access admin invoices index
     */
    public function test_client_cannot_access_admin_invoices_index(): void
    {
        $response = $this->actingAs($this->client)->get(route('admin.invoices.index'));

        $response->assertStatus(403);
    }

    /**
     * Test guest cannot access admin invoices index
     */
    public function test_guest_cannot_access_admin_invoices_index(): void
    {
        $response = $this->get(route('admin.invoices.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test admin can view create invoice form
     */
    public function test_admin_can_view_create_invoice_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.invoices.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.invoices.create');
        $response->assertSee('Create Invoice');
    }

    /**
     * Test admin can create invoice
     */
    public function test_admin_can_create_invoice(): void
    {
        $invoiceData = [
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'tax_rate' => 10.0,
            'discount_amount' => 50.0,
            'currency' => 'USD',
            'notes' => 'Test invoice',
            'terms' => 'Net 30',
            'items' => [
                [
                    'description' => 'Web Development',
                    'quantity' => 10.0,
                    'unit_price' => 100.0,
                    'tax_rate' => 0.0,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.invoices.store'), $invoiceData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('invoices', [
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
        ]);
    }

    /**
     * Test create invoice validation requires company
     */
    public function test_create_invoice_requires_company(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.invoices.store'), [
                'issue_date' => now()->format('Y-m-d'),
            ]);

        $response->assertSessionHasErrors('company_id');
    }

    /**
     * Test create invoice validation requires issue date
     */
    public function test_create_invoice_requires_issue_date(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.invoices.store'), [
                'company_id' => $this->company->id,
            ]);

        $response->assertSessionHasErrors('issue_date');
    }

    /**
     * Test admin can view invoice details
     */
    public function test_admin_can_view_invoice_details(): void
    {
        $invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'invoice_number' => 'INV-TEST-001',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.invoices.show', $invoice));

        $response->assertStatus(200);
        $response->assertViewIs('admin.invoices.show');
        $response->assertViewHas('invoice');
        $response->assertSee('INV-TEST-001');
    }

    /**
     * Test admin can view edit invoice form
     */
    public function test_admin_can_view_edit_invoice_form(): void
    {
        $invoice = Invoice::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.invoices.edit', $invoice));

        $response->assertStatus(200);
        $response->assertViewIs('admin.invoices.edit');
        $response->assertViewHas('invoice');
    }

    /**
     * Test admin can update invoice
     */
    public function test_admin_can_update_invoice(): void
    {
        $invoice = Invoice::factory()->create([
            'notes' => 'Original notes',
        ]);

        $updateData = [
            'notes' => 'Updated notes',
            'terms' => 'Updated terms',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.invoices.update', $invoice), $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'notes' => 'Updated notes',
        ]);
    }

    /**
     * Test admin can mark invoice as paid
     */
    public function test_admin_can_mark_invoice_as_paid(): void
    {
        $invoice = Invoice::factory()->create([
            'status' => InvoiceStatus::Sent,
            'total_amount' => 1000.00,
            'paid_amount' => 0.00,
            'balance' => 1000.00,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.invoices.mark-paid', $invoice));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $invoice->refresh();
        $this->assertEquals(InvoiceStatus::Paid, $invoice->status);
        $this->assertEquals($invoice->total_amount, $invoice->paid_amount);
        $this->assertEquals(0.00, $invoice->balance);
    }

    /**
     * Test admin can send invoice
     */
    public function test_admin_can_send_invoice(): void
    {
        $invoice = Invoice::factory()->create([
            'status' => InvoiceStatus::Draft,
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.invoices.send', $invoice));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $invoice->refresh();
        $this->assertEquals(InvoiceStatus::Sent, $invoice->status);
        $this->assertNotNull($invoice->sent_at);
    }

    /**
     * Test admin can delete invoice
     */
    public function test_admin_can_delete_invoice(): void
    {
        $invoice = Invoice::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.invoices.destroy', $invoice));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('invoices', [
            'id' => $invoice->id,
        ]);
    }

    /**
     * Test invoice index has search functionality
     */
    public function test_invoices_index_can_search(): void
    {
        Invoice::factory()->create([
            'invoice_number' => 'INV-SEARCH-001',
            'company_id' => $this->company->id,
        ]);

        Invoice::factory()->create([
            'invoice_number' => 'INV-OTHER-002',
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.invoices.index', ['search' => 'SEARCH']));

        $response->assertStatus(200);
        $response->assertSee('INV-SEARCH-001');
        $response->assertDontSee('INV-OTHER-002');
    }

    /**
     * Test invoice index can filter by status
     */
    public function test_invoices_index_can_filter_by_status(): void
    {
        Invoice::factory()->create([
            'invoice_number' => 'INV-DRAFT-001',
            'status' => InvoiceStatus::Draft,
        ]);

        Invoice::factory()->create([
            'invoice_number' => 'INV-PAID-002',
            'status' => InvoiceStatus::Paid,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.invoices.index', ['status' => InvoiceStatus::Draft->value]));

        $response->assertStatus(200);
        $response->assertSee('INV-DRAFT-001');
        $response->assertDontSee('INV-PAID-002');
    }

    /**
     * Test invoice index can filter by company
     */
    public function test_invoices_index_can_filter_by_company(): void
    {
        $company2 = Company::factory()->create();

        Invoice::factory()->create([
            'invoice_number' => 'INV-COMPANY1',
            'company_id' => $this->company->id,
        ]);

        Invoice::factory()->create([
            'invoice_number' => 'INV-COMPANY2',
            'company_id' => $company2->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.invoices.index', ['company' => $this->company->id]));

        $response->assertStatus(200);
        $response->assertSee('INV-COMPANY1');
        $response->assertDontSee('INV-COMPANY2');
    }

    /**
     * Test invoice index is paginated
     */
    public function test_invoices_index_is_paginated(): void
    {
        Invoice::factory()->count(20)->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.invoices.index'));

        $response->assertStatus(200);
        $response->assertViewHas('invoices');

        $invoices = $response->viewData('invoices');
        $this->assertLessThanOrEqual(15, $invoices->count());
    }

    /**
     * Test tax rate validation
     */
    public function test_invoice_tax_rate_must_be_numeric(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.invoices.store'), [
                'company_id' => $this->company->id,
                'issue_date' => now()->format('Y-m-d'),
                'tax_rate' => 'not-a-number',
            ]);

        $response->assertSessionHasErrors('tax_rate');
    }

    /**
     * Test discount amount validation
     */
    public function test_invoice_discount_must_be_numeric(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.invoices.store'), [
                'company_id' => $this->company->id,
                'issue_date' => now()->format('Y-m-d'),
                'discount_amount' => 'invalid',
            ]);

        $response->assertSessionHasErrors('discount_amount');
    }
}
