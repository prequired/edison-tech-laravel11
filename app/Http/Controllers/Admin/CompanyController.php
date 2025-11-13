<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Company Controller
 *
 * Manages CRUD operations for companies in the admin panel.
 */
class CompanyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param CompanyService $companyService
     */
    public function __construct(
        private readonly CompanyService $companyService
    ) {}

    /**
     * Display a listing of companies.
     *
     * Supports filtering by:
     * - status (active/inactive)
     * - type
     * - search (name, email, website)
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Company::class);

        $query = Company::withCount(['projects', 'invoices', 'users']);

        // Filter by status
        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search by name, email, or website
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('website', 'like', "%{$search}%");
            });
        }

        // Sort by latest by default
        $query->latest();

        $companies = $query->paginate(15)->withQueryString();

        return view('admin.companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new company.
     *
     * @return View
     */
    public function create(): View
    {
        $this->authorize('create', Company::class);

        return view('admin.companies.create');
    }

    /**
     * Store a newly created company in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Company::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'tax_id' => ['nullable', 'string', 'max:100'],
            'type' => ['required', 'string', 'in:client,partner,vendor'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        try {
            $company = $this->companyService->createCompany($validated);

            return redirect()
                ->route('admin.companies.show', $company)
                ->with('success', 'Company created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create company: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified company with statistics.
     *
     * @param Company $company
     * @return View
     */
    public function show(Company $company): View
    {
        $this->authorize('view', $company);

        $company->load([
            'projects' => function ($query) {
                $query->with('creator')->latest()->limit(10);
            },
            'invoices' => function ($query) {
                $query->latest()->limit(10);
            },
            'users' => function ($query) {
                $query->where('is_active', true);
            },
            'contracts' => function ($query) {
                $query->latest();
            }
        ]);

        // Calculate company statistics
        $totalProjects = $company->projects()->count();
        $activeProjects = $company->projects()->where('status', 'in_progress')->count();
        $completedProjects = $company->projects()->where('status', 'completed')->count();

        $totalInvoices = $company->invoices()->count();
        $paidInvoices = $company->invoices()->where('status', 'paid')->count();
        $totalRevenue = $company->invoices()->where('status', 'paid')->sum('total_amount');
        $pendingAmount = $company->invoices()->whereIn('status', ['draft', 'sent', 'overdue'])->sum('total_amount');

        $totalUsers = $company->users()->count();
        $activeUsers = $company->users()->where('is_active', true)->count();

        $totalContracts = $company->contracts()->count();
        $activeContracts = $company->contracts()->where('status', 'active')->count();

        return view('admin.companies.show', compact(
            'company',
            'totalProjects',
            'activeProjects',
            'completedProjects',
            'totalInvoices',
            'paidInvoices',
            'totalRevenue',
            'pendingAmount',
            'totalUsers',
            'activeUsers',
            'totalContracts',
            'activeContracts'
        ));
    }

    /**
     * Show the form for editing the specified company.
     *
     * @param Company $company
     * @return View
     */
    public function edit(Company $company): View
    {
        $this->authorize('update', $company);

        return view('admin.companies.edit', compact('company'));
    }

    /**
     * Update the specified company in storage.
     *
     * @param Request $request
     * @param Company $company
     * @return RedirectResponse
     */
    public function update(Request $request, Company $company): RedirectResponse
    {
        $this->authorize('update', $company);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'tax_id' => ['nullable', 'string', 'max:100'],
            'type' => ['required', 'string', 'in:client,partner,vendor'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        try {
            $this->companyService->updateCompany($company, $validated);

            return redirect()
                ->route('admin.companies.show', $company)
                ->with('success', 'Company updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update company: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified company from storage.
     *
     * @param Company $company
     * @return RedirectResponse
     */
    public function destroy(Company $company): RedirectResponse
    {
        $this->authorize('delete', $company);

        try {
            $this->companyService->deleteCompany($company);

            return redirect()
                ->route('admin.companies.index')
                ->with('success', 'Company deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete company: ' . $e->getMessage());
        }
    }
}
