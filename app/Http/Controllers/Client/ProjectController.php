<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Client Project Controller
 *
 * Allows clients to view their projects (read-only).
 */
class ProjectController extends Controller
{
    /**
     * Display a listing of the client's projects.
     *
     * @return View
     */
    public function index(): View
    {
        $companyId = Auth::user()->company_id;

        $projects = Project::where('company_id', $companyId)
            ->with(['users', 'tasks' => function ($query) {
                $query->latest()->take(5);
            }])
            ->withCount(['tasks', 'tasks as completed_tasks_count' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->latest()
            ->paginate(15);

        return view('client.projects.index', compact('projects'));
    }

    /**
     * Display the specified project.
     *
     * @param Project $project
     * @return View
     */
    public function show(Project $project): View
    {
        $this->authorize('view', $project);

        $project->load([
            'company',
            'creator',
            'users',
            'tasks' => function ($query) {
                $query->with('assignee')->latest();
            },
            'invoices' => function ($query) {
                $query->latest();
            },
            'contracts',
            'documents',
            'timeEntries' => function ($query) {
                $query->with('user')->latest()->take(10);
            },
        ]);

        // Calculate task completion percentage
        $totalTasks = $project->tasks->count();
        $completedTasks = $project->tasks->where('status', 'completed')->count();
        $taskCompletionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

        // Get status history
        $statusHistory = $project->statusHistories()
            ->with('changedBy')
            ->latest()
            ->take(10)
            ->get();

        return view('client.projects.show', compact('project', 'taskCompletionRate', 'statusHistory'));
    }
}
