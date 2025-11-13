<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTOs\CreateProjectDTO;
use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Enums\Priority;
use App\Models\Company;
use App\Models\Project;
use App\Models\ProjectStatusHistory;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Project Service Unit Tests
 *
 * Tests the business logic in ProjectService including:
 * - Project creation with status history
 * - Project updates
 * - Status changes with history tracking
 * - Transaction rollback on failures
 */
class ProjectServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProjectService $projectService;
    private User $user;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->projectService = app(ProjectService::class);

        // Create test user and company
        $this->user = User::factory()->create(['role' => 'admin']);
        $this->company = Company::factory()->create();
    }

    /**
     * Test project creation with all fields
     */
    public function test_create_project_with_all_fields(): void
    {
        $dto = new CreateProjectDTO(
            company_id: $this->company->id,
            created_by: $this->user->id,
            name: 'Test Project',
            description: 'Test Description',
            status: ProjectStatus::Planning,
            type: ProjectType::WebDevelopment,
            budget: 10000.00,
            estimated_hours: 100.0,
            start_date: now(),
            deadline: now()->addDays(30),
            priority: Priority::High,
            is_billable: true,
            hourly_rate: 100.00,
            technologies: ['PHP', 'Laravel', 'Vue.js'],
            repository_url: 'https://github.com/test/repo',
            staging_url: 'https://staging.example.com',
            production_url: 'https://example.com',
            notes: 'Test notes'
        );

        $project = $this->projectService->create($dto);

        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals('Test Project', $project->name);
        $this->assertEquals('test-project', $project->slug);
        $this->assertEquals($this->company->id, $project->company_id);
        $this->assertEquals($this->user->id, $project->created_by);
        $this->assertEquals(ProjectStatus::Planning, $project->status);
        $this->assertEquals(0, $project->progress);
        $this->assertEquals(0, $project->actual_hours);

        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'slug' => 'test-project',
        ]);
    }

    /**
     * Test that status history is created on project creation
     */
    public function test_creates_status_history_on_project_creation(): void
    {
        $dto = new CreateProjectDTO(
            company_id: $this->company->id,
            created_by: $this->user->id,
            name: 'Test Project',
            description: 'Test Description',
            status: ProjectStatus::Planning,
            type: ProjectType::WebDevelopment,
            budget: 10000.00,
            estimated_hours: 100.0,
            start_date: now(),
            deadline: now()->addDays(30),
            priority: Priority::High,
            is_billable: true,
            hourly_rate: 100.00,
        );

        $project = $this->projectService->create($dto);

        $this->assertDatabaseHas('project_status_histories', [
            'project_id' => $project->id,
            'status' => ProjectStatus::Planning->value,
            'changed_by' => $this->user->id,
            'notes' => 'Project created',
        ]);

        $this->assertCount(1, $project->statusHistory);
    }

    /**
     * Test project update with name change updates slug
     */
    public function test_update_project_with_name_change_updates_slug(): void
    {
        $project = Project::factory()->create([
            'name' => 'Original Name',
            'slug' => 'original-name',
            'company_id' => $this->company->id,
        ]);

        $updated = $this->projectService->update($project, [
            'name' => 'New Project Name',
        ]);

        $this->assertEquals('New Project Name', $updated->name);
        $this->assertEquals('new-project-name', $updated->slug);
    }

    /**
     * Test project update without name change preserves slug
     */
    public function test_update_project_without_name_change_preserves_slug(): void
    {
        $project = Project::factory()->create([
            'name' => 'Project Name',
            'slug' => 'project-name',
            'company_id' => $this->company->id,
        ]);

        $updated = $this->projectService->update($project, [
            'description' => 'Updated description',
        ]);

        $this->assertEquals('Project Name', $updated->name);
        $this->assertEquals('project-name', $updated->slug);
        $this->assertEquals('Updated description', $updated->description);
    }

    /**
     * Test slug generation handles special characters
     */
    public function test_slug_generation_handles_special_characters(): void
    {
        $dto = new CreateProjectDTO(
            company_id: $this->company->id,
            created_by: $this->user->id,
            name: 'Project #1 - Client & Partners!',
            description: 'Test',
            status: ProjectStatus::Planning,
            type: ProjectType::WebDevelopment,
            budget: 5000.00,
            estimated_hours: 50.0,
            start_date: now(),
            deadline: now()->addDays(30),
            priority: Priority::Medium,
            is_billable: true,
            hourly_rate: 100.00,
        );

        $project = $this->projectService->create($dto);

        $this->assertEquals('project-1-client-partners', $project->slug);
    }

    /**
     * Test project creation is wrapped in transaction
     */
    public function test_project_creation_is_transactional(): void
    {
        // This would test that if status history creation fails,
        // the project is not created either
        $this->expectException(\Exception::class);

        // Mock the ProjectStatusHistory to throw exception
        $this->mock(ProjectStatusHistory::class, function ($mock) {
            $mock->shouldReceive('create')->andThrow(new \Exception('Database error'));
        });

        $dto = new CreateProjectDTO(
            company_id: $this->company->id,
            created_by: $this->user->id,
            name: 'Test Project',
            description: 'Test',
            status: ProjectStatus::Planning,
            type: ProjectType::WebDevelopment,
            budget: 5000.00,
            estimated_hours: 50.0,
            start_date: now(),
            deadline: now()->addDays(30),
            priority: Priority::Medium,
            is_billable: true,
            hourly_rate: 100.00,
        );

        try {
            $this->projectService->create($dto);
        } catch (\Exception $e) {
            // Verify no project was created
            $this->assertDatabaseMissing('projects', [
                'name' => 'Test Project',
            ]);
            throw $e;
        }
    }

    /**
     * Test project relationships are loaded
     */
    public function test_created_project_loads_relationships(): void
    {
        $dto = new CreateProjectDTO(
            company_id: $this->company->id,
            created_by: $this->user->id,
            name: 'Test Project',
            description: 'Test',
            status: ProjectStatus::Planning,
            type: ProjectType::WebDevelopment,
            budget: 5000.00,
            estimated_hours: 50.0,
            start_date: now(),
            deadline: now()->addDays(30),
            priority: Priority::Medium,
            is_billable: true,
            hourly_rate: 100.00,
        );

        $project = $this->projectService->create($dto);

        $this->assertTrue($project->relationLoaded('company'));
        $this->assertTrue($project->relationLoaded('creator'));
        $this->assertInstanceOf(Company::class, $project->company);
        $this->assertInstanceOf(User::class, $project->creator);
    }

    /**
     * Test technologies array is properly stored
     */
    public function test_technologies_array_is_stored_correctly(): void
    {
        $technologies = ['PHP', 'Laravel', 'Vue.js', 'Tailwind CSS'];

        $dto = new CreateProjectDTO(
            company_id: $this->company->id,
            created_by: $this->user->id,
            name: 'Tech Stack Project',
            description: 'Testing tech array',
            status: ProjectStatus::Planning,
            type: ProjectType::WebDevelopment,
            budget: 5000.00,
            estimated_hours: 50.0,
            start_date: now(),
            deadline: now()->addDays(30),
            priority: Priority::Medium,
            is_billable: true,
            hourly_rate: 100.00,
            technologies: $technologies,
        );

        $project = $this->projectService->create($dto);

        $this->assertEquals($technologies, $project->technologies);
        $this->assertIsArray($project->technologies);
        $this->assertCount(4, $project->technologies);
    }
}
