<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Payment Controller Feature Tests
 *
 * Tests the admin payment controller operations including:
 * - Listing payments with filtering
 * - Viewing payment details
 * - Processing payments
 * - Refunding payments
 * - Authorization checks
 * - Payment statistics
 */
class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $client;
    private Company $company;
    private Project $project;
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->client = User::factory()->create(['role' => 'client', 'is_active' => true]);
        $this->company = Company::factory()->create();
        $this->project = Project::factory()->create(['company_id' => $this->company->id]);
        $this->invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
        ]);
    }

    /**
     * Test admin can view payments index
     */
    public function test_admin_can_view_payments_index(): void
    {
        Payment::factory()->count(5)->create(['invoice_id' => $this->invoice->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.payments.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.payments.index');
        $response->assertViewHas('payments');
    }

    /**
     * Test client cannot access admin payments index
     */
    public function test_client_cannot_access_admin_payments_index(): void
    {
        $response = $this->actingAs($this->client)->get(route('admin.payments.index'));

        $response->assertStatus(403);
    }

    /**
     * Test guest cannot access admin payments index
     */
    public function test_guest_cannot_access_admin_payments_index(): void
    {
        $response = $this->get(route('admin.payments.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test payments index shows totals
     */
    public function test_payments_index_shows_totals(): void
    {
        Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'amount' => 1000.00,
            'status' => 'completed',
        ]);

        Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'amount' => 500.00,
            'status' => 'pending',
        ]);

        Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'amount' => 250.00,
            'status' => 'failed',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.payments.index'));

        $response->assertStatus(200);
        $response->assertViewHas('totalReceived', 1000.00);
        $response->assertViewHas('totalPending', 500.00);
        $response->assertViewHas('totalFailed', 250.00);
    }

    /**
     * Test admin can view payment details
     */
    public function test_admin_can_view_payment_details(): void
    {
        $payment = Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'payment_number' => 'PAY-12345',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.payments.show', $payment));

        $response->assertStatus(200);
        $response->assertViewIs('admin.payments.show');
        $response->assertViewHas('payment');
        $response->assertSee('PAY-12345');
    }

    /**
     * Test admin can process payment
     */
    public function test_admin_can_process_payment(): void
    {
        $paymentData = [
            'invoice_id' => $this->invoice->id,
            'amount' => 1000.00,
            'payment_method' => 'credit_card',
            'payment_date' => now()->format('Y-m-d'),
            'reference_number' => 'REF-123',
            'notes' => 'Test payment',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.payments.store'), $paymentData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('payments', [
            'invoice_id' => $this->invoice->id,
            'amount' => 1000.00,
            'payment_method' => 'credit_card',
        ]);
    }

    /**
     * Test payments index can filter by status
     */
    public function test_payments_index_can_filter_by_status(): void
    {
        Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'payment_number' => 'PAY-001',
            'status' => 'completed',
        ]);

        Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'payment_number' => 'PAY-002',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.payments.index', ['status' => 'completed']));

        $response->assertStatus(200);
        $response->assertSee('PAY-001');
        $response->assertDontSee('PAY-002');
    }

    /**
     * Test payments index can filter by payment method
     */
    public function test_payments_index_can_filter_by_payment_method(): void
    {
        Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'payment_number' => 'PAY-CC',
            'payment_method' => 'credit_card',
        ]);

        Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'payment_number' => 'PAY-BT',
            'payment_method' => 'bank_transfer',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.payments.index', ['payment_method' => 'credit_card']));

        $response->assertStatus(200);
        $response->assertSee('PAY-CC');
        $response->assertDontSee('PAY-BT');
    }

    /**
     * Test payments index can filter by company
     */
    public function test_payments_index_can_filter_by_company(): void
    {
        $company2 = Company::factory()->create();
        $invoice2 = Invoice::factory()->create(['company_id' => $company2->id]);

        Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'payment_number' => 'PAY-C1',
        ]);

        Payment::factory()->create([
            'invoice_id' => $invoice2->id,
            'payment_number' => 'PAY-C2',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.payments.index', ['company_id' => $this->company->id]));

        $response->assertStatus(200);
        $response->assertSee('PAY-C1');
        $response->assertDontSee('PAY-C2');
    }

    /**
     * Test payments index can filter by invoice
     */
    public function test_payments_index_can_filter_by_invoice(): void
    {
        $invoice2 = Invoice::factory()->create(['company_id' => $this->company->id]);

        Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'payment_number' => 'PAY-INV1',
        ]);

        Payment::factory()->create([
            'invoice_id' => $invoice2->id,
            'payment_number' => 'PAY-INV2',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.payments.index', ['invoice_id' => $this->invoice->id]));

        $response->assertStatus(200);
        $response->assertSee('PAY-INV1');
        $response->assertDontSee('PAY-INV2');
    }

    /**
     * Test payments index can filter by date range
     */
    public function test_payments_index_can_filter_by_date_range(): void
    {
        Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'payment_number' => 'PAY-EARLY',
            'payment_date' => '2025-01-15',
        ]);

        Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'payment_number' => 'PAY-LATE',
            'payment_date' => '2025-12-31',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.payments.index', [
                'from_date' => '2025-12-01',
                'to_date' => '2025-12-31',
            ]));

        $response->assertStatus(200);
        $response->assertSee('PAY-LATE');
        $response->assertDontSee('PAY-EARLY');
    }

    /**
     * Test payments index is paginated
     */
    public function test_payments_index_is_paginated(): void
    {
        Payment::factory()->count(20)->create(['invoice_id' => $this->invoice->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.payments.index'));

        $response->assertStatus(200);
        $response->assertViewHas('payments');

        $payments = $response->viewData('payments');
        $this->assertLessThanOrEqual(15, $payments->count());
    }

    /**
     * Test admin can refund payment
     */
    public function test_admin_can_refund_payment(): void
    {
        $payment = Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'amount' => 1000.00,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.payments.refund', $payment), [
                'refund_amount' => 500.00,
                'refund_reason' => 'Customer request',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /**
     * Test refund amount must be positive
     */
    public function test_refund_amount_must_be_positive(): void
    {
        $payment = Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'amount' => 1000.00,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.payments.refund', $payment), [
                'refund_amount' => 0,
                'refund_reason' => 'Test',
            ]);

        $response->assertSessionHasErrors('refund_amount');
    }

    /**
     * Test refund amount cannot exceed payment amount
     */
    public function test_refund_amount_cannot_exceed_payment_amount(): void
    {
        $payment = Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'amount' => 1000.00,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.payments.refund', $payment), [
                'refund_amount' => 1500.00,
                'refund_reason' => 'Test',
            ]);

        $response->assertSessionHasErrors('refund_amount');
    }

    /**
     * Test different payment methods can be used
     */
    public function test_different_payment_methods_can_be_used(): void
    {
        $methods = ['credit_card', 'debit_card', 'bank_transfer', 'paypal', 'stripe', 'check', 'cash'];

        foreach ($methods as $method) {
            $payment = Payment::factory()->create([
                'invoice_id' => $this->invoice->id,
                'payment_method' => $method,
            ]);

            $this->assertEquals($method, $payment->payment_method);
        }
    }

    /**
     * Test different payment statuses
     */
    public function test_different_payment_statuses(): void
    {
        $statuses = ['pending', 'completed', 'failed', 'refunded', 'cancelled'];

        foreach ($statuses as $status) {
            $payment = Payment::factory()->create([
                'invoice_id' => $this->invoice->id,
                'status' => $status,
            ]);

            $this->assertEquals($status, $payment->status);
        }
    }

    /**
     * Test payment details show invoice information
     */
    public function test_payment_details_show_invoice_information(): void
    {
        $payment = Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.payments.show', $payment));

        $response->assertStatus(200);
        $viewPayment = $response->viewData('payment');
        $this->assertNotNull($viewPayment->invoice);
        $this->assertEquals($this->invoice->id, $viewPayment->invoice->id);
    }

    /**
     * Test payment can have reference number
     */
    public function test_payment_can_have_reference_number(): void
    {
        $paymentData = [
            'invoice_id' => $this->invoice->id,
            'amount' => 1000.00,
            'payment_method' => 'bank_transfer',
            'payment_date' => now()->format('Y-m-d'),
            'reference_number' => 'BANK-REF-123456',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.payments.store'), $paymentData);

        $response->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'reference_number' => 'BANK-REF-123456',
        ]);
    }

    /**
     * Test payment can have notes
     */
    public function test_payment_can_have_notes(): void
    {
        $paymentData = [
            'invoice_id' => $this->invoice->id,
            'amount' => 1000.00,
            'payment_method' => 'credit_card',
            'payment_date' => now()->format('Y-m-d'),
            'notes' => 'Payment received for project milestone',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.payments.store'), $paymentData);

        $response->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'notes' => 'Payment received for project milestone',
        ]);
    }

    /**
     * Test payment amount must be positive
     */
    public function test_payment_amount_must_be_positive(): void
    {
        $paymentData = [
            'invoice_id' => $this->invoice->id,
            'amount' => 0,
            'payment_method' => 'credit_card',
            'payment_date' => now()->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.payments.store'), $paymentData);

        $response->assertSessionHasErrors();
    }

    /**
     * Test payment requires invoice
     */
    public function test_payment_requires_invoice(): void
    {
        $paymentData = [
            'amount' => 1000.00,
            'payment_method' => 'credit_card',
            'payment_date' => now()->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.payments.store'), $paymentData);

        $response->assertSessionHasErrors('invoice_id');
    }

    /**
     * Test payment requires valid invoice
     */
    public function test_payment_requires_valid_invoice(): void
    {
        $paymentData = [
            'invoice_id' => 99999,
            'amount' => 1000.00,
            'payment_method' => 'credit_card',
            'payment_date' => now()->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.payments.store'), $paymentData);

        $response->assertSessionHasErrors();
    }

    /**
     * Test completed payments count in total received
     */
    public function test_completed_payments_count_in_total_received(): void
    {
        Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'amount' => 1000.00,
            'status' => 'completed',
        ]);

        Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'amount' => 2000.00,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.payments.index'));

        $response->assertStatus(200);
        $response->assertViewHas('totalReceived', 3000.00);
    }

    /**
     * Test pending payments count in total pending
     */
    public function test_pending_payments_count_in_total_pending(): void
    {
        Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'amount' => 500.00,
            'status' => 'pending',
        ]);

        Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'amount' => 300.00,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.payments.index'));

        $response->assertStatus(200);
        $response->assertViewHas('totalPending', 800.00);
    }

    /**
     * Test failed payments count in total failed
     */
    public function test_failed_payments_count_in_total_failed(): void
    {
        Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'amount' => 250.00,
            'status' => 'failed',
        ]);

        Payment::factory()->create([
            'invoice_id' => $this->invoice->id,
            'amount' => 150.00,
            'status' => 'failed',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.payments.index'));

        $response->assertStatus(200);
        $response->assertViewHas('totalFailed', 400.00);
    }
}
