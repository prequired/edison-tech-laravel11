<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Project Controller Feature Tests
 *
 * Tests the admin project controller CRUD operations including:
 * - Listing projects
 * - Creating projects
 * - Viewing project details
 * - Updating projects
 * - Deleting projects
 * - Authorization checks
 */
class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $client;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->client = User::factory()->create(['role' => 'client', 'is_active' => true]);
        $this->company = Company::factory()->create();
    }

    /**
     * Test admin can view projects index
     */
    public function test_admin_can_view_projects_index(): void
    {
        Project::factory()->count(3)->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.projects.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.projects.index');
        $response->assertViewHas('projects');
    }

    /**
     * Test client cannot access admin projects index
     */
    public function test_client_cannot_access_admin_projects_index(): void
    {
        $response = $this->actingAs($this->client)->get(route('admin.projects.index'));

        $response->assertStatus(403);
    }

    /**
     * Test guest cannot access admin projects index
     */
    public function test_guest_cannot_access_admin_projects_index(): void
    {
        $response = $this->get(route('admin.projects.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test admin can view create project form
     */
    public function test_admin_can_view_create_project_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.projects.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.projects.create');
        $response->assertSee('Create Project');
    }

    /**
     * Test admin can create project
     */
    public function test_admin_can_create_project(): void
    {
        $projectData = [
            'company_id' => $this->company->id,
            'name' => 'New Test Project',
            'description' => 'Test project description',
            'status' => ProjectStatus::Planning->value,
            'type' => ProjectType::WebDevelopment->value,
            'budget' => 15000.00,
            'estimated_hours' => 150.0,
            'start_date' => now()->format('Y-m-d'),
            'deadline' => now()->addDays(60)->format('Y-m-d'),
            'priority' => Priority::High->value,
            'is_billable' => true,
            'hourly_rate' => 125.00,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.projects.store'), $projectData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('projects', [
            'name' => 'New Test Project',
            'slug' => 'new-test-project',
            'company_id' => $this->company->id,
        ]);
    }

    /**
     * Test create project validation requires name
     */
    public function test_create_project_requires_name(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.projects.store'), [
                'company_id' => $this->company->id,
                'status' => ProjectStatus::Planning->value,
            ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test create project validation requires company
     */
    public function test_create_project_requires_company(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.projects.store'), [
                'name' => 'Test Project',
                'status' => ProjectStatus::Planning->value,
            ]);

        $response->assertSessionHasErrors('company_id');
    }

    /**
     * Test admin can view project details
     */
    public function test_admin_can_view_project_details(): void
    {
        $project = Project::factory()->create([
            'company_id' => $this->company->id,
            'name' => 'Detail Test Project',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.projects.show', $project));

        $response->assertStatus(200);
        $response->assertViewIs('admin.projects.show');
        $response->assertViewHas('project');
        $response->assertSee('Detail Test Project');
    }

    /**
     * Test admin can view edit project form
     */
    public function test_admin_can_view_edit_project_form(): void
    {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.projects.edit', $project));

        $response->assertStatus(200);
        $response->assertViewIs('admin.projects.edit');
        $response->assertViewHas('project');
    }

    /**
     * Test admin can update project
     */
    public function test_admin_can_update_project(): void
    {
        $project = Project::factory()->create([
            'name' => 'Original Name',
            'company_id' => $this->company->id,
        ]);

        $updateData = [
            'name' => 'Updated Project Name',
            'description' => 'Updated description',
            'status' => ProjectStatus::InProgress->value,
            'progress' => 50,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.projects.update', $project), $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project Name',
            'slug' => 'updated-project-name',
        ]);
    }

    /**
     * Test admin can delete project
     */
    public function test_admin_can_delete_project(): void
    {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.projects.destroy', $project));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('projects', [
            'id' => $project->id,
        ]);
    }

    /**
     * Test project index has search functionality
     */
    public function test_projects_index_can_search(): void
    {
        Project::factory()->create([
            'name' => 'Laravel Project',
            'company_id' => $this->company->id,
        ]);

        Project::factory()->create([
            'name' => 'React Project',
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.projects.index', ['search' => 'Laravel']));

        $response->assertStatus(200);
        $response->assertSee('Laravel Project');
        $response->assertDontSee('React Project');
    }

    /**
     * Test project index can filter by status
     */
    public function test_projects_index_can_filter_by_status(): void
    {
        Project::factory()->create([
            'name' => 'Planning Project',
            'status' => ProjectStatus::Planning,
            'company_id' => $this->company->id,
        ]);

        Project::factory()->create([
            'name' => 'In Progress Project',
            'status' => ProjectStatus::InProgress,
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.projects.index', ['status' => ProjectStatus::Planning->value]));

        $response->assertStatus(200);
        $response->assertSee('Planning Project');
        $response->assertDontSee('In Progress Project');
    }

    /**
     * Test project index can filter by company
     */
    public function test_projects_index_can_filter_by_company(): void
    {
        $company2 = Company::factory()->create();

        Project::factory()->create([
            'name' => 'Company 1 Project',
            'company_id' => $this->company->id,
        ]);

        Project::factory()->create([
            'name' => 'Company 2 Project',
            'company_id' => $company2->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.projects.index', ['company' => $this->company->id]));

        $response->assertStatus(200);
        $response->assertSee('Company 1 Project');
        $response->assertDontSee('Company 2 Project');
    }

    /**
     * Test project index is paginated
     */
    public function test_projects_index_is_paginated(): void
    {
        Project::factory()->count(20)->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.projects.index'));

        $response->assertStatus(200);
        $response->assertViewHas('projects');

        $projects = $response->viewData('projects');
        $this->assertLessThanOrEqual(15, $projects->count());
    }

    /**
     * Test budget validation
     */
    public function test_project_budget_must_be_numeric(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.projects.store'), [
                'company_id' => $this->company->id,
                'name' => 'Test Project',
                'status' => ProjectStatus::Planning->value,
                'budget' => 'not-a-number',
            ]);

        $response->assertSessionHasErrors('budget');
    }

    /**
     * Test progress validation
     */
    public function test_project_progress_must_be_between_0_and_100(): void
    {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->admin)
            ->put(route('admin.projects.update', $project), [
                'name' => 'Test',
                'progress' => 150,
            ]);

        $response->assertSessionHasErrors('progress');
    }
}
