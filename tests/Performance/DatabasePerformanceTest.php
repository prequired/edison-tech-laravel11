<?php

declare(strict_types=1);

namespace Tests\Performance;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Database Performance Tests
 *
 * Tests database query performance and optimization:
 * - Query count (N+1 prevention)
 * - Response time benchmarks
 * - Eager loading efficiency
 * - Index effectiveness
 * - Large dataset handling
 */
class DatabasePerformanceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /**
     * Test projects index has minimal queries with eager loading
     */
    public function test_projects_index_has_minimal_queries(): void
    {
        // Create test data
        $companies = Company::factory()->count(5)->create();
        foreach ($companies as $company) {
            Project::factory()->count(4)->create(['company_id' => $company->id]);
        }

        DB::enableQueryLog();

        // Fetch projects with proper eager loading
        $projects = Project::with(['company'])->paginate(15);

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        // Should be minimal queries (ideally 2-3: projects, companies, count)
        $this->assertLessThan(5, $queryCount, "Too many queries: {$queryCount}. Check for N+1 issues.");

        DB::disableQueryLog();
    }

    /**
     * Test tasks index avoids N+1 query problem
     */
    public function test_tasks_index_avoids_n_plus_one(): void
    {
        $project = Project::factory()->create();
        $users = User::factory()->count(10)->create();

        // Create 50 tasks
        foreach ($users as $user) {
            Task::factory()->count(5)->create([
                'project_id' => $project->id,
                'assigned_to' => $user->id,
            ]);
        }

        DB::enableQueryLog();

        // Fetch with eager loading
        $tasks = Task::with(['project.company', 'assignedTo'])->paginate(15);

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        // Should be minimal (3-4 queries max)
        $this->assertLessThan(6, $queryCount, "N+1 detected: {$queryCount} queries");

        DB::disableQueryLog();
    }

    /**
     * Test invoice with items loads efficiently
     */
    public function test_invoice_with_items_loads_efficiently(): void
    {
        $company = Company::factory()->create();
        $invoice = Invoice::factory()->create(['company_id' => $company->id]);

        DB::enableQueryLog();

        // Load invoice with all relationships
        $loadedInvoice = Invoice::with(['company', 'project', 'items', 'payments'])
            ->find($invoice->id);

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        // Should be 5 queries max (invoice, company, project, items, payments)
        $this->assertLessThan(7, $queryCount);

        DB::disableQueryLog();
    }

    /**
     * Test large dataset pagination performance
     */
    public function test_large_dataset_pagination_performance(): void
    {
        // Create 1000 projects
        $companies = Company::factory()->count(10)->create();
        foreach ($companies as $company) {
            Project::factory()->count(100)->create(['company_id' => $company->id]);
        }

        $startTime = microtime(true);

        // Paginate through large dataset
        $projects = Project::with('company')->paginate(50);

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        // Should complete in less than 500ms
        $this->assertLessThan(500, $executionTime, "Pagination took {$executionTime}ms");
        $this->assertEquals(50, $projects->count());
    }

    /**
     * Test search query performance
     */
    public function test_search_query_performance(): void
    {
        // Create searchable data
        Company::factory()->count(100)->create();
        Project::factory()->count(200)->create();
        Task::factory()->count(500)->create();

        $startTime = microtime(true);

        // Perform search
        $results = Project::where('name', 'like', '%test%')
            ->orWhere('description', 'like', '%test%')
            ->limit(50)
            ->get();

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        // Search should be fast (< 200ms)
        $this->assertLessThan(200, $executionTime, "Search took {$executionTime}ms");
    }

    /**
     * Test complex aggregation query performance
     */
    public function test_complex_aggregation_performance(): void
    {
        $company = Company::factory()->create();
        $project = Project::factory()->create(['company_id' => $company->id]);

        // Create time entries
        $users = User::factory()->count(10)->create();
        foreach ($users as $user) {
            TimeEntry::factory()->count(20)->create([
                'project_id' => $project->id,
                'user_id' => $user->id,
            ]);
        }

        $startTime = microtime(true);

        // Complex aggregation
        $stats = TimeEntry::where('project_id', $project->id)
            ->selectRaw('user_id, SUM(duration) as total_hours, COUNT(*) as entry_count')
            ->groupBy('user_id')
            ->get();

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        // Should be fast (< 100ms)
        $this->assertLessThan(100, $executionTime, "Aggregation took {$executionTime}ms");
        $this->assertCount(10, $stats);
    }

    /**
     * Test invoice total calculation performance
     */
    public function test_invoice_total_calculation_performance(): void
    {
        $company = Company::factory()->create();

        // Create 100 invoices
        Invoice::factory()->count(100)->create(['company_id' => $company->id]);

        $startTime = microtime(true);

        // Calculate totals
        $totalAmount = Invoice::sum('total_amount');
        $paidAmount = Invoice::sum('paid_amount');
        $balance = Invoice::sum('balance');

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        // Should be very fast (< 50ms)
        $this->assertLessThan(50, $executionTime, "Calculation took {$executionTime}ms");
    }

    /**
     * Test payment filtering performance
     */
    public function test_payment_filtering_performance(): void
    {
        $company = Company::factory()->create();
        $invoices = Invoice::factory()->count(50)->create(['company_id' => $company->id]);

        // Create 500 payments
        foreach ($invoices as $invoice) {
            Payment::factory()->count(10)->create(['invoice_id' => $invoice->id]);
        }

        $startTime = microtime(true);

        // Filter payments
        $payments = Payment::whereHas('invoice', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })
        ->where('status', 'completed')
        ->where('payment_date', '>=', now()->subMonths(3))
        ->with('invoice')
        ->paginate(50);

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        // Should be reasonably fast (< 300ms)
        $this->assertLessThan(300, $executionTime, "Filtering took {$executionTime}ms");
    }

    /**
     * Test project with all relationships loads efficiently
     */
    public function test_project_with_all_relationships_efficiency(): void
    {
        $project = Project::factory()->create();

        // Add related data
        Task::factory()->count(20)->create(['project_id' => $project->id]);
        TimeEntry::factory()->count(50)->create(['project_id' => $project->id]);
        Invoice::factory()->count(5)->create(['project_id' => $project->id]);

        DB::enableQueryLog();

        $startTime = microtime(true);

        // Load everything
        $loadedProject = Project::with([
            'company',
            'tasks',
            'timeEntries.user',
            'invoices.payments',
            'documents',
        ])->find($project->id);

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        // Should use reasonable number of queries
        $this->assertLessThan(10, $queryCount, "Too many queries: {$queryCount}");

        // Should complete reasonably fast
        $this->assertLessThan(500, $executionTime, "Loading took {$executionTime}ms");

        DB::disableQueryLog();
    }

    /**
     * Test task filtering with multiple conditions
     */
    public function test_task_filtering_with_multiple_conditions(): void
    {
        $project = Project::factory()->create();
        $users = User::factory()->count(20)->create();

        // Create diverse tasks
        foreach ($users as $user) {
            Task::factory()->count(5)->create([
                'project_id' => $project->id,
                'assigned_to' => $user->id,
            ]);
        }

        $startTime = microtime(true);

        // Complex filter
        $tasks = Task::where('project_id', $project->id)
            ->where('status', 'in_progress')
            ->where('priority', 'high')
            ->whereNotNull('assigned_to')
            ->whereDate('due_date', '>=', now())
            ->with(['assignedTo', 'project'])
            ->get();

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        // Should be fast (< 150ms)
        $this->assertLessThan(150, $executionTime, "Complex filter took {$executionTime}ms");
    }

    /**
     * Test bulk insert performance
     */
    public function test_bulk_insert_performance(): void
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();

        $startTime = microtime(true);

        // Bulk insert 100 time entries
        $entries = [];
        for ($i = 0; $i < 100; $i++) {
            $entries[] = [
                'project_id' => $project->id,
                'user_id' => $user->id,
                'duration' => 8.0,
                'description' => "Entry {$i}",
                'entry_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        TimeEntry::insert($entries);

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        // Bulk insert should be fast (< 200ms)
        $this->assertLessThan(200, $executionTime, "Bulk insert took {$executionTime}ms");
        $this->assertEquals(100, TimeEntry::where('project_id', $project->id)->count());
    }

    /**
     * Test dashboard statistics query performance
     */
    public function test_dashboard_statistics_performance(): void
    {
        // Create realistic dataset
        $companies = Company::factory()->count(10)->create();
        foreach ($companies as $company) {
            $projects = Project::factory()->count(5)->create(['company_id' => $company->id]);
            foreach ($projects as $project) {
                Task::factory()->count(10)->create(['project_id' => $project->id]);
                Invoice::factory()->count(3)->create([
                    'company_id' => $company->id,
                    'project_id' => $project->id,
                ]);
            }
        }

        $startTime = microtime(true);

        // Calculate dashboard statistics
        $stats = [
            'total_projects' => Project::count(),
            'active_projects' => Project::where('status', 'active')->count(),
            'total_tasks' => Task::count(),
            'pending_tasks' => Task::where('status', 'pending')->count(),
            'total_invoices' => Invoice::count(),
            'unpaid_invoices' => Invoice::where('status', '!=', 'paid')->count(),
            'total_revenue' => Invoice::sum('total_amount'),
            'outstanding_balance' => Invoice::sum('balance'),
        ];

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        // Dashboard should load fast (< 300ms)
        $this->assertLessThan(300, $executionTime, "Dashboard stats took {$executionTime}ms");
        $this->assertIsArray($stats);
    }

    /**
     * Test index with soft-deleted records
     */
    public function test_index_with_soft_deleted_records_performance(): void
    {
        // Create projects, some deleted
        Project::factory()->count(50)->create();
        Project::factory()->count(50)->create(['deleted_at' => now()]);

        $startTime = microtime(true);

        // Query only active
        $activeProjects = Project::count();

        // Query with trashed
        $allProjects = Project::withTrashed()->count();

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        // Should be fast (< 50ms)
        $this->assertLessThan(50, $executionTime, "Soft delete queries took {$executionTime}ms");
        $this->assertEquals(50, $activeProjects);
        $this->assertEquals(100, $allProjects);
    }

    /**
     * Test concurrent read performance
     */
    public function test_concurrent_read_performance(): void
    {
        Project::factory()->count(100)->create();

        $iterations = 10;
        $startTime = microtime(true);

        // Simulate concurrent reads
        for ($i = 0; $i < $iterations; $i++) {
            Project::with('company')->paginate(15);
        }

        $endTime = microtime(true);
        $totalTime = ($endTime - $startTime) * 1000;
        $avgTime = $totalTime / $iterations;

        // Average request should be fast (< 100ms)
        $this->assertLessThan(100, $avgTime, "Average read took {$avgTime}ms");
    }
}
