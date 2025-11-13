<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Project;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Invoice Controller
 *
 * Manages CRUD operations for invoices in the admin panel.
 */
class InvoiceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param InvoiceService $invoiceService
     */
    public function __construct(
        private readonly InvoiceService $invoiceService
    ) {}

    /**
     * Display a listing of invoices.
     *
     * Supports filtering by:
     * - status
     * - company_id
     * - date_range (from, to)
     * - search (invoice_number)
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Invoice::class);

        $query = Invoice::with(['company', 'project']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by company
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('issue_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('issue_date', '<=', $request->to_date);
        }

        // Search by invoice number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('invoice_number', 'like', "%{$search}%");
        }

        // Sort by latest by default
        $query->latest('issue_date');

        $invoices = $query->paginate(15)->withQueryString();

        // Get companies for filter dropdown
        $companies = Company::orderBy('name')->get(['id', 'name']);

        // Calculate totals
        $totalAmount = Invoice::sum('total_amount');
        $paidAmount = Invoice::where('status', 'paid')->sum('total_amount');
        $pendingAmount = Invoice::whereIn('status', ['draft', 'sent'])->sum('total_amount');

        return view('admin.invoices.index', compact(
            'invoices',
            'companies',
            'totalAmount',
            'paidAmount',
            'pendingAmount'
        ));
    }

    /**
     * Show the form for creating a new invoice.
     *
     * @param Request $request
     * @return View
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Invoice::class);

        $companies = Company::orderBy('name')->get(['id', 'name']);
        $projects = Project::with('company')->orderBy('name')->get();

        // Pre-select company and project if passed via query params
        $selectedCompanyId = $request->query('company_id');
        $selectedProjectId = $request->query('project_id');

        return view('admin.invoices.create', compact(
            'companies',
            'projects',
            'selectedCompanyId',
            'selectedProjectId'
        ));
    }

    /**
     * Store a newly created invoice in storage.
     *
     * @param StoreInvoiceRequest $request
     * @return RedirectResponse
     */
    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $this->authorize('create', Invoice::class);

        try {
            $invoice = $this->invoiceService->createInvoice($request->validated());

            return redirect()
                ->route('admin.invoices.show', $invoice)
                ->with('success', 'Invoice created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create invoice: ' . $e->getMessage());
        }
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
                $query->latest();
            }
        ]);

        // Calculate payment statistics
        $totalPaid = $invoice->payments->sum('amount');
        $remainingBalance = $invoice->total_amount - $totalPaid;

        return view('admin.invoices.show', compact('invoice', 'totalPaid', 'remainingBalance'));
    }

    /**
     * Show the form for editing the specified invoice.
     *
     * @param Invoice $invoice
     * @return View
     */
    public function edit(Invoice $invoice): View
    {
        $this->authorize('update', $invoice);

        $invoice->load(['company', 'project', 'items']);

        $companies = Company::orderBy('name')->get(['id', 'name']);
        $projects = Project::with('company')->orderBy('name')->get();

        return view('admin.invoices.edit', compact('invoice', 'companies', 'projects'));
    }

    /**
     * Update the specified invoice in storage.
     *
     * @param StoreInvoiceRequest $request
     * @param Invoice $invoice
     * @return RedirectResponse
     */
    public function update(StoreInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);

        try {
            $this->invoiceService->updateInvoice($invoice, $request->validated());

            return redirect()
                ->route('admin.invoices.show', $invoice)
                ->with('success', 'Invoice updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update invoice: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified invoice from storage.
     *
     * @param Invoice $invoice
     * @return RedirectResponse
     */
    public function destroy(Invoice $invoice): RedirectResponse
    {
        $this->authorize('delete', $invoice);

        try {
            $invoice->delete();

            return redirect()
                ->route('admin.invoices.index')
                ->with('success', 'Invoice deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete invoice: ' . $e->getMessage());
        }
    }

    /**
     * Mark the specified invoice as paid.
     *
     * @param Invoice $invoice
     * @return RedirectResponse
     */
    public function markAsPaid(Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);

        try {
            $this->invoiceService->markAsPaid($invoice);

            return redirect()
                ->back()
                ->with('success', 'Invoice marked as paid successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to mark invoice as paid: ' . $e->getMessage());
        }
    }

    /**
     * Send the specified invoice to the client.
     *
     * @param Invoice $invoice
     * @return RedirectResponse
     */
    public function send(Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);

        try {
            $this->invoiceService->sendInvoice($invoice);

            return redirect()
                ->back()
                ->with('success', 'Invoice sent successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to send invoice: ' . $e->getMessage());
        }
    }
}
