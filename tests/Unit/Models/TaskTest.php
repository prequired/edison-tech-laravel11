<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Task Model Unit Tests
 *
 * Tests the Task model including:
 * - Factory creation
 * - Relationships (project, assignedTo, creator, timeEntries)
 * - Mass assignment
 * - Type casting
 * - Status and priority handling
 * - Date handling
 * - Soft deletes
 * - Business logic
 */
class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test task can be created using factory
     */
    public function test_task_can_be_created_using_factory(): void
    {
        $task = Task::factory()->create();

        $this->assertInstanceOf(Task::class, $task);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
        ]);
    }

    /**
     * Test task belongs to project
     */
    public function test_task_belongs_to_project(): void
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);

        $this->assertInstanceOf(Project::class, $task->project);
        $this->assertEquals($project->id, $task->project->id);
    }

    /**
     * Test task belongs to assigned user
     */
    public function test_task_belongs_to_assigned_user(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['assigned_to' => $user->id]);

        $this->assertInstanceOf(User::class, $task->assignedTo);
        $this->assertEquals($user->id, $task->assignedTo->id);
    }

    /**
     * Test task can be unassigned
     */
    public function test_task_can_be_unassigned(): void
    {
        $task = Task::factory()->create(['assigned_to' => null]);

        $this->assertNull($task->assigned_to);
        $this->assertNull($task->assignedTo);
    }

    /**
     * Test task belongs to creator
     */
    public function test_task_belongs_to_creator(): void
    {
        $creator = User::factory()->create();
        $task = Task::factory()->create(['created_by' => $creator->id]);

        $this->assertInstanceOf(User::class, $task->creator);
        $this->assertEquals($creator->id, $task->creator->id);
    }

    /**
     * Test task has many time entries
     */
    public function test_task_has_many_time_entries(): void
    {
        $task = Task::factory()->create();
        TimeEntry::factory()->count(3)->create(['task_id' => $task->id]);

        $this->assertCount(3, $task->timeEntries);
        $this->assertInstanceOf(TimeEntry::class, $task->timeEntries->first());
    }

    /**
     * Test task with no time entries
     */
    public function test_task_with_no_time_entries(): void
    {
        $task = Task::factory()->create();

        $this->assertCount(0, $task->timeEntries);
    }

    /**
     * Test task status can be set
     */
    public function test_task_status_can_be_set(): void
    {
        $task = Task::factory()->create(['status' => 'in_progress']);

        $this->assertEquals('in_progress', $task->status);
    }

    /**
     * Test task priority can be set
     */
    public function test_task_priority_can_be_set(): void
    {
        $task = Task::factory()->create(['priority' => 'high']);

        $this->assertEquals('high', $task->priority);
    }

    /**
     * Test different task statuses
     */
    public function test_different_task_statuses(): void
    {
        $statuses = ['pending', 'in_progress', 'completed', 'blocked', 'cancelled'];

        foreach ($statuses as $status) {
            $task = Task::factory()->create(['status' => $status]);
            $this->assertEquals($status, $task->status);
        }
    }

    /**
     * Test different task priorities
     */
    public function test_different_task_priorities(): void
    {
        $priorities = ['low', 'medium', 'high', 'urgent'];

        foreach ($priorities as $priority) {
            $task = Task::factory()->create(['priority' => $priority]);
            $this->assertEquals($priority, $task->priority);
        }
    }

    /**
     * Test due date is cast to date
     */
    public function test_due_date_is_cast_to_date(): void
    {
        $task = Task::factory()->create([
            'due_date' => '2025-12-31',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $task->due_date);
        $this->assertEquals('2025-12-31', $task->due_date->format('Y-m-d'));
    }

    /**
     * Test due date can be null
     */
    public function test_due_date_can_be_null(): void
    {
        $task = Task::factory()->create(['due_date' => null]);

        $this->assertNull($task->due_date);
    }

    /**
     * Test completed at is cast to date
     */
    public function test_completed_at_is_cast_to_date(): void
    {
        $task = Task::factory()->create([
            'completed_at' => '2025-01-15',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $task->completed_at);
        $this->assertEquals('2025-01-15', $task->completed_at->format('Y-m-d'));
    }

    /**
     * Test completed at can be null
     */
    public function test_completed_at_can_be_null(): void
    {
        $task = Task::factory()->create(['completed_at' => null]);

        $this->assertNull($task->completed_at);
    }

    /**
     * Test estimated hours is cast to decimal
     */
    public function test_estimated_hours_is_cast_to_decimal(): void
    {
        $task = Task::factory()->create(['estimated_hours' => 8.5]);

        $this->assertEquals('8.50', $task->estimated_hours);
        $this->assertIsString($task->estimated_hours);
    }

    /**
     * Test estimated hours can be null
     */
    public function test_estimated_hours_can_be_null(): void
    {
        $task = Task::factory()->create(['estimated_hours' => null]);

        $this->assertNull($task->estimated_hours);
    }

    /**
     * Test estimated hours precision
     */
    public function test_estimated_hours_precision(): void
    {
        $task = Task::factory()->create(['estimated_hours' => 12.345]);

        // Should be rounded to 2 decimal places
        $this->assertEquals('12.35', $task->estimated_hours);
    }

    /**
     * Test sort order is cast to integer
     */
    public function test_sort_order_is_cast_to_integer(): void
    {
        $task = Task::factory()->create(['sort_order' => 5]);

        $this->assertIsInt($task->sort_order);
        $this->assertEquals(5, $task->sort_order);
    }

    /**
     * Test sort order defaults
     */
    public function test_sort_order_defaults(): void
    {
        $task = Task::factory()->create();

        $this->assertIsInt($task->sort_order);
        $this->assertGreaterThanOrEqual(0, $task->sort_order);
    }

    /**
     * Test task can have description
     */
    public function test_task_can_have_description(): void
    {
        $task = Task::factory()->create([
            'description' => 'This is a detailed task description',
        ]);

        $this->assertEquals('This is a detailed task description', $task->description);
    }

    /**
     * Test task description can be null
     */
    public function test_task_description_can_be_null(): void
    {
        $task = Task::factory()->create(['description' => null]);

        $this->assertNull($task->description);
    }

    /**
     * Test task can have notes
     */
    public function test_task_can_have_notes(): void
    {
        $task = Task::factory()->create([
            'notes' => 'Internal notes about this task',
        ]);

        $this->assertEquals('Internal notes about this task', $task->notes);
    }

    /**
     * Test task notes can be null
     */
    public function test_task_notes_can_be_null(): void
    {
        $task = Task::factory()->create(['notes' => null]);

        $this->assertNull($task->notes);
    }

    /**
     * Test task uses soft deletes
     */
    public function test_task_uses_soft_deletes(): void
    {
        $task = Task::factory()->create();

        $task->delete();

        $this->assertSoftDeleted('tasks', [
            'id' => $task->id,
        ]);

        $this->assertNotNull($task->fresh()->deleted_at);
    }

    /**
     * Test soft deleted tasks can be restored
     */
    public function test_soft_deleted_tasks_can_be_restored(): void
    {
        $task = Task::factory()->create();
        $task->delete();

        $task->restore();

        $this->assertNull($task->fresh()->deleted_at);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test task has timestamps
     */
    public function test_task_has_timestamps(): void
    {
        $task = Task::factory()->create();

        $this->assertNotNull($task->created_at);
        $this->assertNotNull($task->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $task->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $task->updated_at);
    }

    /**
     * Test task mass assignment
     */
    public function test_task_mass_assignment(): void
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();
        $creator = User::factory()->create();

        $task = Task::create([
            'project_id' => $project->id,
            'assigned_to' => $user->id,
            'created_by' => $creator->id,
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => '2025-12-31',
            'estimated_hours' => 16.5,
            'sort_order' => 1,
            'notes' => 'Test notes',
        ]);

        $this->assertEquals('Test Task', $task->title);
        $this->assertEquals('Test Description', $task->description);
        $this->assertEquals('pending', $task->status);
        $this->assertEquals('medium', $task->priority);
    }

    /**
     * Test task can be updated
     */
    public function test_task_can_be_updated(): void
    {
        $task = Task::factory()->create([
            'title' => 'Original Title',
            'status' => 'pending',
        ]);

        $task->update([
            'title' => 'Updated Title',
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->assertEquals('Updated Title', $task->title);
        $this->assertEquals('completed', $task->status);
        $this->assertNotNull($task->completed_at);
    }

    /**
     * Test overdue task identification
     */
    public function test_overdue_task_identification(): void
    {
        $overdueTask = Task::factory()->create([
            'due_date' => now()->subDays(5),
            'status' => 'pending',
        ]);

        $futureTask = Task::factory()->create([
            'due_date' => now()->addDays(5),
            'status' => 'pending',
        ]);

        $this->assertTrue($overdueTask->due_date->isPast());
        $this->assertTrue($futureTask->due_date->isFuture());
    }

    /**
     * Test completed task has completed at date
     */
    public function test_completed_task_has_completed_at_date(): void
    {
        $task = Task::factory()->create([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->assertEquals('completed', $task->status);
        $this->assertNotNull($task->completed_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $task->completed_at);
    }

    /**
     * Test pending task has no completed at date
     */
    public function test_pending_task_has_no_completed_at_date(): void
    {
        $task = Task::factory()->create([
            'status' => 'pending',
            'completed_at' => null,
        ]);

        $this->assertEquals('pending', $task->status);
        $this->assertNull($task->completed_at);
    }

    /**
     * Test task can be reassigned
     */
    public function test_task_can_be_reassigned(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $task = Task::factory()->create(['assigned_to' => $user1->id]);
        $this->assertEquals($user1->id, $task->assigned_to);

        $task->update(['assigned_to' => $user2->id]);
        $this->assertEquals($user2->id, $task->assigned_to);
    }

    /**
     * Test task creator cannot be null
     */
    public function test_task_creator_cannot_be_null(): void
    {
        $this->expectException(\Exception::class);

        Task::factory()->create(['created_by' => null]);
    }

    /**
     * Test multiple tasks in same project
     */
    public function test_multiple_tasks_in_same_project(): void
    {
        $project = Project::factory()->create();

        Task::factory()->count(5)->create(['project_id' => $project->id]);

        $this->assertCount(5, $project->tasks);
    }

    /**
     * Test task sorting by sort order
     */
    public function test_task_sorting_by_sort_order(): void
    {
        $project = Project::factory()->create();

        Task::factory()->create(['project_id' => $project->id, 'sort_order' => 3]);
        Task::factory()->create(['project_id' => $project->id, 'sort_order' => 1]);
        Task::factory()->create(['project_id' => $project->id, 'sort_order' => 2]);

        $tasks = Task::where('project_id', $project->id)
            ->orderBy('sort_order')
            ->get();

        $this->assertEquals(1, $tasks[0]->sort_order);
        $this->assertEquals(2, $tasks[1]->sort_order);
        $this->assertEquals(3, $tasks[2]->sort_order);
    }
}
