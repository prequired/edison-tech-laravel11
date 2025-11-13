<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Task Controller Feature Tests
 *
 * Tests the admin task controller CRUD operations including:
 * - Listing tasks with filtering
 * - Creating tasks
 * - Viewing task details
 * - Updating tasks
 * - Deleting tasks
 * - Assigning tasks
 * - Status updates
 * - Authorization checks
 */
class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $client;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->client = User::factory()->create(['role' => 'client', 'is_active' => true]);
        $this->project = Project::factory()->create();
    }

    /**
     * Test admin can view tasks index
     */
    public function test_admin_can_view_tasks_index(): void
    {
        Task::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.tasks.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.tasks.index');
        $response->assertViewHas('tasks');
    }

    /**
     * Test client cannot access admin tasks index
     */
    public function test_client_cannot_access_admin_tasks_index(): void
    {
        $response = $this->actingAs($this->client)->get(route('admin.tasks.index'));

        $response->assertStatus(403);
    }

    /**
     * Test guest cannot access admin tasks index
     */
    public function test_guest_cannot_access_admin_tasks_index(): void
    {
        $response = $this->get(route('admin.tasks.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test admin can view create task form
     */
    public function test_admin_can_view_create_task_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.tasks.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.tasks.create');
        $response->assertSee('Create Task');
    }

    /**
     * Test admin can create task
     */
    public function test_admin_can_create_task(): void
    {
        $user = User::factory()->create();

        $taskData = [
            'project_id' => $this->project->id,
            'title' => 'New Task',
            'description' => 'Task description',
            'assigned_to' => $user->id,
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => '2025-12-31',
            'estimated_hours' => 8.5,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.tasks.store'), $taskData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', [
            'title' => 'New Task',
            'project_id' => $this->project->id,
        ]);
    }

    /**
     * Test create task validation requires title
     */
    public function test_create_task_requires_title(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.tasks.store'), [
                'project_id' => $this->project->id,
                'status' => 'pending',
                'priority' => 'medium',
            ]);

        $response->assertSessionHasErrors('title');
    }

    /**
     * Test create task validation requires project
     */
    public function test_create_task_requires_project(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.tasks.store'), [
                'title' => 'Test Task',
                'status' => 'pending',
                'priority' => 'medium',
            ]);

        $response->assertSessionHasErrors('project_id');
    }

    /**
     * Test create task validation requires status
     */
    public function test_create_task_requires_status(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.tasks.store'), [
                'project_id' => $this->project->id,
                'title' => 'Test Task',
                'priority' => 'medium',
            ]);

        $response->assertSessionHasErrors('status');
    }

    /**
     * Test create task validation requires priority
     */
    public function test_create_task_requires_priority(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.tasks.store'), [
                'project_id' => $this->project->id,
                'title' => 'Test Task',
                'status' => 'pending',
            ]);

        $response->assertSessionHasErrors('priority');
    }

    /**
     * Test status must be valid
     */
    public function test_status_must_be_valid(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.tasks.store'), [
                'project_id' => $this->project->id,
                'title' => 'Test Task',
                'status' => 'invalid_status',
                'priority' => 'medium',
            ]);

        $response->assertSessionHasErrors('status');
    }

    /**
     * Test priority must be valid
     */
    public function test_priority_must_be_valid(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.tasks.store'), [
                'project_id' => $this->project->id,
                'title' => 'Test Task',
                'status' => 'pending',
                'priority' => 'invalid_priority',
            ]);

        $response->assertSessionHasErrors('priority');
    }

    /**
     * Test admin can view task details
     */
    public function test_admin_can_view_task_details(): void
    {
        $task = Task::factory()->create([
            'title' => 'Detail Test Task',
            'project_id' => $this->project->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.tasks.show', $task));

        $response->assertStatus(200);
        $response->assertViewIs('admin.tasks.show');
        $response->assertViewHas('task');
        $response->assertSee('Detail Test Task');
    }

    /**
     * Test task details show time entries
     */
    public function test_task_details_show_time_entries(): void
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);
        TimeEntry::factory()->count(3)->create(['task_id' => $task->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.tasks.show', $task));

        $response->assertStatus(200);
        $response->assertViewHas('totalHours');
        $response->assertViewHas('remainingHours');
    }

    /**
     * Test admin can view edit task form
     */
    public function test_admin_can_view_edit_task_form(): void
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.tasks.edit', $task));

        $response->assertStatus(200);
        $response->assertViewIs('admin.tasks.edit');
        $response->assertViewHas('task');
    }

    /**
     * Test admin can update task
     */
    public function test_admin_can_update_task(): void
    {
        $task = Task::factory()->create([
            'title' => 'Original Title',
            'status' => 'pending',
            'project_id' => $this->project->id,
        ]);

        $updateData = [
            'project_id' => $this->project->id,
            'title' => 'Updated Title',
            'status' => 'in_progress',
            'priority' => 'high',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.tasks.update', $task), $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'status' => 'in_progress',
        ]);
    }

    /**
     * Test admin can delete task
     */
    public function test_admin_can_delete_task(): void
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.tasks.destroy', $task));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('tasks', [
            'id' => $task->id,
        ]);
    }

    /**
     * Test tasks index can search by title
     */
    public function test_tasks_index_can_search(): void
    {
        Task::factory()->create([
            'title' => 'Design Homepage',
            'project_id' => $this->project->id,
        ]);

        Task::factory()->create([
            'title' => 'Write Documentation',
            'project_id' => $this->project->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.tasks.index', ['search' => 'Design']));

        $response->assertStatus(200);
        $response->assertSee('Design Homepage');
        $response->assertDontSee('Write Documentation');
    }

    /**
     * Test tasks index can filter by status
     */
    public function test_tasks_index_can_filter_by_status(): void
    {
        Task::factory()->create([
            'title' => 'Pending Task',
            'status' => 'pending',
            'project_id' => $this->project->id,
        ]);

        Task::factory()->create([
            'title' => 'Completed Task',
            'status' => 'completed',
            'project_id' => $this->project->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.tasks.index', ['status' => 'pending']));

        $response->assertStatus(200);
        $response->assertSee('Pending Task');
        $response->assertDontSee('Completed Task');
    }

    /**
     * Test tasks index can filter by priority
     */
    public function test_tasks_index_can_filter_by_priority(): void
    {
        Task::factory()->create([
            'title' => 'High Priority Task',
            'priority' => 'high',
            'project_id' => $this->project->id,
        ]);

        Task::factory()->create([
            'title' => 'Low Priority Task',
            'priority' => 'low',
            'project_id' => $this->project->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.tasks.index', ['priority' => 'high']));

        $response->assertStatus(200);
        $response->assertSee('High Priority Task');
        $response->assertDontSee('Low Priority Task');
    }

    /**
     * Test tasks index can filter by project
     */
    public function test_tasks_index_can_filter_by_project(): void
    {
        $project2 = Project::factory()->create();

        Task::factory()->create([
            'title' => 'Project 1 Task',
            'project_id' => $this->project->id,
        ]);

        Task::factory()->create([
            'title' => 'Project 2 Task',
            'project_id' => $project2->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.tasks.index', ['project_id' => $this->project->id]));

        $response->assertStatus(200);
        $response->assertSee('Project 1 Task');
        $response->assertDontSee('Project 2 Task');
    }

    /**
     * Test tasks index can filter by assignee
     */
    public function test_tasks_index_can_filter_by_assignee(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Task::factory()->create([
            'title' => 'User 1 Task',
            'assigned_to' => $user1->id,
            'project_id' => $this->project->id,
        ]);

        Task::factory()->create([
            'title' => 'User 2 Task',
            'assigned_to' => $user2->id,
            'project_id' => $this->project->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.tasks.index', ['assignee_id' => $user1->id]));

        $response->assertStatus(200);
        $response->assertSee('User 1 Task');
        $response->assertDontSee('User 2 Task');
    }

    /**
     * Test tasks index can filter by due date range
     */
    public function test_tasks_index_can_filter_by_due_date_range(): void
    {
        Task::factory()->create([
            'title' => 'Early Task',
            'due_date' => '2025-01-15',
            'project_id' => $this->project->id,
        ]);

        Task::factory()->create([
            'title' => 'Late Task',
            'due_date' => '2025-12-31',
            'project_id' => $this->project->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.tasks.index', [
                'from_date' => '2025-12-01',
                'to_date' => '2025-12-31',
            ]));

        $response->assertStatus(200);
        $response->assertSee('Late Task');
        $response->assertDontSee('Early Task');
    }

    /**
     * Test tasks index is paginated
     */
    public function test_tasks_index_is_paginated(): void
    {
        Task::factory()->count(20)->create(['project_id' => $this->project->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.tasks.index'));

        $response->assertStatus(200);
        $response->assertViewHas('tasks');

        $tasks = $response->viewData('tasks');
        $this->assertLessThanOrEqual(15, $tasks->count());
    }

    /**
     * Test admin can assign task to user
     */
    public function test_admin_can_assign_task(): void
    {
        $task = Task::factory()->create([
            'assigned_to' => null,
            'project_id' => $this->project->id,
        ]);
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.tasks.assign', $task), [
                'assigned_to' => $user->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $task->refresh();
        $this->assertEquals($user->id, $task->assigned_to);
    }

    /**
     * Test admin can update task status
     */
    public function test_admin_can_update_task_status(): void
    {
        $task = Task::factory()->create([
            'status' => 'pending',
            'project_id' => $this->project->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.tasks.update-status', $task), [
                'status' => 'completed',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $task->refresh();
        $this->assertEquals('completed', $task->status);
    }

    /**
     * Test different task statuses can be set
     */
    public function test_different_task_statuses_can_be_set(): void
    {
        $statuses = ['pending', 'in_progress', 'completed', 'on_hold', 'cancelled'];

        foreach ($statuses as $status) {
            $taskData = [
                'project_id' => $this->project->id,
                'title' => "Task - {$status}",
                'status' => $status,
                'priority' => 'medium',
            ];

            $response = $this->actingAs($this->admin)
                ->post(route('admin.tasks.store'), $taskData);

            $response->assertRedirect();

            $this->assertDatabaseHas('tasks', [
                'title' => "Task - {$status}",
                'status' => $status,
            ]);
        }
    }

    /**
     * Test different task priorities can be set
     */
    public function test_different_task_priorities_can_be_set(): void
    {
        $priorities = ['low', 'medium', 'high', 'urgent'];

        foreach ($priorities as $priority) {
            $taskData = [
                'project_id' => $this->project->id,
                'title' => "Task - {$priority}",
                'status' => 'pending',
                'priority' => $priority,
            ];

            $response = $this->actingAs($this->admin)
                ->post(route('admin.tasks.store'), $taskData);

            $response->assertRedirect();

            $this->assertDatabaseHas('tasks', [
                'title' => "Task - {$priority}",
                'priority' => $priority,
            ]);
        }
    }

    /**
     * Test task can be created without assignee
     */
    public function test_task_can_be_created_without_assignee(): void
    {
        $taskData = [
            'project_id' => $this->project->id,
            'title' => 'Unassigned Task',
            'status' => 'pending',
            'priority' => 'medium',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.tasks.store'), $taskData);

        $response->assertRedirect();

        $this->assertDatabaseHas('tasks', [
            'title' => 'Unassigned Task',
            'assigned_to' => null,
        ]);
    }

    /**
     * Test task can be created without due date
     */
    public function test_task_can_be_created_without_due_date(): void
    {
        $taskData = [
            'project_id' => $this->project->id,
            'title' => 'No Due Date Task',
            'status' => 'pending',
            'priority' => 'medium',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.tasks.store'), $taskData);

        $response->assertRedirect();

        $this->assertDatabaseHas('tasks', [
            'title' => 'No Due Date Task',
            'due_date' => null,
        ]);
    }

    /**
     * Test estimated hours must be numeric
     */
    public function test_estimated_hours_must_be_numeric(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.tasks.store'), [
                'project_id' => $this->project->id,
                'title' => 'Test Task',
                'status' => 'pending',
                'priority' => 'medium',
                'estimated_hours' => 'not-a-number',
            ]);

        $response->assertSessionHasErrors('estimated_hours');
    }

    /**
     * Test estimated hours must be positive
     */
    public function test_estimated_hours_must_be_positive(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.tasks.store'), [
                'project_id' => $this->project->id,
                'title' => 'Test Task',
                'status' => 'pending',
                'priority' => 'medium',
                'estimated_hours' => -5,
            ]);

        $response->assertSessionHasErrors('estimated_hours');
    }

    /**
     * Test create form can preselect project
     */
    public function test_create_form_can_preselect_project(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.tasks.create', ['project_id' => $this->project->id]));

        $response->assertStatus(200);
        $response->assertViewHas('selectedProjectId', $this->project->id);
    }
}
