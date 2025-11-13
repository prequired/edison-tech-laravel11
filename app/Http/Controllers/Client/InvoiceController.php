<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\DTOs\ProcessPaymentDTO;
use App\Models\Invoice;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Client Invoice Controller
 *
 * Allows clients to view invoices, download them, and make payments.
 */
class InvoiceController extends Controller
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
     * Display a listing of the client's invoices.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $companyId = Auth::user()->company_id;

        $invoices = Invoice::where('company_id', $companyId)
            ->with(['project', 'items', 'payments'])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest('invoice_date')
            ->paginate(15)
            ->withQueryString();

        // Calculate statistics
        $totalAmount = Invoice::where('company_id', $companyId)->sum('total_amount');
        $paidAmount = Invoice::where('company_id', $companyId)
            ->where('status', 'paid')
            ->sum('total_amount');
        $pendingAmount = Invoice::where('company_id', $companyId)
            ->whereIn('status', ['pending', 'overdue'])
            ->sum('total_amount');

        return view('client.invoices.index', compact(
            'invoices',
            'totalAmount',
            'paidAmount',
            'pendingAmount'
        ));
    }

    /**
     * Display the specified invoice.
     *
     * @param Invoice $invoice
     * @return View
     */
    public function show(Invoice $invoice): View
    {
        $this->authorize('view', $invoice);

        $invoice->load([
            'company',
            'project',
            'items',
            'payments' => function ($query) {
                $query->latest('payment_date');
            },
        ]);

        return view('client.invoices.show', compact('invoice'));
    }

    /**
     * Download the invoice as PDF.
     *
     * @param Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public function download(Invoice $invoice): \Illuminate\Http\Response
    {
        $this->authorize('view', $invoice);

        $invoice->load(['company', 'project', 'items']);

        // In a real implementation, you would generate a PDF here
        // For now, we'll return a simple response
        // You can use packages like barryvdh/laravel-dompdf or spatie/laravel-pdf

        $pdf = \PDF::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Show the payment form for the invoice.
     *
     * @param Invoice $invoice
     * @return View
     */
    public function pay(Invoice $invoice): View
    {
        $this->authorize('view', $invoice);

        // Only allow payment for pending or overdue invoices
        if (!in_array($invoice->status, ['pending', 'overdue'])) {
            abort(403, 'This invoice cannot be paid.');
        }

        $invoice->load(['company', 'project']);

        return view('client.invoices.pay', compact('invoice'));
    }

    /**
     * Process the payment for the invoice.
     *
     * @param Request $request
     * @param Invoice $invoice
     * @return RedirectResponse
     */
    public function processPayment(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('view', $invoice);

        // Only allow payment for pending or overdue invoices
        if (!in_array($invoice->status, ['pending', 'overdue'])) {
            return redirect()
                ->back()
                ->with('error', 'This invoice cannot be paid.');
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'in:credit_card,bank_transfer,paypal,stripe'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:' . $invoice->total_amount],
            'payment_token' => ['nullable', 'string'], // For Stripe/PayPal
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $payment = $this->paymentService->processPayment([
                'invoice_id' => $invoice->id,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_date' => now()->toDateString(),
                'transaction_id' => $validated['payment_token'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            return redirect()
                ->route('client.invoices.show', $invoice)
                ->with('success', 'Payment processed successfully. Thank you!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to process payment: ' . $e->getMessage());
        }
    }
}
