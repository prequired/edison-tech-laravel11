<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTOs\CreateInvoiceDTO;
use App\Enums\InvoiceStatus;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Project;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Invoice Service Unit Tests
 *
 * Tests the business logic in InvoiceService including:
 * - Invoice creation with auto-number generation
 * - Invoice item management
 * - Tax and total calculations
 * - Transaction integrity
 */
class InvoiceServiceTest extends TestCase
{
    use RefreshDatabase;

    private InvoiceService $invoiceService;
    private Company $company;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->invoiceService = app(InvoiceService::class);
        $this->company = Company::factory()->create();
        $this->project = Project::factory()->create([
            'company_id' => $this->company->id,
        ]);
    }

    /**
     * Test invoice creation with items
     */
    public function test_create_invoice_with_items(): void
    {
        $dto = new CreateInvoiceDTO(
            company_id: $this->company->id,
            project_id: $this->project->id,
            status: InvoiceStatus::Draft,
            issue_date: now(),
            due_date: now()->addDays(30),
            tax_rate: 10.0,
            discount_amount: 0.0,
            currency: 'USD',
            notes: 'Test invoice',
            terms: 'Net 30',
            items: [
                [
                    'description' => 'Web Development',
                    'quantity' => 10.0,
                    'unit_price' => 100.0,
                    'tax_rate' => 0.0,
                ],
                [
                    'description' => 'Design Work',
                    'quantity' => 5.0,
                    'unit_price' => 80.0,
                    'tax_rate' => 0.0,
                ],
            ]
        );

        $invoice = $this->invoiceService->create($dto);

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertCount(2, $invoice->items);
        $this->assertEquals($this->company->id, $invoice->company_id);
        $this->assertEquals($this->project->id, $invoice->project_id);
        $this->assertEquals(InvoiceStatus::Draft, $invoice->status);
    }

    /**
     * Test invoice number generation
     */
    public function test_generates_unique_invoice_number(): void
    {
        $dto1 = new CreateInvoiceDTO(
            company_id: $this->company->id,
            project_id: $this->project->id,
            status: InvoiceStatus::Draft,
            issue_date: now(),
            due_date: now()->addDays(30),
            tax_rate: 0.0,
            discount_amount: 0.0,
            currency: 'USD',
            notes: '',
            terms: 'Net 30',
            items: [
                [
                    'description' => 'Item 1',
                    'quantity' => 1.0,
                    'unit_price' => 100.0,
                    'tax_rate' => 0.0,
                ],
            ]
        );

        $dto2 = new CreateInvoiceDTO(
            company_id: $this->company->id,
            project_id: $this->project->id,
            status: InvoiceStatus::Draft,
            issue_date: now(),
            due_date: now()->addDays(30),
            tax_rate: 0.0,
            discount_amount: 0.0,
            currency: 'USD',
            notes: '',
            terms: 'Net 30',
            items: [
                [
                    'description' => 'Item 2',
                    'quantity' => 1.0,
                    'unit_price' => 200.0,
                    'tax_rate' => 0.0,
                ],
            ]
        );

        $invoice1 = $this->invoiceService->create($dto1);
        $invoice2 = $this->invoiceService->create($dto2);

        $this->assertNotEquals($invoice1->invoice_number, $invoice2->invoice_number);
        $this->assertNotEmpty($invoice1->invoice_number);
        $this->assertNotEmpty($invoice2->invoice_number);
    }

    /**
     * Test invoice total calculation
     */
    public function test_calculates_invoice_totals_correctly(): void
    {
        $dto = new CreateInvoiceDTO(
            company_id: $this->company->id,
            project_id: $this->project->id,
            status: InvoiceStatus::Draft,
            issue_date: now(),
            due_date: now()->addDays(30),
            tax_rate: 10.0, // 10% tax
            discount_amount: 50.0,
            currency: 'USD',
            notes: '',
            terms: 'Net 30',
            items: [
                [
                    'description' => 'Item 1',
                    'quantity' => 10.0,
                    'unit_price' => 100.0, // $1000
                    'tax_rate' => 0.0,
                ],
                [
                    'description' => 'Item 2',
                    'quantity' => 5.0,
                    'unit_price' => 80.0, // $400
                    'tax_rate' => 0.0,
                ],
            ]
        );

        $invoice = $this->invoiceService->create($dto);

        // Subtotal: 1000 + 400 = 1400
        $this->assertEquals(1400.00, $invoice->subtotal);

        // Tax: 10% of 1400 = 140
        $this->assertEquals(140.00, $invoice->tax_amount);

        // Total: 1400 + 140 - 50 (discount) = 1490
        $this->assertEquals(1490.00, $invoice->total_amount);

        // Balance should equal total (nothing paid yet)
        $this->assertEquals($invoice->total_amount, $invoice->balance);
    }

    /**
     * Test invoice item calculation
     */
    public function test_calculates_invoice_item_total(): void
    {
        $dto = new CreateInvoiceDTO(
            company_id: $this->company->id,
            project_id: $this->project->id,
            status: InvoiceStatus::Draft,
            issue_date: now(),
            due_date: now()->addDays(30),
            tax_rate: 0.0,
            discount_amount: 0.0,
            currency: 'USD',
            notes: '',
            terms: 'Net 30',
            items: [
                [
                    'description' => 'Consulting Hours',
                    'quantity' => 12.5,
                    'unit_price' => 150.0,
                    'tax_rate' => 0.0,
                ],
            ]
        );

        $invoice = $this->invoiceService->create($dto);
        $item = $invoice->items->first();

        // 12.5 * 150 = 1875
        $this->assertEquals(1875.00, $item->total);
    }

    /**
     * Test invoice creation is transactional
     */
    public function test_invoice_creation_is_transactional(): void
    {
        // If item creation fails, invoice should not be created
        $this->expectException(\Exception::class);

        // Mock InvoiceItem to throw exception
        $this->mock(InvoiceItem::class, function ($mock) {
            $mock->shouldReceive('create')->andThrow(new \Exception('Database error'));
        });

        $dto = new CreateInvoiceDTO(
            company_id: $this->company->id,
            project_id: $this->project->id,
            status: InvoiceStatus::Draft,
            issue_date: now(),
            due_date: now()->addDays(30),
            tax_rate: 0.0,
            discount_amount: 0.0,
            currency: 'USD',
            notes: '',
            terms: 'Net 30',
            items: [
                [
                    'description' => 'Item',
                    'quantity' => 1.0,
                    'unit_price' => 100.0,
                    'tax_rate' => 0.0,
                ],
            ]
        );

        try {
            $this->invoiceService->create($dto);
        } catch (\Exception $e) {
            // Verify no invoice was created
            $this->assertEquals(0, Invoice::count());
            throw $e;
        }
    }

    /**
     * Test invoice relationships are loaded
     */
    public function test_created_invoice_loads_relationships(): void
    {
        $dto = new CreateInvoiceDTO(
            company_id: $this->company->id,
            project_id: $this->project->id,
            status: InvoiceStatus::Draft,
            issue_date: now(),
            due_date: now()->addDays(30),
            tax_rate: 0.0,
            discount_amount: 0.0,
            currency: 'USD',
            notes: '',
            terms: 'Net 30',
            items: [
                [
                    'description' => 'Item',
                    'quantity' => 1.0,
                    'unit_price' => 100.0,
                    'tax_rate' => 0.0,
                ],
            ]
        );

        $invoice = $this->invoiceService->create($dto);

        $this->assertTrue($invoice->relationLoaded('items'));
        $this->assertTrue($invoice->relationLoaded('company'));
        $this->assertTrue($invoice->relationLoaded('project'));
        $this->assertInstanceOf(Company::class, $invoice->company);
        $this->assertInstanceOf(Project::class, $invoice->project);
    }

    /**
     * Test discount is applied correctly
     */
    public function test_applies_discount_correctly(): void
    {
        $dto = new CreateInvoiceDTO(
            company_id: $this->company->id,
            project_id: $this->project->id,
            status: InvoiceStatus::Draft,
            issue_date: now(),
            due_date: now()->addDays(30),
            tax_rate: 0.0,
            discount_amount: 100.0,
            currency: 'USD',
            notes: '',
            terms: 'Net 30',
            items: [
                [
                    'description' => 'Service',
                    'quantity' => 1.0,
                    'unit_price' => 500.0,
                    'tax_rate' => 0.0,
                ],
            ]
        );

        $invoice = $this->invoiceService->create($dto);

        // Subtotal: 500
        // Discount: 100
        // Total: 400
        $this->assertEquals(500.00, $invoice->subtotal);
        $this->assertEquals(400.00, $invoice->total_amount);
    }

    /**
     * Test invoice with zero items has zero totals
     */
    public function test_invoice_with_no_items_has_zero_totals(): void
    {
        $dto = new CreateInvoiceDTO(
            company_id: $this->company->id,
            project_id: $this->project->id,
            status: InvoiceStatus::Draft,
            issue_date: now(),
            due_date: now()->addDays(30),
            tax_rate: 10.0,
            discount_amount: 0.0,
            currency: 'USD',
            notes: '',
            terms: 'Net 30',
            items: []
        );

        $invoice = $this->invoiceService->create($dto);

        $this->assertEquals(0.00, $invoice->subtotal);
        $this->assertEquals(0.00, $invoice->tax_amount);
        $this->assertEquals(0.00, $invoice->total_amount);
        $this->assertEquals(0.00, $invoice->balance);
    }
}
