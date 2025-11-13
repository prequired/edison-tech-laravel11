<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use App\Models\Company;
use App\Models\ContactSubmission;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Analytics Controller
 *
 * Provides comprehensive analytics and statistics for the admin dashboard.
 */
class AnalyticsController extends Controller
{
    /**
     * Display the analytics dashboard.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // Date range filter (default to current month)
        $startDate = $request->filled('start_date')
            ? \Carbon\Carbon::parse($request->start_date)
            : now()->startOfMonth();

        $endDate = $request->filled('end_date')
            ? \Carbon\Carbon::parse($request->end_date)
            : now()->endOfMonth();

        // Project Statistics
        $projectStats = [
            'total' => Project::count(),
            'in_progress' => Project::where('status', 'in_progress')->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'on_hold' => Project::where('status', 'on_hold')->count(),
            'planning' => Project::where('status', 'planning')->count(),
        ];

        // Financial Statistics
        $financialStats = [
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'pending_revenue' => Invoice::where('status', 'pending')->sum('total_amount'),
            'overdue_revenue' => Invoice::where('status', 'overdue')->sum('total_amount'),
            'this_month_revenue' => Payment::where('status', 'completed')
                ->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->sum('amount'),
        ];

        // Task Statistics
        $taskStats = [
            'total' => Task::count(),
            'todo' => Task::where('status', 'todo')->count(),
            'in_progress' => Task::where('status', 'in_progress')->count(),
            'completed' => Task::where('status', 'completed')->count(),
            'overdue' => Task::where('status', '!=', 'completed')
                ->where('due_date', '<', now())
                ->count(),
        ];

        // Time Tracking Statistics
        $timeStats = [
            'total_hours' => TimeEntry::sum('hours'),
            'billable_hours' => TimeEntry::where('is_billable', true)->sum('hours'),
            'this_month_hours' => TimeEntry::whereBetween('date', [$startDate, $endDate])->sum('hours'),
            'total_billable_amount' => TimeEntry::where('is_billable', true)->sum('billable_amount'),
        ];

        // User Statistics
        $userStats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'admins' => User::where('role', 'admin')->count(),
            'employees' => User::where('role', 'employee')->count(),
            'clients' => User::where('role', 'client')->count(),
        ];

        // Company Statistics
        $companyStats = [
            'total' => Company::count(),
            'active' => Company::where('is_active', true)->count(),
            'with_active_projects' => Company::whereHas('projects', function ($query) {
                $query->whereIn('status', ['planning', 'in_progress']);
            })->count(),
        ];

        // Contact & Lead Statistics
        $contactStats = [
            'total_submissions' => ContactSubmission::count(),
            'unread' => ContactSubmission::where('status', 'unread')->count(),
            'this_month' => ContactSubmission::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        // Blog Statistics
        $blogStats = [
            'total_posts' => BlogPost::count(),
            'published' => BlogPost::where('status', 'published')->count(),
            'drafts' => BlogPost::where('status', 'draft')->count(),
            'this_month' => BlogPost::where('status', 'published')
                ->whereMonth('published_at', now()->month)
                ->whereYear('published_at', now()->year)
                ->count(),
        ];

        // Revenue by Month (last 12 months)
        $revenueByMonth = Payment::where('status', 'completed')
            ->where('payment_date', '>=', now()->subMonths(12))
            ->select(
                DB::raw('DATE_FORMAT(payment_date, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top Projects by Revenue
        $topProjects = Project::select('projects.*')
            ->selectRaw('COALESCE(SUM(payments.amount), 0) as total_revenue')
            ->leftJoin('invoices', 'projects.id', '=', 'invoices.project_id')
            ->leftJoin('payments', function ($join) {
                $join->on('invoices.id', '=', 'payments.invoice_id')
                    ->where('payments.status', '=', 'completed');
            })
            ->groupBy('projects.id')
            ->orderByDesc('total_revenue')
            ->take(10)
            ->get();

        // Recent Activity (latest invoices, payments, projects)
        $recentInvoices = Invoice::with('company')
            ->latest()
            ->take(5)
            ->get();

        $recentPayments = Payment::with('invoice.company')
            ->latest('payment_date')
            ->take(5)
            ->get();

        $recentProjects = Project::with('company')
            ->latest()
            ->take(5)
            ->get();

        // Project completion rate
        $completedProjectsCount = Project::where('status', 'completed')->count();
        $totalProjectsCount = Project::count();
        $projectCompletionRate = $totalProjectsCount > 0
            ? round(($completedProjectsCount / $totalProjectsCount) * 100, 2)
            : 0;

        // Task completion rate
        $completedTasksCount = Task::where('status', 'completed')->count();
        $totalTasksCount = Task::count();
        $taskCompletionRate = $totalTasksCount > 0
            ? round(($completedTasksCount / $totalTasksCount) * 100, 2)
            : 0;

        return view('admin.analytics.index', compact(
            'projectStats',
            'financialStats',
            'taskStats',
            'timeStats',
            'userStats',
            'companyStats',
            'contactStats',
            'blogStats',
            'revenueByMonth',
            'topProjects',
            'recentInvoices',
            'recentPayments',
            'recentProjects',
            'projectCompletionRate',
            'taskCompletionRate',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display project analytics.
     *
     * @param Request $request
     * @return View
     */
    public function projects(Request $request): View
    {
        $startDate = $request->filled('start_date')
            ? \Carbon\Carbon::parse($request->start_date)
            : now()->subMonths(6);

        $endDate = $request->filled('end_date')
            ? \Carbon\Carbon::parse($request->end_date)
            : now();

        // Projects created over time
        $projectsByMonth = Project::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Projects by status
        $projectsByStatus = Project::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Average project duration
        $averageProjectDuration = Project::whereNotNull('start_date')
            ->whereNotNull('deadline')
            ->selectRaw('AVG(DATEDIFF(deadline, start_date)) as avg_duration')
            ->value('avg_duration');

        return view('admin.analytics.projects', compact(
            'projectsByMonth',
            'projectsByStatus',
            'averageProjectDuration',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display financial analytics.
     *
     * @param Request $request
     * @return View
     */
    public function financial(Request $request): View
    {
        $startDate = $request->filled('start_date')
            ? \Carbon\Carbon::parse($request->start_date)
            : now()->subMonths(12);

        $endDate = $request->filled('end_date')
            ? \Carbon\Carbon::parse($request->end_date)
            : now();

        // Revenue by payment method
        $revenueByMethod = Payment::where('status', 'completed')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get();

        // Invoice aging report
        $overdueInvoices = Invoice::where('status', 'overdue')
            ->selectRaw('
                CASE
                    WHEN DATEDIFF(NOW(), due_date) <= 30 THEN "0-30 days"
                    WHEN DATEDIFF(NOW(), due_date) <= 60 THEN "31-60 days"
                    WHEN DATEDIFF(NOW(), due_date) <= 90 THEN "61-90 days"
                    ELSE "90+ days"
                END as age_group,
                COUNT(*) as count,
                SUM(total_amount) as total
            ')
            ->groupBy('age_group')
            ->get();

        // Top clients by revenue
        $topClients = Company::select('companies.*')
            ->selectRaw('COALESCE(SUM(payments.amount), 0) as total_revenue')
            ->leftJoin('invoices', 'companies.id', '=', 'invoices.company_id')
            ->leftJoin('payments', function ($join) {
                $join->on('invoices.id', '=', 'payments.invoice_id')
                    ->where('payments.status', '=', 'completed');
            })
            ->whereBetween('payments.payment_date', [$startDate, $endDate])
            ->groupBy('companies.id')
            ->orderByDesc('total_revenue')
            ->take(10)
            ->get();

        return view('admin.analytics.financial', compact(
            'revenueByMethod',
            'overdueInvoices',
            'topClients',
            'startDate',
            'endDate'
        ));
    }
}
