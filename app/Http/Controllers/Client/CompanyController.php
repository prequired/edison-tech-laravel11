<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Client Company Controller
 *
 * Allows clients to view and edit their own company information.
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
     * Display the client's company information.
     *
     * @return View
     */
    public function show(): View
    {
        $company = Auth::user()->company;

        if (!$company) {
            abort(404, 'Company not found.');
        }

        $this->authorize('view', $company);

        $company->load(['users', 'projects']);

        return view('client.company.show', compact('company'));
    }

    /**
     * Show the form for editing the client's company.
     *
     * @return View
     */
    public function edit(): View
    {
        $company = Auth::user()->company;

        if (!$company) {
            abort(404, 'Company not found.');
        }

        $this->authorize('update', $company);

        return view('client.company.edit', compact('company'));
    }

    /**
     * Update the client's company in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $company = Auth::user()->company;

        if (!$company) {
            abort(404, 'Company not found.');
        }

        $this->authorize('update', $company);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'tax_id' => ['nullable', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'billing_address' => ['nullable', 'string'],
            'billing_city' => ['nullable', 'string', 'max:100'],
            'billing_state' => ['nullable', 'string', 'max:100'],
            'billing_country' => ['nullable', 'string', 'max:100'],
            'billing_postal_code' => ['nullable', 'string', 'max:20'],
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($company->logo && \Storage::disk('public')->exists($company->logo)) {
                \Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = $request->file('logo')->store('companies', 'public');
        }

        $company->update($validated);

        return redirect()
            ->route('client.company.show')
            ->with('success', 'Company information updated successfully.');
    }
}
