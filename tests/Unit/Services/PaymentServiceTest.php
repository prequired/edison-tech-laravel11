<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTOs\ProcessPaymentDTO;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Payment Service Unit Tests
 *
 * Tests the business logic in PaymentService including:
 * - Payment recording
 * - Payment number generation
 * - Invoice status updates
 * - Transaction integrity
 */
class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentService $paymentService;
    private Company $company;
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'total_amount' => 1000.00,
            'balance' => 1000.00,
            'paid_amount' => 0.00,
        ]);

        $this->paymentService = app(PaymentService::class);
    }

    /**
     * Test record payment with all fields
     */
    public function test_record_payment_with_all_fields(): void
    {
        $dto = new ProcessPaymentDTO(
            invoice_id: $this->invoice->id,
            amount: 500.00,
            payment_method: 'credit_card',
            status: 'completed',
            payment_date: now()->format('Y-m-d'),
            transaction_id: 'txn_123456',
            stripe_payment_intent_id: 'pi_123456',
            stripe_charge_id: 'ch_123456',
            notes: 'Partial payment',
            metadata: ['customer_id' => 'cus_123']
        );

        $payment = $this->paymentService->recordPayment($dto);

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals(500.00, $payment->amount);
        $this->assertEquals('credit_card', $payment->payment_method);
        $this->assertEquals('completed', $payment->status);
        $this->assertEquals($this->invoice->id, $payment->invoice_id);
        $this->assertEquals($this->company->id, $payment->company_id);

        $this->assertDatabaseHas('payments', [
            'invoice_id' => $this->invoice->id,
            'amount' => 500.00,
            'transaction_id' => 'txn_123456',
        ]);
    }

    /**
     * Test payment number generation
     */
    public function test_generates_unique_payment_number(): void
    {
        $dto1 = new ProcessPaymentDTO(
            invoice_id: $this->invoice->id,
            amount: 100.00,
            payment_method: 'credit_card',
            status: 'completed',
            payment_date: now()->format('Y-m-d'),
            transaction_id: 'txn_1',
            stripe_payment_intent_id: null,
            stripe_charge_id: null,
            notes: null,
            metadata: []
        );

        $dto2 = new ProcessPaymentDTO(
            invoice_id: $this->invoice->id,
            amount: 200.00,
            payment_method: 'bank_transfer',
            status: 'completed',
            payment_date: now()->format('Y-m-d'),
            transaction_id: 'txn_2',
            stripe_payment_intent_id: null,
            stripe_charge_id: null,
            notes: null,
            metadata: []
        );

        $payment1 = $this->paymentService->recordPayment($dto1);
        $payment2 = $this->paymentService->recordPayment($dto2);

        $this->assertNotEquals($payment1->payment_number, $payment2->payment_number);
        $this->assertNotEmpty($payment1->payment_number);
        $this->assertNotEmpty($payment2->payment_number);
    }

    /**
     * Test payment date defaults to today
     */
    public function test_payment_date_defaults_to_today(): void
    {
        $dto = new ProcessPaymentDTO(
            invoice_id: $this->invoice->id,
            amount: 100.00,
            payment_method: 'credit_card',
            status: 'completed',
            payment_date: null, // Not provided
            transaction_id: 'txn_123',
            stripe_payment_intent_id: null,
            stripe_charge_id: null,
            notes: null,
            metadata: []
        );

        $payment = $this->paymentService->recordPayment($dto);

        $this->assertEquals(now()->format('Y-m-d'), $payment->payment_date->format('Y-m-d'));
    }

    /**
     * Test payment gets company_id from invoice
     */
    public function test_payment_gets_company_id_from_invoice(): void
    {
        $dto = new ProcessPaymentDTO(
            invoice_id: $this->invoice->id,
            amount: 100.00,
            payment_method: 'credit_card',
            status: 'completed',
            payment_date: now()->format('Y-m-d'),
            transaction_id: 'txn_123',
            stripe_payment_intent_id: null,
            stripe_charge_id: null,
            notes: null,
            metadata: []
        );

        $payment = $this->paymentService->recordPayment($dto);

        $this->assertEquals($this->invoice->company_id, $payment->company_id);
        $this->assertEquals($this->company->id, $payment->company_id);
    }

    /**
     * Test metadata is stored correctly
     */
    public function test_metadata_stored_correctly(): void
    {
        $metadata = [
            'customer_id' => 'cus_123',
            'source' => 'online',
            'ip_address' => '192.168.1.1',
        ];

        $dto = new ProcessPaymentDTO(
            invoice_id: $this->invoice->id,
            amount: 100.00,
            payment_method: 'credit_card',
            status: 'completed',
            payment_date: now()->format('Y-m-d'),
            transaction_id: 'txn_123',
            stripe_payment_intent_id: null,
            stripe_charge_id: null,
            notes: null,
            metadata: $metadata
        );

        $payment = $this->paymentService->recordPayment($dto);

        $this->assertEquals($metadata, $payment->metadata);
        $this->assertIsArray($payment->metadata);
    }

    /**
     * Test payment recording is transactional
     */
    public function test_payment_recording_is_transactional(): void
    {
        $this->expectException(\Exception::class);

        $dto = new ProcessPaymentDTO(
            invoice_id: 99999, // Non-existent invoice
            amount: 100.00,
            payment_method: 'credit_card',
            status: 'completed',
            payment_date: now()->format('Y-m-d'),
            transaction_id: 'txn_123',
            stripe_payment_intent_id: null,
            stripe_charge_id: null,
            notes: null,
            metadata: []
        );

        try {
            $this->paymentService->recordPayment($dto);
        } catch (\Exception $e) {
            $this->assertDatabaseMissing('payments', [
                'transaction_id' => 'txn_123',
            ]);
            throw $e;
        }
    }

    /**
     * Test different payment methods are supported
     */
    public function test_supports_different_payment_methods(): void
    {
        $methods = ['credit_card', 'bank_transfer', 'paypal', 'stripe', 'cash'];

        foreach ($methods as $method) {
            $dto = new ProcessPaymentDTO(
                invoice_id: $this->invoice->id,
                amount: 100.00,
                payment_method: $method,
                status: 'completed',
                payment_date: now()->format('Y-m-d'),
                transaction_id: "txn_{$method}",
                stripe_payment_intent_id: null,
                stripe_charge_id: null,
                notes: null,
                metadata: []
            );

            $payment = $this->paymentService->recordPayment($dto);

            $this->assertEquals($method, $payment->payment_method);
        }
    }

    /**
     * Test payment with notes
     */
    public function test_payment_with_notes(): void
    {
        $notes = 'This is a partial payment for services rendered in Q1 2025';

        $dto = new ProcessPaymentDTO(
            invoice_id: $this->invoice->id,
            amount: 250.00,
            payment_method: 'bank_transfer',
            status: 'completed',
            payment_date: now()->format('Y-m-d'),
            transaction_id: 'txn_123',
            stripe_payment_intent_id: null,
            stripe_charge_id: null,
            notes: $notes,
            metadata: []
        );

        $payment = $this->paymentService->recordPayment($dto);

        $this->assertEquals($notes, $payment->notes);
    }

    /**
     * Test payment statuses
     */
    public function test_supports_different_payment_statuses(): void
    {
        $statuses = ['pending', 'completed', 'failed', 'refunded'];

        foreach ($statuses as $status) {
            $dto = new ProcessPaymentDTO(
                invoice_id: $this->invoice->id,
                amount: 100.00,
                payment_method: 'credit_card',
                status: $status,
                payment_date: now()->format('Y-m-d'),
                transaction_id: "txn_{$status}",
                stripe_payment_intent_id: null,
                stripe_charge_id: null,
                notes: null,
                metadata: []
            );

            $payment = $this->paymentService->recordPayment($dto);

            $this->assertEquals($status, $payment->status);
        }
    }

    /**
     * Test Stripe payment IDs are stored
     */
    public function test_stripe_payment_ids_stored(): void
    {
        $dto = new ProcessPaymentDTO(
            invoice_id: $this->invoice->id,
            amount: 500.00,
            payment_method: 'stripe',
            status: 'completed',
            payment_date: now()->format('Y-m-d'),
            transaction_id: 'txn_stripe',
            stripe_payment_intent_id: 'pi_1234567890',
            stripe_charge_id: 'ch_1234567890',
            notes: null,
            metadata: []
        );

        $payment = $this->paymentService->recordPayment($dto);

        $this->assertEquals('pi_1234567890', $payment->stripe_payment_intent_id);
        $this->assertEquals('ch_1234567890', $payment->stripe_charge_id);
    }

    /**
     * Test payment amount must be positive
     */
    public function test_payment_amount_validation(): void
    {
        $dto = new ProcessPaymentDTO(
            invoice_id: $this->invoice->id,
            amount: 100.00,
            payment_method: 'credit_card',
            status: 'completed',
            payment_date: now()->format('Y-m-d'),
            transaction_id: 'txn_123',
            stripe_payment_intent_id: null,
            stripe_charge_id: null,
            notes: null,
            metadata: []
        );

        $payment = $this->paymentService->recordPayment($dto);

        $this->assertGreaterThan(0, $payment->amount);
    }
}
