<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Models\Company;
use App\Models\Contract;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Project Lifecycle Integration Tests
 *
 * Tests the complete project lifecycle including:
 * - Project creation and setup
 * - Contract management
 * - Task management
 * - Time tracking
 * - Invoicing
 * - Payment processing
 * - Document management
 * - Project completion
 */
class ProjectLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $projectManager;
    private User $client;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->projectManager = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $this->client = User::factory()->create(['role' => 'client', 'is_active' => true]);
        $this->company = Company::factory()->create();

        Notification::fake();
    }

    /**
     * Test complete project lifecycle from creation to completion
     */
    public function test_complete_project_lifecycle(): void
    {
        // Step 1: Create project
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'name' => 'Website Redesign',
            'status' => 'planning',
            'budget' => 50000.00,
            'start_date' => now(),
            'end_date' => now()->addMonths(3),
        ]);

        $this->assertEquals('planning', $project->status);

        // Step 2: Create contract
        $contract = Contract::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $project->id,
            'contract_value' => 50000.00,
            'status' => 'draft',
        ]);

        $this->assertNotNull($project->contracts()->first());

        // Step 3: Activate contract and start project
        $contract->update(['status' => 'active']);
        $project->update(['status' => 'active']);

        $this->assertEquals('active', $project->fresh()->status);

        // Step 4: Create tasks
        $task1 = Task::factory()->create([
            'project_id' => $project->id,
            'title' => 'Design Homepage',
            'status' => 'pending',
            'priority' => 'high',
            'estimated_hours' => 40,
            'assigned_to' => $this->projectManager->id,
        ]);

        $task2 = Task::factory()->create([
            'project_id' => $project->id,
            'title' => 'Develop Frontend',
            'status' => 'pending',
            'priority' => 'high',
            'estimated_hours' => 80,
        ]);

        $this->assertCount(2, $project->tasks);

        // Step 5: Work on tasks and track time
        $task1->update(['status' => 'in_progress']);

        $timeEntry1 = TimeEntry::factory()->create([
            'project_id' => $project->id,
            'task_id' => $task1->id,
            'user_id' => $this->projectManager->id,
            'duration' => 8.0,
            'description' => 'Initial homepage design',
        ]);

        $this->assertEquals(8.0, $task1->timeEntries->sum('duration'));

        // Step 6: Complete first task
        $task1->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->assertEquals('completed', $task1->fresh()->status);

        // Step 7: Upload project documents
        $document = Document::factory()->create([
            'documentable_type' => Project::class,
            'documentable_id' => $project->id,
            'uploaded_by' => $this->admin->id,
            'name' => 'Design Mockups',
            'category' => 'design',
        ]);

        $this->assertCount(1, $project->documents);

        // Step 8: Create invoice for milestone
        $invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $project->id,
            'total_amount' => 20000.00,
            'status' => 'sent',
        ]);

        $this->assertNotNull($project->invoices()->first());

        // Step 9: Process payment
        $payment = Payment::factory()->create([
            'invoice_id' => $invoice->id,
            'amount' => 20000.00,
            'status' => 'completed',
            'payment_method' => 'bank_transfer',
        ]);

        $invoice->update([
            'paid_amount' => 20000.00,
            'balance' => 0.00,
            'status' => 'paid',
        ]);

        $this->assertEquals('paid', $invoice->fresh()->status);

        // Step 10: Complete remaining tasks
        $task2->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Step 11: Mark project as completed
        $project->update([
            'status' => 'completed',
            'actual_end_date' => now(),
        ]);

        $this->assertEquals('completed', $project->fresh()->status);

        // Verify final state
        $this->assertTrue($project->tasks->every(fn($task) => $task->status === 'completed'));
        $this->assertGreaterThan(0, $project->timeEntries->count());
        $this->assertGreaterThan(0, $project->documents->count());
        $this->assertGreaterThan(0, $project->invoices->count());
    }

    /**
     * Test project with multiple phases and milestones
     */
    public function test_project_with_multiple_phases(): void
    {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'active',
        ]);

        // Phase 1: Discovery (Tasks 1-3)
        $discoveryTasks = Task::factory()->count(3)->create([
            'project_id' => $project->id,
            'status' => 'completed',
            'priority' => 'high',
        ]);

        // Phase 2: Design (Tasks 4-6)
        $designTasks = Task::factory()->count(3)->create([
            'project_id' => $project->id,
            'status' => 'in_progress',
            'priority' => 'medium',
        ]);

        // Phase 3: Development (Tasks 7-10)
        $devTasks = Task::factory()->count(4)->create([
            'project_id' => $project->id,
            'status' => 'pending',
            'priority' => 'low',
        ]);

        $this->assertCount(10, $project->tasks);

        // Check task completion by status
        $completedCount = $project->tasks()->where('status', 'completed')->count();
        $inProgressCount = $project->tasks()->where('status', 'in_progress')->count();
        $pendingCount = $project->tasks()->where('status', 'pending')->count();

        $this->assertEquals(3, $completedCount);
        $this->assertEquals(3, $inProgressCount);
        $this->assertEquals(4, $pendingCount);
    }

    /**
     * Test project budget tracking
     */
    public function test_project_budget_tracking(): void
    {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'budget' => 10000.00,
            'status' => 'active',
        ]);

        // Track time entries
        TimeEntry::factory()->create([
            'project_id' => $project->id,
            'user_id' => $this->projectManager->id,
            'duration' => 20.0,
            'hourly_rate' => 100.00,
        ]);

        TimeEntry::factory()->create([
            'project_id' => $project->id,
            'user_id' => $this->projectManager->id,
            'duration' => 30.0,
            'hourly_rate' => 100.00,
        ]);

        $totalHours = $project->timeEntries->sum('duration');
        $totalCost = $project->timeEntries->sum(fn($entry) => $entry->duration * $entry->hourly_rate);

        $this->assertEquals(50.0, $totalHours);
        $this->assertEquals(5000.00, $totalCost);
        $this->assertLessThan($project->budget, $totalCost);
    }

    /**
     * Test project with team members
     */
    public function test_project_with_team_members(): void
    {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'active',
        ]);

        $teamMembers = User::factory()->count(5)->create(['role' => 'employee']);

        // Assign tasks to different team members
        foreach ($teamMembers as $index => $member) {
            Task::factory()->create([
                'project_id' => $project->id,
                'assigned_to' => $member->id,
                'title' => "Task for {$member->name}",
            ]);
        }

        // Each team member logs time
        foreach ($teamMembers as $member) {
            TimeEntry::factory()->create([
                'project_id' => $project->id,
                'user_id' => $member->id,
                'duration' => 8.0,
            ]);
        }

        $uniqueUsers = $project->timeEntries->pluck('user_id')->unique();
        $this->assertCount(5, $uniqueUsers);
    }

    /**
     * Test project status transitions
     */
    public function test_project_status_transitions(): void
    {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'draft',
        ]);

        // Draft -> Planning
        $project->update(['status' => 'planning']);
        $this->assertEquals('planning', $project->fresh()->status);

        // Planning -> Active
        $project->update(['status' => 'active']);
        $this->assertEquals('active', $project->fresh()->status);

        // Active -> On Hold
        $project->update(['status' => 'on_hold']);
        $this->assertEquals('on_hold', $project->fresh()->status);

        // On Hold -> Active
        $project->update(['status' => 'active']);
        $this->assertEquals('active', $project->fresh()->status);

        // Active -> Completed
        $project->update(['status' => 'completed', 'actual_end_date' => now()]);
        $this->assertEquals('completed', $project->fresh()->status);
        $this->assertNotNull($project->actual_end_date);
    }

    /**
     * Test project with deliverables and documents
     */
    public function test_project_with_deliverables_and_documents(): void
    {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'active',
        ]);

        // Upload various documents
        $categories = ['proposal', 'contract', 'design', 'report', 'deliverable'];

        foreach ($categories as $category) {
            Document::factory()->create([
                'documentable_type' => Project::class,
                'documentable_id' => $project->id,
                'uploaded_by' => $this->admin->id,
                'category' => $category,
                'name' => ucfirst($category) . ' Document',
            ]);
        }

        $this->assertCount(5, $project->documents);

        // Check documents by category
        $designDocs = $project->documents()->where('category', 'design')->count();
        $this->assertEquals(1, $designDocs);
    }

    /**
     * Test project invoicing workflow
     */
    public function test_project_invoicing_workflow(): void
    {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'budget' => 30000.00,
            'status' => 'active',
        ]);

        // Create 3 milestone invoices
        $invoice1 = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $project->id,
            'total_amount' => 10000.00,
            'status' => 'paid',
            'paid_amount' => 10000.00,
        ]);

        $invoice2 = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $project->id,
            'total_amount' => 10000.00,
            'status' => 'sent',
            'paid_amount' => 0.00,
        ]);

        $invoice3 = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $project->id,
            'total_amount' => 10000.00,
            'status' => 'draft',
            'paid_amount' => 0.00,
        ]);

        $this->assertCount(3, $project->invoices);

        $totalInvoiced = $project->invoices->sum('total_amount');
        $totalPaid = $project->invoices->sum('paid_amount');

        $this->assertEquals(30000.00, $totalInvoiced);
        $this->assertEquals(10000.00, $totalPaid);
    }

    /**
     * Test project time tracking accuracy
     */
    public function test_project_time_tracking_accuracy(): void
    {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'active',
        ]);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'estimated_hours' => 40.0,
        ]);

        // Log time entries
        TimeEntry::factory()->create([
            'project_id' => $project->id,
            'task_id' => $task->id,
            'user_id' => $this->projectManager->id,
            'duration' => 10.0,
        ]);

        TimeEntry::factory()->create([
            'project_id' => $project->id,
            'task_id' => $task->id,
            'user_id' => $this->projectManager->id,
            'duration' => 15.5,
        ]);

        TimeEntry::factory()->create([
            'project_id' => $project->id,
            'task_id' => $task->id,
            'user_id' => $this->projectManager->id,
            'duration' => 8.25,
        ]);

        $totalTracked = $task->timeEntries->sum('duration');
        $remaining = $task->estimated_hours - $totalTracked;

        $this->assertEquals(33.75, $totalTracked);
        $this->assertEquals(6.25, $remaining);
    }

    /**
     * Test project can be cancelled
     */
    public function test_project_can_be_cancelled(): void
    {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'active',
        ]);

        // Create some tasks
        Task::factory()->count(5)->create([
            'project_id' => $project->id,
            'status' => 'in_progress',
        ]);

        // Cancel project
        $project->update(['status' => 'cancelled']);

        $this->assertEquals('cancelled', $project->fresh()->status);

        // Optionally cancel all tasks
        $project->tasks()->update(['status' => 'cancelled']);

        $this->assertTrue($project->tasks->every(fn($task) => $task->status === 'cancelled'));
    }

    /**
     * Test project with contract lifecycle
     */
    public function test_project_with_contract_lifecycle(): void
    {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'planning',
        ]);

        // Create contract
        $contract = Contract::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $project->id,
            'contract_value' => 50000.00,
            'status' => 'draft',
        ]);

        $this->assertEquals('draft', $contract->status);

        // Send for signature
        $contract->update(['status' => 'pending_signature']);
        $this->assertEquals('pending_signature', $contract->fresh()->status);

        // Contract signed
        $contract->update([
            'status' => 'active',
            'signed_date' => now(),
        ]);

        $this->assertEquals('active', $contract->fresh()->status);
        $this->assertNotNull($contract->signed_date);

        // Start project
        $project->update(['status' => 'active']);

        $this->assertEquals('active', $project->fresh()->status);
    }

    /**
     * Test project completion requirements
     */
    public function test_project_completion_requirements(): void
    {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'active',
        ]);

        // Create and complete all tasks
        $tasks = Task::factory()->count(5)->create([
            'project_id' => $project->id,
            'status' => 'pending',
        ]);

        foreach ($tasks as $task) {
            $task->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        // All invoices paid
        $invoice = Invoice::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $project->id,
            'total_amount' => 10000.00,
            'paid_amount' => 10000.00,
            'status' => 'paid',
        ]);

        // Verify completion criteria
        $allTasksCompleted = $project->tasks->every(fn($task) => $task->status === 'completed');
        $allInvoicesPaid = $project->invoices->every(fn($invoice) => $invoice->status === 'paid');

        $this->assertTrue($allTasksCompleted);
        $this->assertTrue($allInvoicesPaid);

        // Complete project
        $project->update([
            'status' => 'completed',
            'actual_end_date' => now(),
        ]);

        $this->assertEquals('completed', $project->fresh()->status);
    }

    /**
     * Test project with overdue tasks
     */
    public function test_project_with_overdue_tasks(): void
    {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'active',
        ]);

        // Create tasks with different due dates
        $overdueTask = Task::factory()->create([
            'project_id' => $project->id,
            'due_date' => now()->subDays(5),
            'status' => 'in_progress',
        ]);

        $upcomingTask = Task::factory()->create([
            'project_id' => $project->id,
            'due_date' => now()->addDays(5),
            'status' => 'pending',
        ]);

        // Check overdue tasks
        $overdueTasks = $project->tasks()
            ->where('due_date', '<', now())
            ->where('status', '!=', 'completed')
            ->get();

        $this->assertCount(1, $overdueTasks);
        $this->assertTrue($overdueTasks->contains($overdueTask));
    }

    /**
     * Test project archival
     */
    public function test_project_archival(): void
    {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'completed',
            'actual_end_date' => now()->subMonths(6),
        ]);

        // Archive project (soft delete)
        $project->delete();

        $this->assertSoftDeleted('projects', [
            'id' => $project->id,
        ]);

        // Can still access archived project
        $archivedProject = Project::withTrashed()->find($project->id);
        $this->assertNotNull($archivedProject);
        $this->assertNotNull($archivedProject->deleted_at);
    }
}
