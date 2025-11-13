<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Payment Model Unit Tests
 *
 * Tests the Payment model including:
 * - Relationships
 * - Status handling
 * - Payment methods
 * - Decimal precision
 * - Metadata storage
 */
class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
        ]);
    }

    /**
     * Test payment belongs to invoice
     */
    public function test_belongs_to_invoice(): void
    {
        $payment = Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
        ]);

        $this->assertInstanceOf(Invoice::class, $payment->invoice);
        $this->assertEquals($this->invoice->id, $payment->invoice->id);
    }

    /**
     * Test payment belongs to company
     */
    public function test_belongs_to_company(): void
    {
        $payment = Payment::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $this->assertInstanceOf(Company::class, $payment->company);
        $this->assertEquals($this->company->id, $payment->company->id);
    }

    /**
     * Test payment amount is decimal
     */
    public function test_amount_is_decimal(): void
    {
        $payment = Payment::factory()->create([
            'amount' => '1234.56',
        ]);

        $this->assertIsFloat($payment->amount);
        $this->assertEquals(1234.56, $payment->amount);
    }

    /**
     * Test payment date is cast to Carbon
     */
    public function test_payment_date_cast_to_carbon(): void
    {
        $paymentDate = now();

        $payment = Payment::factory()->create([
            'payment_date' => $paymentDate,
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $payment->payment_date);
        $this->assertEquals($paymentDate->format('Y-m-d'), $payment->payment_date->format('Y-m-d'));
    }

    /**
     * Test metadata is cast to array
     */
    public function test_metadata_cast_to_array(): void
    {
        $metadata = [
            'customer_id' => 'cus_123',
            'source' => 'online',
            'ip_address' => '192.168.1.1',
        ];

        $payment = Payment::factory()->create([
            'metadata' => $metadata,
        ]);

        $this->assertIsArray($payment->metadata);
        $this->assertEquals($metadata, $payment->metadata);
    }

    /**
     * Test different payment statuses
     */
    public function test_different_payment_statuses(): void
    {
        $statuses = ['pending', 'completed', 'failed', 'refunded'];

        foreach ($statuses as $status) {
            $payment = Payment::factory()->create(['status' => $status]);
            $this->assertEquals($status, $payment->status);
        }
    }

    /**
     * Test different payment methods
     */
    public function test_different_payment_methods(): void
    {
        $methods = ['credit_card', 'bank_transfer', 'paypal', 'stripe', 'cash'];

        foreach ($methods as $method) {
            $payment = Payment::factory()->create(['payment_method' => $method]);
            $this->assertEquals($method, $payment->payment_method);
        }
    }

    /**
     * Test payment number is unique
     */
    public function test_payment_number_is_unique(): void
    {
        Payment::factory()->create([
            'payment_number' => 'PAY-001',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Payment::factory()->create([
            'payment_number' => 'PAY-001',
        ]);
    }

    /**
     * Test Stripe payment intent ID storage
     */
    public function test_stripe_payment_intent_id_stored(): void
    {
        $payment = Payment::factory()->create([
            'stripe_payment_intent_id' => 'pi_1234567890',
        ]);

        $this->assertEquals('pi_1234567890', $payment->stripe_payment_intent_id);
    }

    /**
     * Test Stripe charge ID storage
     */
    public function test_stripe_charge_id_stored(): void
    {
        $payment = Payment::factory()->create([
            'stripe_charge_id' => 'ch_1234567890',
        ]);

        $this->assertEquals('ch_1234567890', $payment->stripe_charge_id);
    }

    /**
     * Test transaction ID storage
     */
    public function test_transaction_id_stored(): void
    {
        $payment = Payment::factory()->create([
            'transaction_id' => 'txn_abc123xyz',
        ]);

        $this->assertEquals('txn_abc123xyz', $payment->transaction_id);
    }

    /**
     * Test payment with notes
     */
    public function test_payment_with_notes(): void
    {
        $notes = 'Partial payment for services rendered in Q1 2025';

        $payment = Payment::factory()->create([
            'notes' => $notes,
        ]);

        $this->assertEquals($notes, $payment->notes);
    }

    /**
     * Test completed payment
     */
    public function test_completed_payment(): void
    {
        $payment = Payment::factory()->create([
            'status' => 'completed',
            'payment_date' => now(),
            'transaction_id' => 'txn_completed_123',
        ]);

        $this->assertEquals('completed', $payment->status);
        $this->assertNotNull($payment->payment_date);
        $this->assertNotNull($payment->transaction_id);
    }

    /**
     * Test failed payment
     */
    public function test_failed_payment(): void
    {
        $payment = Payment::factory()->create([
            'status' => 'failed',
            'notes' => 'Insufficient funds',
        ]);

        $this->assertEquals('failed', $payment->status);
        $this->assertNotNull($payment->notes);
    }

    /**
     * Test refunded payment
     */
    public function test_refunded_payment(): void
    {
        $payment = Payment::factory()->create([
            'status' => 'refunded',
            'amount' => 500.00,
        ]);

        $this->assertEquals('refunded', $payment->status);
        $this->assertEquals(500.00, $payment->amount);
    }

    /**
     * Test fillable attributes
     */
    public function test_fillable_attributes(): void
    {
        $data = [
            'invoice_id' => $this->invoice->id,
            'company_id' => $this->company->id,
            'payment_number' => 'PAY-TEST-001',
            'amount' => 1000.00,
            'payment_method' => 'credit_card',
            'status' => 'completed',
            'payment_date' => now(),
            'transaction_id' => 'txn_test_123',
            'notes' => 'Test payment',
            'metadata' => ['test' => 'data'],
        ];

        $payment = Payment::create($data);

        $this->assertEquals('PAY-TEST-001', $payment->payment_number);
        $this->assertEquals(1000.00, $payment->amount);
        $this->assertEquals('completed', $payment->status);
    }

    /**
     * Test timestamps are recorded
     */
    public function test_timestamps_are_recorded(): void
    {
        $payment = Payment::factory()->create();

        $this->assertNotNull($payment->created_at);
        $this->assertNotNull($payment->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $payment->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $payment->updated_at);
    }

    /**
     * Test payment soft deletes
     */
    public function test_uses_soft_deletes(): void
    {
        $payment = Payment::factory()->create();
        $paymentId = $payment->id;

        $payment->delete();

        $this->assertSoftDeleted('payments', ['id' => $paymentId]);

        $payment->restore();
        $this->assertDatabaseHas('payments', [
            'id' => $paymentId,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test metadata can be empty
     */
    public function test_metadata_can_be_empty(): void
    {
        $payment = Payment::factory()->create([
            'metadata' => [],
        ]);

        $this->assertIsArray($payment->metadata);
        $this->assertEmpty($payment->metadata);
    }

    /**
     * Test notes can be nullable
     */
    public function test_notes_can_be_nullable(): void
    {
        $payment = Payment::factory()->create(['notes' => null]);

        $this->assertNull($payment->notes);
    }

    /**
     * Test amount precision
     */
    public function test_amount_precision(): void
    {
        $payment = Payment::factory()->create([
            'amount' => 99.99,
        ]);

        $this->assertEquals(99.99, $payment->amount);
    }

    /**
     * Test large amounts
     */
    public function test_large_amounts(): void
    {
        $payment = Payment::factory()->create([
            'amount' => 999999.99,
        ]);

        $this->assertEquals(999999.99, $payment->amount);
    }

    /**
     * Test payment method can be custom
     */
    public function test_payment_method_can_be_custom(): void
    {
        $payment = Payment::factory()->create([
            'payment_method' => 'custom_gateway',
        ]);

        $this->assertEquals('custom_gateway', $payment->payment_method);
    }
}
