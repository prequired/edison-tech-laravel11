<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\Document;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Client Dashboard Controller
 *
 * Displays the client dashboard with overview of projects, invoices, and documents.
 */
class DashboardController extends Controller
{
    /**
     * Display the client dashboard.
     *
     * @return View
     */
    public function index(): View
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        // Get active projects
        $activeProjects = Project::where('company_id', $companyId)
            ->whereIn('status', ['planning', 'in_progress'])
            ->with('users')
            ->latest()
            ->take(5)
            ->get();

        // Get recent invoices
        $recentInvoices = Invoice::where('company_id', $companyId)
            ->latest('invoice_date')
            ->take(5)
            ->get();

        // Get pending invoices (unpaid)
        $pendingInvoices = Invoice::where('company_id', $companyId)
            ->whereIn('status', ['pending', 'overdue'])
            ->get();

        // Calculate total outstanding amount
        $totalOutstanding = $pendingInvoices->sum('total_amount');

        // Get recent documents
        $recentDocuments = Document::where('documentable_type', 'App\Models\Company')
            ->where('documentable_id', $companyId)
            ->orWhereHasMorph('documentable', [Project::class], function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->with('documentable')
            ->latest()
            ->take(5)
            ->get();

        // Get tasks assigned to the user
        $myTasks = Task::where('assigned_to', $user->id)
            ->whereIn('status', ['todo', 'in_progress'])
            ->with('project')
            ->latest()
            ->take(5)
            ->get();

        // Statistics
        $stats = [
            'total_projects' => Project::where('company_id', $companyId)->count(),
            'active_projects' => Project::where('company_id', $companyId)
                ->whereIn('status', ['planning', 'in_progress'])
                ->count(),
            'completed_projects' => Project::where('company_id', $companyId)
                ->where('status', 'completed')
                ->count(),
            'total_invoices' => Invoice::where('company_id', $companyId)->count(),
            'pending_invoices' => $pendingInvoices->count(),
            'total_outstanding' => $totalOutstanding,
            'my_tasks' => Task::where('assigned_to', $user->id)
                ->whereIn('status', ['todo', 'in_progress'])
                ->count(),
        ];

        return view('client.dashboard', compact(
            'activeProjects',
            'recentInvoices',
            'pendingInvoices',
            'recentDocuments',
            'myTasks',
            'stats'
        ));
    }
}
