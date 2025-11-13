<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Task;
use App\Enums\InvoiceStatus;
use App\Enums\ProjectStatus;
use Illuminate\View\View;

/**
 * Dashboard Controller
 *
 * Handles the admin dashboard display with statistics and recent activities.
 */
class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * Shows key statistics including:
     * - Total projects count and breakdown by status
     * - Total invoices count and revenue statistics
     * - Active clients count
     * - Recent activities and upcoming deadlines
     *
     * @return View
     */
    public function index(): View
    {
        // Projects statistics
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', ProjectStatus::IN_PROGRESS)->count();
        $completedProjects = Project::where('status', ProjectStatus::COMPLETED)->count();
        $onHoldProjects = Project::where('status', ProjectStatus::ON_HOLD)->count();

        // Invoices statistics
        $totalInvoices = Invoice::count();
        $paidInvoices = Invoice::where('status', InvoiceStatus::PAID)->count();
        $pendingInvoices = Invoice::where('status', InvoiceStatus::SENT)->count();
        $overdueInvoices = Invoice::where('status', InvoiceStatus::OVERDUE)->count();

        // Revenue statistics
        $totalRevenue = Invoice::where('status', InvoiceStatus::PAID)->sum('total_amount');
        $pendingRevenue = Invoice::whereIn('status', [InvoiceStatus::SENT, InvoiceStatus::OVERDUE])
            ->sum('total_amount');
        $monthlyRevenue = Invoice::where('status', InvoiceStatus::PAID)
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total_amount');

        // Active clients (companies with active projects)
        $activeClients = Company::whereHas('projects', function ($query) {
            $query->where('status', ProjectStatus::IN_PROGRESS);
        })->count();

        // Recent activities (last 10)
        $recentActivities = ActivityLog::with(['user', 'company'])
            ->latest()
            ->limit(10)
            ->get();

        // Upcoming deadlines (tasks and projects due in next 7 days)
        $upcomingTasks = Task::with(['project', 'assignee'])
            ->where('status', '!=', 'completed')
            ->whereBetween('due_date', [now(), now()->addDays(7)])
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        $upcomingProjects = Project::with(['company', 'creator'])
            ->whereNotIn('status', [ProjectStatus::COMPLETED, ProjectStatus::CANCELLED])
            ->whereBetween('end_date', [now(), now()->addDays(7)])
            ->orderBy('end_date')
            ->limit(10)
            ->get();

        // Recent projects (last 5)
        $recentProjects = Project::with(['company', 'creator'])
            ->latest()
            ->limit(5)
            ->get();

        // Recent invoices (last 5)
        $recentInvoices = Invoice::with(['company', 'project'])
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalProjects',
            'activeProjects',
            'completedProjects',
            'onHoldProjects',
            'totalInvoices',
            'paidInvoices',
            'pendingInvoices',
            'overdueInvoices',
            'totalRevenue',
            'pendingRevenue',
            'monthlyRevenue',
            'activeClients',
            'recentActivities',
            'upcomingTasks',
            'upcomingProjects',
            'recentProjects',
            'recentInvoices'
        ));
    }
}
