<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Enums\InvoiceStatus;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Invoice Model Unit Tests
 *
 * Tests the Invoice model including:
 * - Relationships
 * - Calculations
 * - Status enum casting
 * - Soft deletes
 */
class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->project = Project::factory()->create([
            'company_id' => $this->company->id,
        ]);
    }

    /**
     * Test invoice belongs to company
     */
    public function test_belongs_to_company(): void
    {
        $invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $this->assertInstanceOf(Company::class, $invoice->company);
        $this->assertEquals($this->company->id, $invoice->company->id);
    }

    /**
     * Test invoice belongs to project
     */
    public function test_belongs_to_project(): void
    {
        $invoice = Invoice::factory()->create([
            'project_id' => $this->project->id,
        ]);

        $this->assertInstanceOf(Project::class, $invoice->project);
        $this->assertEquals($this->project->id, $invoice->project->id);
    }

    /**
     * Test invoice has many items
     */
    public function test_has_many_items(): void
    {
        $invoice = Invoice::factory()->create();

        InvoiceItem::factory()->count(3)->create([
            'invoice_id' => $invoice->id,
        ]);

        $this->assertCount(3, $invoice->items);
        $this->assertInstanceOf(InvoiceItem::class, $invoice->items->first());
    }

    /**
     * Test invoice has many payments
     */
    public function test_has_many_payments(): void
    {
        $invoice = Invoice::factory()->create();

        Payment::factory()->count(2)->create([
            'invoice_id' => $invoice->id,
            'company_id' => $invoice->company_id,
        ]);

        $this->assertCount(2, $invoice->payments);
        $this->assertInstanceOf(Payment::class, $invoice->payments->first());
    }

    /**
     * Test status is cast to enum
     */
    public function test_status_cast_to_enum(): void
    {
        $invoice = Invoice::factory()->create([
            'status' => InvoiceStatus::Draft,
        ]);

        $this->assertInstanceOf(InvoiceStatus::class, $invoice->status);
        $this->assertEquals(InvoiceStatus::Draft, $invoice->status);
    }

    /**
     * Test different invoice statuses
     */
    public function test_different_invoice_statuses(): void
    {
        $statuses = [
            InvoiceStatus::Draft,
            InvoiceStatus::Sent,
            InvoiceStatus::Paid,
            InvoiceStatus::Partial,
            InvoiceStatus::Overdue,
            InvoiceStatus::Cancelled,
        ];

        foreach ($statuses as $status) {
            $invoice = Invoice::factory()->create(['status' => $status]);
            $this->assertEquals($status, $invoice->status);
        }
    }

    /**
     * Test dates are cast correctly
     */
    public function test_dates_cast_correctly(): void
    {
        $issueDate = now();
        $dueDate = now()->addDays(30);
        $sentAt = now();
        $paidAt = now()->addDays(15);

        $invoice = Invoice::factory()->create([
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'sent_at' => $sentAt,
            'paid_at' => $paidAt,
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $invoice->issue_date);
        $this->assertInstanceOf(\Carbon\Carbon::class, $invoice->due_date);
        $this->assertInstanceOf(\Carbon\Carbon::class, $invoice->sent_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $invoice->paid_at);
    }

    /**
     * Test decimal fields cast correctly
     */
    public function test_decimal_fields_cast_correctly(): void
    {
        $invoice = Invoice::factory()->create([
            'subtotal' => '1000.50',
            'tax_amount' => '100.05',
            'discount_amount' => '50.25',
            'total_amount' => '1050.30',
            'paid_amount' => '500.00',
            'balance' => '550.30',
        ]);

        $this->assertIsFloat($invoice->subtotal);
        $this->assertEquals(1000.50, $invoice->subtotal);
        $this->assertIsFloat($invoice->tax_amount);
        $this->assertEquals(100.05, $invoice->tax_amount);
        $this->assertIsFloat($invoice->total_amount);
        $this->assertEquals(1050.30, $invoice->total_amount);
    }

    /**
     * Test invoice number is unique
     */
    public function test_invoice_number_is_unique(): void
    {
        Invoice::factory()->create([
            'invoice_number' => 'INV-001',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Invoice::factory()->create([
            'invoice_number' => 'INV-001',
        ]);
    }

    /**
     * Test fillable attributes
     */
    public function test_fillable_attributes(): void
    {
        $data = [
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'invoice_number' => 'INV-TEST-001',
            'status' => InvoiceStatus::Draft,
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'tax_rate' => 10.0,
            'discount_amount' => 50.0,
            'currency' => 'USD',
            'notes' => 'Test notes',
            'terms' => 'Net 30',
            'subtotal' => 1000.00,
            'tax_amount' => 100.00,
            'total_amount' => 1050.00,
            'paid_amount' => 0.00,
            'balance' => 1050.00,
        ];

        $invoice = Invoice::create($data);

        $this->assertEquals('INV-TEST-001', $invoice->invoice_number);
        $this->assertEquals($this->company->id, $invoice->company_id);
        $this->assertEquals($this->project->id, $invoice->project_id);
    }

    /**
     * Test invoice soft deletes
     */
    public function test_uses_soft_deletes(): void
    {
        $invoice = Invoice::factory()->create();
        $invoiceId = $invoice->id;

        $invoice->delete();

        // Should still exist in database but with deleted_at timestamp
        $this->assertSoftDeleted('invoices', ['id' => $invoiceId]);

        // Can be restored
        $invoice->restore();
        $this->assertDatabaseHas('invoices', [
            'id' => $invoiceId,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test currency defaults to USD
     */
    public function test_currency_defaults_to_usd(): void
    {
        $invoice = Invoice::factory()->create(['currency' => 'USD']);

        $this->assertEquals('USD', $invoice->currency);
    }

    /**
     * Test tax rate can be zero
     */
    public function test_tax_rate_can_be_zero(): void
    {
        $invoice = Invoice::factory()->create(['tax_rate' => 0.0]);

        $this->assertEquals(0.0, $invoice->tax_rate);
    }

    /**
     * Test discount can be zero
     */
    public function test_discount_can_be_zero(): void
    {
        $invoice = Invoice::factory()->create(['discount_amount' => 0.0]);

        $this->assertEquals(0.0, $invoice->discount_amount);
    }

    /**
     * Test balance calculation
     */
    public function test_balance_represents_remaining_amount(): void
    {
        $invoice = Invoice::factory()->create([
            'total_amount' => 1000.00,
            'paid_amount' => 400.00,
            'balance' => 600.00,
        ]);

        $this->assertEquals(600.00, $invoice->balance);
        $this->assertEquals($invoice->total_amount - $invoice->paid_amount, $invoice->balance);
    }

    /**
     * Test fully paid invoice
     */
    public function test_fully_paid_invoice(): void
    {
        $invoice = Invoice::factory()->create([
            'total_amount' => 1000.00,
            'paid_amount' => 1000.00,
            'balance' => 0.00,
            'status' => InvoiceStatus::Paid,
        ]);

        $this->assertEquals(0.00, $invoice->balance);
        $this->assertEquals($invoice->total_amount, $invoice->paid_amount);
        $this->assertEquals(InvoiceStatus::Paid, $invoice->status);
    }

    /**
     * Test partially paid invoice
     */
    public function test_partially_paid_invoice(): void
    {
        $invoice = Invoice::factory()->create([
            'total_amount' => 1000.00,
            'paid_amount' => 500.00,
            'balance' => 500.00,
            'status' => InvoiceStatus::Partial,
        ]);

        $this->assertGreaterThan(0, $invoice->paid_amount);
        $this->assertLessThan($invoice->total_amount, $invoice->paid_amount);
        $this->assertEquals(InvoiceStatus::Partial, $invoice->status);
    }

    /**
     * Test invoice notes and terms
     */
    public function test_invoice_notes_and_terms(): void
    {
        $notes = 'Thank you for your business!';
        $terms = 'Payment due within 30 days. Late payments subject to 1.5% monthly interest.';

        $invoice = Invoice::factory()->create([
            'notes' => $notes,
            'terms' => $terms,
        ]);

        $this->assertEquals($notes, $invoice->notes);
        $this->assertEquals($terms, $invoice->terms);
    }

    /**
     * Test timestamps are recorded
     */
    public function test_timestamps_are_recorded(): void
    {
        $invoice = Invoice::factory()->create();

        $this->assertNotNull($invoice->created_at);
        $this->assertNotNull($invoice->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $invoice->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $invoice->updated_at);
    }
}
