<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Payment Controller
 *
 * Manages payment operations in the admin panel.
 */
class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param PaymentService $paymentService
     */
    public function __construct(
        private readonly PaymentService $paymentService
    ) {}

    /**
     * Display a listing of payments.
     *
     * Supports filtering by:
     * - status
     * - payment_method
     * - company_id
     * - invoice_id
     * - date_range (from, to)
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Payment::class);

        $query = Payment::with(['invoice.company', 'invoice.project']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by company
        if ($request->filled('company_id')) {
            $query->whereHas('invoice', function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        // Filter by invoice
        if ($request->filled('invoice_id')) {
            $query->where('invoice_id', $request->invoice_id);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('payment_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('payment_date', '<=', $request->to_date);
        }

        // Sort by latest payment date
        $query->latest('payment_date');

        $payments = $query->paginate(15)->withQueryString();

        // Get companies and invoices for filter dropdowns
        $companies = Company::orderBy('name')->get(['id', 'name']);
        $invoices = Invoice::orderBy('invoice_number')->get(['id', 'invoice_number']);

        // Calculate totals
        $totalReceived = Payment::where('status', 'completed')->sum('amount');
        $totalPending = Payment::where('status', 'pending')->sum('amount');
        $totalFailed = Payment::where('status', 'failed')->sum('amount');

        return view('admin.payments.index', compact(
            'payments',
            'companies',
            'invoices',
            'totalReceived',
            'totalPending',
            'totalFailed'
        ));
    }

    /**
     * Display the specified payment.
     *
     * @param Payment $payment
     * @return View
     */
    public function show(Payment $payment): View
    {
        $this->authorize('view', $payment);

        $payment->load([
            'invoice' => function ($query) {
                $query->with(['company', 'project', 'items']);
            }
        ]);

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Store a newly created payment in storage (process payment).
     *
     * @param StorePaymentRequest $request
     * @return RedirectResponse
     */
    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $invoice = Invoice::findOrFail($request->invoice_id);

        $this->authorize('create', [Payment::class, $invoice]);

        try {
            $payment = $this->paymentService->processPayment($request->validated());

            return redirect()
                ->route('admin.payments.show', $payment)
                ->with('success', 'Payment processed successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to process payment: ' . $e->getMessage());
        }
    }

    /**
     * Process a refund for the specified payment.
     *
     * @param Request $request
     * @param Payment $payment
     * @return RedirectResponse
     */
    public function refund(Request $request, Payment $payment): RedirectResponse
    {
        $this->authorize('refund', $payment);

        $request->validate([
            'refund_amount' => ['required', 'numeric', 'min:0.01', 'max:' . $payment->amount],
            'refund_reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->paymentService->refundPayment(
                $payment,
                (float) $request->refund_amount,
                $request->refund_reason
            );

            return redirect()
                ->back()
                ->with('success', 'Payment refunded successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to refund payment: ' . $e->getMessage());
        }
    }
}
