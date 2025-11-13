@extends('layouts.admin')

@section('title', 'Financial Analytics')
@section('header', 'Financial Analytics')

@section('content')
    <!-- Revenue Overview -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-4 mb-6">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="text-sm font-medium text-gray-500">Total Revenue</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">${{ number_format($totalRevenue, 2) }}</dd>
            <dd class="mt-1 text-sm text-gray-500">All time</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="text-sm font-medium text-gray-500">This Month</dt>
            <dd class="mt-1 text-3xl font-semibold text-green-600">${{ number_format($monthlyRevenue, 2) }}</dd>
            <dd class="mt-1 text-sm text-green-600">{{ $monthlyGrowth }}% from last month</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="text-sm font-medium text-gray-500">Pending</dt>
            <dd class="mt-1 text-3xl font-semibold text-yellow-600">${{ number_format($pendingRevenue, 2) }}</dd>
            <dd class="mt-1 text-sm text-gray-500">{{ $pendingInvoiceCount }} invoices</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="text-sm font-medium text-gray-500">Overdue</dt>
            <dd class="mt-1 text-3xl font-semibold text-red-600">${{ number_format($overdueRevenue, 2) }}</dd>
            <dd class="mt-1 text-sm text-gray-500">{{ $overdueInvoiceCount }} invoices</dd>
        </div>
    </div>

    <!-- Payment Method Breakdown -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-6">
        <div class="rounded-lg bg-white shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue by Payment Method</h3>
            <dl class="space-y-3">
                @foreach($paymentMethods ?? [] as $method => $amount)
                    <div class="flex items-center justify-between">
                        <dt class="text-sm font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $method)) }}</dt>
                        <dd class="text-sm font-semibold text-gray-900">${{ number_format($amount, 2) }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        <div class="rounded-lg bg-white shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Top Clients by Revenue</h3>
            <dl class="space-y-3">
                @forelse($topClients ?? [] as $client)
                    <div class="flex items-center justify-between">
                        <dt class="text-sm font-medium text-gray-700">{{ $client->name }}</dt>
                        <dd class="text-sm font-semibold text-gray-900">${{ number_format($client->total_revenue ?? 0, 2) }}</dd>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No data available</p>
                @endforelse
            </dl>
        </div>
    </div>

    <!-- Recent Invoices -->
    <div class="rounded-lg bg-white shadow">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-lg font-medium text-gray-900">Recent Invoices</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Invoice #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Company</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($recentInvoices ?? [] as $invoice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $invoice->company->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">${{ number_format($invoice->total_amount, 2) }}</td>
                        <td class="px-6 py-4"><span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $invoice->status->badge() }}">{{ $invoice->status->label() }}</span></td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $invoice->issue_date->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">No invoices found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
