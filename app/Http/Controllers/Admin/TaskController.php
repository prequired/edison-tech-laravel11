<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Task Controller
 *
 * Manages CRUD operations for tasks in the admin panel.
 */
class TaskController extends Controller
{
    /**
     * Display a listing of tasks.
     *
     * Supports filtering by:
     * - status
     * - priority
     * - project_id
     * - assignee_id
     * - due_date_range (from, to)
     * - search (title, description)
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Task::class);

        $query = Task::with(['project.company', 'assignee']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by assignee
        if ($request->filled('assignee_id')) {
            $query->where('assigned_to', $request->assignee_id);
        }

        // Filter by due date range
        if ($request->filled('from_date')) {
            $query->whereDate('due_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('due_date', '<=', $request->to_date);
        }

        // Search by title or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort by due date, then priority
        $query->orderBy('due_date', 'asc')->orderBy('priority', 'desc');

        $tasks = $query->paginate(15)->withQueryString();

        // Get projects and users for filter dropdowns
        $projects = Project::orderBy('name')->get(['id', 'name']);
        $users = User::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.tasks.index', compact('tasks', 'projects', 'users'));
    }

    /**
     * Show the form for creating a new task.
     *
     * @param Request $request
     * @return View
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Task::class);

        $projects = Project::with('company')->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        // Pre-select project if passed via query params
        $selectedProjectId = $request->query('project_id');

        return view('admin.tasks.create', compact('projects', 'users', 'selectedProjectId'));
    }

    /**
     * Store a newly created task in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Task::class);

        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'status' => ['required', 'string', 'in:pending,in_progress,completed,on_hold,cancelled'],
            'priority' => ['required', 'string', 'in:low,medium,high,urgent'],
            'due_date' => ['nullable', 'date'],
            'estimated_hours' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $task = Task::create($validated);

            return redirect()
                ->route('admin.tasks.show', $task)
                ->with('success', 'Task created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create task: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified task.
     *
     * @param Task $task
     * @return View
     */
    public function show(Task $task): View
    {
        $this->authorize('view', $task);

        $task->load([
            'project.company',
            'assignee',
            'timeEntries' => function ($query) {
                $query->with('user')->latest();
            }
        ]);

        // Calculate task statistics
        $totalHours = $task->timeEntries->sum('duration');
        $remainingHours = $task->estimated_hours ? $task->estimated_hours - $totalHours : null;

        return view('admin.tasks.show', compact('task', 'totalHours', 'remainingHours'));
    }

    /**
     * Show the form for editing the specified task.
     *
     * @param Task $task
     * @return View
     */
    public function edit(Task $task): View
    {
        $this->authorize('update', $task);

        $task->load(['project', 'assignee']);

        $projects = Project::with('company')->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.tasks.edit', compact('task', 'projects', 'users'));
    }

    /**
     * Update the specified task in storage.
     *
     * @param Request $request
     * @param Task $task
     * @return RedirectResponse
     */
    public function update(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'status' => ['required', 'string', 'in:pending,in_progress,completed,on_hold,cancelled'],
            'priority' => ['required', 'string', 'in:low,medium,high,urgent'],
            'due_date' => ['nullable', 'date'],
            'estimated_hours' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $task->update($validated);

            return redirect()
                ->route('admin.tasks.show', $task)
                ->with('success', 'Task updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update task: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified task from storage.
     *
     * @param Task $task
     * @return RedirectResponse
     */
    public function destroy(Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);

        try {
            $task->delete();

            return redirect()
                ->route('admin.tasks.index')
                ->with('success', 'Task deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete task: ' . $e->getMessage());
        }
    }

    /**
     * Assign the specified task to a user.
     *
     * @param Request $request
     * @param Task $task
     * @return RedirectResponse
     */
    public function assign(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $request->validate([
            'assigned_to' => ['required', 'exists:users,id'],
        ]);

        try {
            $task->update(['assigned_to' => $request->assigned_to]);

            return redirect()
                ->back()
                ->with('success', 'Task assigned successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to assign task: ' . $e->getMessage());
        }
    }

    /**
     * Update the status of the specified task.
     *
     * @param Request $request
     * @param Task $task
     * @return RedirectResponse
     */
    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $request->validate([
            'status' => ['required', 'string', 'in:pending,in_progress,completed,on_hold,cancelled'],
        ]);

        try {
            $task->update(['status' => $request->status]);

            return redirect()
                ->back()
                ->with('success', 'Task status updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update task status: ' . $e->getMessage());
        }
    }
}
