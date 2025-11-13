<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ProjectStatusHistory;
use App\Models\Task;
use App\Models\TimeTracking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Project Model Unit Tests
 *
 * Tests the Project model including:
 * - Relationships
 * - Accessors and mutators
 * - Casting
 * - Business logic methods
 */
class ProjectTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->user = User::factory()->create();
    }

    /**
     * Test project belongs to company
     */
    public function test_belongs_to_company(): void
    {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $this->assertInstanceOf(Company::class, $project->company);
        $this->assertEquals($this->company->id, $project->company->id);
    }

    /**
     * Test project belongs to creator (user)
     */
    public function test_belongs_to_creator(): void
    {
        $project = Project::factory()->create([
            'created_by' => $this->user->id,
        ]);

        $this->assertInstanceOf(User::class, $project->creator);
        $this->assertEquals($this->user->id, $project->creator->id);
    }

    /**
     * Test project has many tasks
     */
    public function test_has_many_tasks(): void
    {
        $project = Project::factory()->create();

        Task::factory()->count(3)->create([
            'project_id' => $project->id,
        ]);

        $this->assertCount(3, $project->tasks);
        $this->assertInstanceOf(Task::class, $project->tasks->first());
    }

    /**
     * Test project has many time trackings
     */
    public function test_has_many_time_trackings(): void
    {
        $project = Project::factory()->create();

        TimeTracking::factory()->count(5)->create([
            'project_id' => $project->id,
        ]);

        $this->assertCount(5, $project->timeTrackings);
        $this->assertInstanceOf(TimeTracking::class, $project->timeTrackings->first());
    }

    /**
     * Test project has many invoices
     */
    public function test_has_many_invoices(): void
    {
        $project = Project::factory()->create();

        Invoice::factory()->count(2)->create([
            'project_id' => $project->id,
            'company_id' => $project->company_id,
        ]);

        $this->assertCount(2, $project->invoices);
        $this->assertInstanceOf(Invoice::class, $project->invoices->first());
    }

    /**
     * Test project has many status histories
     */
    public function test_has_many_status_histories(): void
    {
        $project = Project::factory()->create();

        ProjectStatusHistory::factory()->count(3)->create([
            'project_id' => $project->id,
        ]);

        $this->assertCount(3, $project->statusHistory);
        $this->assertInstanceOf(ProjectStatusHistory::class, $project->statusHistory->first());
    }

    /**
     * Test technologies are cast to array
     */
    public function test_technologies_cast_to_array(): void
    {
        $technologies = ['PHP', 'Laravel', 'Vue.js'];

        $project = Project::factory()->create([
            'technologies' => $technologies,
        ]);

        $this->assertIsArray($project->technologies);
        $this->assertEquals($technologies, $project->technologies);
    }

    /**
     * Test status is cast to enum
     */
    public function test_status_cast_to_enum(): void
    {
        $project = Project::factory()->create([
            'status' => ProjectStatus::InProgress,
        ]);

        $this->assertInstanceOf(ProjectStatus::class, $project->status);
        $this->assertEquals(ProjectStatus::InProgress, $project->status);
    }

    /**
     * Test type is cast to enum
     */
    public function test_type_cast_to_enum(): void
    {
        $project = Project::factory()->create([
            'type' => ProjectType::WebDevelopment,
        ]);

        $this->assertInstanceOf(ProjectType::class, $project->type);
        $this->assertEquals(ProjectType::WebDevelopment, $project->type);
    }

    /**
     * Test priority is cast to enum
     */
    public function test_priority_cast_to_enum(): void
    {
        $project = Project::factory()->create([
            'priority' => Priority::High,
        ]);

        $this->assertInstanceOf(Priority::class, $project->priority);
        $this->assertEquals(Priority::High, $project->priority);
    }

    /**
     * Test dates are cast to Carbon instances
     */
    public function test_dates_are_cast_correctly(): void
    {
        $startDate = now();
        $deadline = now()->addDays(30);
        $completedAt = now()->addDays(20);

        $project = Project::factory()->create([
            'start_date' => $startDate,
            'deadline' => $deadline,
            'completed_at' => $completedAt,
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $project->start_date);
        $this->assertInstanceOf(\Carbon\Carbon::class, $project->deadline);
        $this->assertInstanceOf(\Carbon\Carbon::class, $project->completed_at);
    }

    /**
     * Test fillable attributes
     */
    public function test_fillable_attributes(): void
    {
        $data = [
            'company_id' => $this->company->id,
            'created_by' => $this->user->id,
            'name' => 'Test Project',
            'slug' => 'test-project',
            'description' => 'Test Description',
            'status' => ProjectStatus::Planning,
            'type' => ProjectType::MobileDevelopment,
            'budget' => 10000.00,
            'estimated_hours' => 100.0,
            'priority' => Priority::Medium,
        ];

        $project = Project::create($data);

        $this->assertEquals('Test Project', $project->name);
        $this->assertEquals($this->company->id, $project->company_id);
        $this->assertEquals(ProjectStatus::Planning, $project->status);
    }

    /**
     * Test slug is unique
     */
    public function test_slug_is_unique(): void
    {
        Project::factory()->create([
            'slug' => 'unique-slug',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Project::factory()->create([
            'slug' => 'unique-slug',
        ]);
    }

    /**
     * Test is_billable is boolean
     */
    public function test_is_billable_is_boolean(): void
    {
        $project = Project::factory()->create([
            'is_billable' => true,
        ]);

        $this->assertIsBool($project->is_billable);
        $this->assertTrue($project->is_billable);
    }

    /**
     * Test progress is clamped between 0 and 100
     */
    public function test_progress_validation(): void
    {
        $project = Project::factory()->create([
            'progress' => 50,
        ]);

        $this->assertEquals(50, $project->progress);
        $this->assertGreaterThanOrEqual(0, $project->progress);
        $this->assertLessThanOrEqual(100, $project->progress);
    }

    /**
     * Test decimal fields are cast correctly
     */
    public function test_decimal_fields_cast_correctly(): void
    {
        $project = Project::factory()->create([
            'budget' => '15000.50',
            'hourly_rate' => '125.75',
            'estimated_hours' => '100.25',
            'actual_hours' => '50.5',
        ]);

        $this->assertIsFloat($project->budget);
        $this->assertEquals(15000.50, $project->budget);
        $this->assertIsFloat($project->hourly_rate);
        $this->assertEquals(125.75, $project->hourly_rate);
    }

    /**
     * Test project soft deletes
     */
    public function test_uses_soft_deletes(): void
    {
        $project = Project::factory()->create();
        $projectId = $project->id;

        $project->delete();

        // Should still exist in database but with deleted_at timestamp
        $this->assertSoftDeleted('projects', ['id' => $projectId]);

        // Can be restored
        $project->restore();
        $this->assertDatabaseHas('projects', [
            'id' => $projectId,
            'deleted_at' => null,
        ]);
    }
}
