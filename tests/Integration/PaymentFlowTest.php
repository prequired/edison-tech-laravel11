<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Payment Flow Integration Tests
 *
 * Tests the complete payment processing flow including:
 * - Invoice creation
 * - Payment processing
 * - Invoice status updates
 * - Payment refunds
 * - Partial payments
 * - Overpayments
 * - Error handling
 */
class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Company $company;
    private Project $project;
    private Invoice $invoice;
    private PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->company = Company::factory()->create();
        $this->project = Project::factory()->create(['company_id' => $this->company->id]);

        $this->paymentService = app(PaymentService::class);

        // Fake notifications
        Notification::fake();
    }

    /**
     * Test complete payment flow from invoice creation to full payment
     */
    public function test_complete_payment_flow_from_invoice_to_full_payment(): void
    {
        // Step 1: Create invoice
        $invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'total_amount' => 1000.00,
            'paid_amount' => 0.00,
            'balance' => 1000.00,
            'status' => 'draft',
        ]);

        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'quantity' => 10,
            'unit_price' => 100.00,
            'amount' => 1000.00,
        ]);

        $this->assertEquals('draft', $invoice->status);
        $this->assertEquals(1000.00, $invoice->balance);

        // Step 2: Issue invoice
        $invoice->update(['status' => 'sent']);
        $this->assertEquals('sent', $invoice->fresh()->status);

        // Step 3: Process payment
        $paymentData = [
            'invoice_id' => $invoice->id,
            'amount' => 1000.00,
            'payment_method' => 'credit_card',
            'payment_date' => now()->toDateString(),
            'reference_number' => 'REF-123',
        ];

        $payment = $this->paymentService->recordPayment($paymentData);

        // Step 4: Verify payment created
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals(1000.00, $payment->amount);
        $this->assertEquals('completed', $payment->status);

        // Step 5: Verify invoice updated
        $invoice->refresh();
        $this->assertEquals(1000.00, $invoice->paid_amount);
        $this->assertEquals(0.00, $invoice->balance);
        $this->assertEquals('paid', $invoice->status);
    }

    /**
     * Test partial payment flow
     */
    public function test_partial_payment_flow(): void
    {
        $invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'total_amount' => 1000.00,
            'paid_amount' => 0.00,
            'balance' => 1000.00,
            'status' => 'sent',
        ]);

        // First partial payment (40%)
        $payment1 = $this->paymentService->recordPayment([
            'invoice_id' => $invoice->id,
            'amount' => 400.00,
            'payment_method' => 'credit_card',
            'payment_date' => now()->toDateString(),
        ]);

        $invoice->refresh();
        $this->assertEquals(400.00, $invoice->paid_amount);
        $this->assertEquals(600.00, $invoice->balance);
        $this->assertEquals('partially_paid', $invoice->status);

        // Second partial payment (30%)
        $payment2 = $this->paymentService->recordPayment([
            'invoice_id' => $invoice->id,
            'amount' => 300.00,
            'payment_method' => 'bank_transfer',
            'payment_date' => now()->toDateString(),
        ]);

        $invoice->refresh();
        $this->assertEquals(700.00, $invoice->paid_amount);
        $this->assertEquals(300.00, $invoice->balance);
        $this->assertEquals('partially_paid', $invoice->status);

        // Final payment (30%)
        $payment3 = $this->paymentService->recordPayment([
            'invoice_id' => $invoice->id,
            'amount' => 300.00,
            'payment_method' => 'paypal',
            'payment_date' => now()->toDateString(),
        ]);

        $invoice->refresh();
        $this->assertEquals(1000.00, $invoice->paid_amount);
        $this->assertEquals(0.00, $invoice->balance);
        $this->assertEquals('paid', $invoice->status);
    }

    /**
     * Test refund flow
     */
    public function test_refund_flow(): void
    {
        // Create invoice and full payment
        $invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'total_amount' => 1000.00,
            'paid_amount' => 1000.00,
            'balance' => 0.00,
            'status' => 'paid',
        ]);

        $payment = Payment::factory()->create([
            'invoice_id' => $invoice->id,
            'amount' => 1000.00,
            'status' => 'completed',
        ]);

        // Process partial refund
        $this->paymentService->refundPayment($payment, 400.00, 'Customer request');

        $payment->refresh();
        $invoice->refresh();

        $this->assertEquals('refunded', $payment->status);
        $this->assertEquals(600.00, $invoice->paid_amount);
        $this->assertEquals(400.00, $invoice->balance);
        $this->assertEquals('partially_paid', $invoice->status);
    }

    /**
     * Test full refund flow
     */
    public function test_full_refund_flow(): void
    {
        $invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'total_amount' => 1000.00,
            'paid_amount' => 1000.00,
            'balance' => 0.00,
            'status' => 'paid',
        ]);

        $payment = Payment::factory()->create([
            'invoice_id' => $invoice->id,
            'amount' => 1000.00,
            'status' => 'completed',
        ]);

        // Process full refund
        $this->paymentService->refundPayment($payment, 1000.00, 'Order cancelled');

        $payment->refresh();
        $invoice->refresh();

        $this->assertEquals('refunded', $payment->status);
        $this->assertEquals(0.00, $invoice->paid_amount);
        $this->assertEquals(1000.00, $invoice->balance);
        $this->assertEquals('sent', $invoice->status);
    }

    /**
     * Test multiple payments for same invoice
     */
    public function test_multiple_payments_for_same_invoice(): void
    {
        $invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'total_amount' => 5000.00,
            'paid_amount' => 0.00,
            'balance' => 5000.00,
            'status' => 'sent',
        ]);

        // Create 5 payments of $1000 each
        for ($i = 1; $i <= 5; $i++) {
            $payment = $this->paymentService->recordPayment([
                'invoice_id' => $invoice->id,
                'amount' => 1000.00,
                'payment_method' => 'credit_card',
                'payment_date' => now()->toDateString(),
                'reference_number' => "REF-{$i}",
            ]);

            $this->assertInstanceOf(Payment::class, $payment);
        }

        $invoice->refresh();
        $this->assertEquals(5000.00, $invoice->paid_amount);
        $this->assertEquals(0.00, $invoice->balance);
        $this->assertEquals('paid', $invoice->status);
        $this->assertCount(5, $invoice->payments);
    }

    /**
     * Test overpayment handling
     */
    public function test_overpayment_handling(): void
    {
        $invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'total_amount' => 1000.00,
            'paid_amount' => 0.00,
            'balance' => 1000.00,
            'status' => 'sent',
        ]);

        // Attempt to pay more than invoice total
        $payment = $this->paymentService->recordPayment([
            'invoice_id' => $invoice->id,
            'amount' => 1200.00,
            'payment_method' => 'credit_card',
            'payment_date' => now()->toDateString(),
        ]);

        $invoice->refresh();

        // Payment should be recorded
        $this->assertEquals(1200.00, $payment->amount);

        // Invoice should show overpayment
        $this->assertEquals(1200.00, $invoice->paid_amount);
        $this->assertEquals(-200.00, $invoice->balance);
        $this->assertEquals('paid', $invoice->status);
    }

    /**
     * Test payment with different payment methods
     */
    public function test_payment_with_different_payment_methods(): void
    {
        $invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'total_amount' => 3000.00,
            'paid_amount' => 0.00,
            'balance' => 3000.00,
            'status' => 'sent',
        ]);

        $methods = ['credit_card', 'bank_transfer', 'paypal'];

        foreach ($methods as $index => $method) {
            $payment = $this->paymentService->recordPayment([
                'invoice_id' => $invoice->id,
                'amount' => 1000.00,
                'payment_method' => $method,
                'payment_date' => now()->toDateString(),
            ]);

            $this->assertEquals($method, $payment->payment_method);
        }

        $invoice->refresh();
        $this->assertEquals('paid', $invoice->status);
        $this->assertCount(3, $invoice->payments);
    }

    /**
     * Test payment number generation is unique
     */
    public function test_payment_number_generation_is_unique(): void
    {
        $invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'total_amount' => 5000.00,
            'status' => 'sent',
        ]);

        $paymentNumbers = [];

        for ($i = 0; $i < 10; $i++) {
            $payment = $this->paymentService->recordPayment([
                'invoice_id' => $invoice->id,
                'amount' => 100.00,
                'payment_method' => 'credit_card',
                'payment_date' => now()->toDateString(),
            ]);

            $this->assertNotNull($payment->payment_number);
            $this->assertNotContains($payment->payment_number, $paymentNumbers);

            $paymentNumbers[] = $payment->payment_number;
        }
    }

    /**
     * Test failed payment does not update invoice
     */
    public function test_failed_payment_does_not_update_invoice(): void
    {
        $invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'total_amount' => 1000.00,
            'paid_amount' => 0.00,
            'balance' => 1000.00,
            'status' => 'sent',
        ]);

        $payment = Payment::factory()->create([
            'invoice_id' => $invoice->id,
            'amount' => 1000.00,
            'status' => 'failed',
        ]);

        $invoice->refresh();

        // Invoice should not be updated for failed payment
        $this->assertEquals(0.00, $invoice->paid_amount);
        $this->assertEquals(1000.00, $invoice->balance);
        $this->assertEquals('sent', $invoice->status);
    }

    /**
     * Test pending payment does not mark invoice as paid
     */
    public function test_pending_payment_does_not_mark_invoice_as_paid(): void
    {
        $invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'total_amount' => 1000.00,
            'paid_amount' => 0.00,
            'balance' => 1000.00,
            'status' => 'sent',
        ]);

        $payment = Payment::factory()->create([
            'invoice_id' => $invoice->id,
            'amount' => 1000.00,
            'status' => 'pending',
        ]);

        $invoice->refresh();

        // Invoice should not be marked as paid for pending payment
        $this->assertNotEquals('paid', $invoice->status);
    }

    /**
     * Test payment date is recorded correctly
     */
    public function test_payment_date_is_recorded_correctly(): void
    {
        $invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'total_amount' => 1000.00,
            'status' => 'sent',
        ]);

        $paymentDate = '2025-06-15';

        $payment = $this->paymentService->recordPayment([
            'invoice_id' => $invoice->id,
            'amount' => 1000.00,
            'payment_method' => 'credit_card',
            'payment_date' => $paymentDate,
        ]);

        $this->assertEquals($paymentDate, $payment->payment_date->format('Y-m-d'));
    }

    /**
     * Test payment with notes
     */
    public function test_payment_with_notes(): void
    {
        $invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'total_amount' => 1000.00,
            'status' => 'sent',
        ]);

        $notes = 'Payment received for Q1 services';

        $payment = $this->paymentService->recordPayment([
            'invoice_id' => $invoice->id,
            'amount' => 1000.00,
            'payment_method' => 'bank_transfer',
            'payment_date' => now()->toDateString(),
            'notes' => $notes,
        ]);

        $this->assertEquals($notes, $payment->notes);
    }

    /**
     * Test invoice with multiple items total calculation
     */
    public function test_invoice_with_multiple_items_total_calculation(): void
    {
        $invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'total_amount' => 0.00,
            'status' => 'draft',
        ]);

        // Add 3 items
        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'quantity' => 5,
            'unit_price' => 100.00,
            'amount' => 500.00,
        ]);

        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'quantity' => 3,
            'unit_price' => 200.00,
            'amount' => 600.00,
        ]);

        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'quantity' => 2,
            'unit_price' => 150.00,
            'amount' => 300.00,
        ]);

        // Update invoice total
        $total = $invoice->items->sum('amount');
        $invoice->update([
            'total_amount' => $total,
            'balance' => $total,
        ]);

        $this->assertEquals(1400.00, $invoice->total_amount);

        // Pay full amount
        $payment = $this->paymentService->recordPayment([
            'invoice_id' => $invoice->id,
            'amount' => 1400.00,
            'payment_method' => 'credit_card',
            'payment_date' => now()->toDateString(),
        ]);

        $invoice->refresh();
        $this->assertEquals('paid', $invoice->status);
        $this->assertEquals(0.00, $invoice->balance);
    }

    /**
     * Test refund cannot exceed payment amount
     */
    public function test_refund_cannot_exceed_payment_amount(): void
    {
        $invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'total_amount' => 1000.00,
            'paid_amount' => 1000.00,
            'status' => 'paid',
        ]);

        $payment = Payment::factory()->create([
            'invoice_id' => $invoice->id,
            'amount' => 1000.00,
            'status' => 'completed',
        ]);

        $this->expectException(\Exception::class);

        $this->paymentService->refundPayment($payment, 1500.00, 'Should fail');
    }

    /**
     * Test payment reference number is stored
     */
    public function test_payment_reference_number_is_stored(): void
    {
        $invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'total_amount' => 1000.00,
            'status' => 'sent',
        ]);

        $referenceNumber = 'BANK-TXN-987654321';

        $payment = $this->paymentService->recordPayment([
            'invoice_id' => $invoice->id,
            'amount' => 1000.00,
            'payment_method' => 'bank_transfer',
            'payment_date' => now()->toDateString(),
            'reference_number' => $referenceNumber,
        ]);

        $this->assertEquals($referenceNumber, $payment->reference_number);
    }
}
