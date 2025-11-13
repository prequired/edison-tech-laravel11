<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * User Controller Feature Tests
 *
 * Tests the admin user controller CRUD operations including:
 * - Listing users
 * - Creating users
 * - Viewing user details
 * - Updating users
 * - Deleting users
 * - Activation/deactivation
 * - Authorization checks
 */
class UserControllerTest extends TestCase
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
     * Test admin can view users index
     */
    public function test_admin_can_view_users_index(): void
    {
        User::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
        $response->assertViewHas('users');
    }

    /**
     * Test client cannot access admin users index
     */
    public function test_client_cannot_access_admin_users_index(): void
    {
        $response = $this->actingAs($this->client)->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    /**
     * Test guest cannot access admin users index
     */
    public function test_guest_cannot_access_admin_users_index(): void
    {
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test admin can view create user form
     */
    public function test_admin_can_view_create_user_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.create');
        $response->assertSee('Create User');
    }

    /**
     * Test admin can create user
     */
    public function test_admin_can_create_user(): void
    {
        $userData = [
            'company_id' => $this->company->id,
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'client',
            'phone' => '+1234567890',
            'is_active' => true,
            'timezone' => 'America/New_York',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), $userData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'name' => 'New User',
        ]);

        // Verify password is hashed
        $user = User::where('email', 'newuser@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /**
     * Test create user validation requires name
     */
    public function test_create_user_requires_name(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'email' => 'test@example.com',
                'password' => 'password123',
            ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test create user validation requires email
     */
    public function test_create_user_requires_email(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'password' => 'password123',
            ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test create user validation requires password
     */
    public function test_create_user_requires_password(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test email must be unique
     */
    public function test_email_must_be_unique(): void
    {
        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'company_id' => $this->company->id,
                'name' => 'New User',
                'email' => 'existing@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'client',
            ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test admin can view user details
     */
    public function test_admin_can_view_user_details(): void
    {
        $user = User::factory()->create([
            'name' => 'Detail Test User',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.show', $user));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.show');
        $response->assertViewHas('user');
        $response->assertSee('Detail Test User');
    }

    /**
     * Test admin can view edit user form
     */
    public function test_admin_can_view_edit_user_form(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.edit', $user));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.edit');
        $response->assertViewHas('user');
    }

    /**
     * Test admin can update user
     */
    public function test_admin_can_update_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '+0987654321',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.users.update', $user), $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    /**
     * Test admin can update user password
     */
    public function test_admin_can_update_user_password(): void
    {
        $user = User::factory()->create();
        $oldPassword = $user->password;

        $updateData = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.users.update', $user), $updateData);

        $response->assertRedirect();

        $user->refresh();
        $this->assertNotEquals($oldPassword, $user->password);
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    /**
     * Test admin can delete user
     */
    public function test_admin_can_delete_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $user));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('users', [
            'id' => $user->id,
        ]);
    }

    /**
     * Test admin can activate user
     */
    public function test_admin_can_activate_user(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.activate', $user));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue($user->is_active);
    }

    /**
     * Test admin can deactivate user
     */
    public function test_admin_can_deactivate_user(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.deactivate', $user));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertFalse($user->is_active);
    }

    /**
     * Test users index has search functionality
     */
    public function test_users_index_can_search(): void
    {
        User::factory()->create([
            'name' => 'Alice Smith',
            'email' => 'alice@example.com',
        ]);

        User::factory()->create([
            'name' => 'Bob Jones',
            'email' => 'bob@example.com',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['search' => 'Alice']));

        $response->assertStatus(200);
        $response->assertSee('Alice Smith');
        $response->assertDontSee('Bob Jones');
    }

    /**
     * Test users index can filter by role
     */
    public function test_users_index_can_filter_by_role(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Client User',
            'role' => 'client',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['role' => 'admin']));

        $response->assertStatus(200);
        $response->assertSee('Admin User');
        $response->assertDontSee('Client User');
    }

    /**
     * Test users index can filter by company
     */
    public function test_users_index_can_filter_by_company(): void
    {
        $company2 = Company::factory()->create();

        User::factory()->create([
            'name' => 'Company 1 User',
            'company_id' => $this->company->id,
        ]);

        User::factory()->create([
            'name' => 'Company 2 User',
            'company_id' => $company2->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['company' => $this->company->id]));

        $response->assertStatus(200);
        $response->assertSee('Company 1 User');
        $response->assertDontSee('Company 2 User');
    }

    /**
     * Test users index is paginated
     */
    public function test_users_index_is_paginated(): void
    {
        User::factory()->count(20)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewHas('users');

        $users = $response->viewData('users');
        $this->assertLessThanOrEqual(15, $users->count());
    }

    /**
     * Test password confirmation is required
     */
    public function test_password_confirmation_is_required(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'company_id' => $this->company->id,
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123',
                'role' => 'client',
            ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test different user roles can be assigned
     */
    public function test_different_user_roles_can_be_assigned(): void
    {
        $roles = ['admin', 'client', 'employee'];

        foreach ($roles as $role) {
            $userData = [
                'company_id' => $this->company->id,
                'name' => "Test {$role}",
                'email' => "{$role}@example.com",
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => $role,
                'is_active' => true,
            ];

            $response = $this->actingAs($this->admin)
                ->post(route('admin.users.store'), $userData);

            $response->assertRedirect();

            $this->assertDatabaseHas('users', [
                'email' => "{$role}@example.com",
                'role' => $role,
            ]);
        }
    }
}
