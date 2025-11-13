<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Company;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalCompanies = Company::count();
        $activeProjects = Project::where('status', 'active')->count();
        $totalProjects = Project::count();

        $thisMonthRevenue = Payment::where('status', 'completed')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $lastMonthRevenue = Payment::where('status', 'completed')
            ->whereMonth('payment_date', now()->subMonth()->month)
            ->whereYear('payment_date', now()->subMonth()->year)
            ->sum('amount');

        $revenueChange = $lastMonthRevenue > 0
            ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : 0;

        $pendingInvoices = Invoice::whereIn('status', ['draft', 'sent'])->count();
        $overdueInvoices = Invoice::where('status', 'overdue')->count();

        $unpaidAmount = Invoice::whereIn('status', ['sent', 'overdue'])
            ->sum('balance');

        return [
            Stat::make('Total Companies', $totalCompanies)
                ->description('Active clients')
                ->descriptionIcon('heroicon-o-building-office-2')
                ->color('success')
                ->chart([7, 12, 15, 18, 22, 25, $totalCompanies]),

            Stat::make('Active Projects', $activeProjects)
                ->description("{$totalProjects} total projects")
                ->descriptionIcon('heroicon-o-briefcase')
                ->color('info')
                ->chart([5, 8, 12, 15, 18, 20, $activeProjects]),

            Stat::make('Revenue This Month', '$' . Number::format($thisMonthRevenue, precision: 2))
                ->description(($revenueChange >= 0 ? '+' : '') . number_format($revenueChange, 1) . '% from last month')
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger')
                ->chart([
                    $lastMonthRevenue * 0.7,
                    $lastMonthRevenue * 0.8,
                    $lastMonthRevenue * 0.9,
                    $lastMonthRevenue,
                    $thisMonthRevenue * 0.7,
                    $thisMonthRevenue * 0.85,
                    $thisMonthRevenue,
                ]),

            Stat::make('Pending Invoices', $pendingInvoices)
                ->description($overdueInvoices > 0 ? "{$overdueInvoices} overdue" : 'All on track')
                ->descriptionIcon($overdueInvoices > 0 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle')
                ->color($overdueInvoices > 0 ? 'warning' : 'success'),

            Stat::make('Unpaid Balance', '$' . Number::format($unpaidAmount, precision: 2))
                ->description('Outstanding receivables')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('warning'),
        ];
    }
}
