<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Project Controller
 *
 * Manages CRUD operations for projects in the admin panel.
 */
class ProjectController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ProjectService $projectService
     */
    public function __construct(
        private readonly ProjectService $projectService
    ) {}

    /**
     * Display a listing of projects.
     *
     * Supports filtering by:
     * - status
     * - priority
     * - company_id
     * - search (name, description)
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Project::class);

        $query = Project::with(['company', 'creator', 'assignee']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by company
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Search by name or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort by latest by default
        $query->latest();

        $projects = $query->paginate(15)->withQueryString();

        // Get companies for filter dropdown
        $companies = Company::orderBy('name')->get(['id', 'name']);

        return view('admin.projects.index', compact('projects', 'companies'));
    }

    /**
     * Show the form for creating a new project.
     *
     * @return View
     */
    public function create(): View
    {
        $this->authorize('create', Project::class);

        $companies = Company::orderBy('name')->get(['id', 'name']);
        $users = User::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.projects.create', compact('companies', 'users'));
    }

    /**
     * Store a newly created project in storage.
     *
     * @param StoreProjectRequest $request
     * @return RedirectResponse
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $this->authorize('create', Project::class);

        try {
            $project = $this->projectService->createProject($request->validated());

            return redirect()
                ->route('admin.projects.show', $project)
                ->with('success', 'Project created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create project: ' . $e->getMessage());
        }
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
            'assignee',
            'tasks' => function ($query) {
                $query->with('assignee')->latest();
            },
            'timeEntries' => function ($query) {
                $query->with('user')->latest()->limit(10);
            },
            'invoices' => function ($query) {
                $query->latest();
            },
            'documents' => function ($query) {
                $query->latest();
            },
            'statusHistories' => function ($query) {
                $query->with('user')->latest();
            }
        ]);

        // Calculate project statistics
        $totalHours = $project->timeEntries->sum('duration');
        $totalTasks = $project->tasks->count();
        $completedTasks = $project->tasks->where('status', 'completed')->count();
        $totalInvoiced = $project->invoices->sum('total_amount');

        return view('admin.projects.show', compact(
            'project',
            'totalHours',
            'totalTasks',
            'completedTasks',
            'totalInvoiced'
        ));
    }

    /**
     * Show the form for editing the specified project.
     *
     * @param Project $project
     * @return View
     */
    public function edit(Project $project): View
    {
        $this->authorize('update', $project);

        $project->load(['company', 'creator', 'assignee']);

        $companies = Company::orderBy('name')->get(['id', 'name']);
        $users = User::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.projects.edit', compact('project', 'companies', 'users'));
    }

    /**
     * Update the specified project in storage.
     *
     * @param UpdateProjectRequest $request
     * @param Project $project
     * @return RedirectResponse
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        try {
            $this->projectService->updateProject($project, $request->validated());

            return redirect()
                ->route('admin.projects.show', $project)
                ->with('success', 'Project updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update project: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified project from storage.
     *
     * @param Project $project
     * @return RedirectResponse
     */
    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        try {
            $this->projectService->deleteProject($project);

            return redirect()
                ->route('admin.projects.index')
                ->with('success', 'Project deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete project: ' . $e->getMessage());
        }
    }
}
