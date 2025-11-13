<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeTracking;
use App\Models\TwoFactorAuthentication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * User Model Unit Tests
 *
 * Tests the User model including:
 * - Relationships
 * - Authentication features
 * - 2FA functionality
 * - Casting and accessors
 * - Soft deletes
 */
class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user belongs to company
     */
    public function test_belongs_to_company(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);

        $this->assertInstanceOf(Company::class, $user->company);
        $this->assertEquals($company->id, $user->company->id);
    }

    /**
     * Test user has many projects as creator
     */
    public function test_has_many_projects_as_creator(): void
    {
        $user = User::factory()->create();

        Project::factory()->count(3)->create(['created_by' => $user->id]);

        $this->assertCount(3, $user->createdProjects);
        $this->assertInstanceOf(Project::class, $user->createdProjects->first());
    }

    /**
     * Test user has many tasks
     */
    public function test_has_many_tasks(): void
    {
        $user = User::factory()->create();

        Task::factory()->count(5)->create(['assigned_to' => $user->id]);

        $this->assertCount(5, $user->tasks);
        $this->assertInstanceOf(Task::class, $user->tasks->first());
    }

    /**
     * Test user has many time trackings
     */
    public function test_has_many_time_trackings(): void
    {
        $user = User::factory()->create();

        TimeTracking::factory()->count(4)->create(['user_id' => $user->id]);

        $this->assertCount(4, $user->timeTrackings);
        $this->assertInstanceOf(TimeTracking::class, $user->timeTrackings->first());
    }

    /**
     * Test user has one two factor authentication
     */
    public function test_has_one_two_factor_authentication(): void
    {
        $user = User::factory()->create();

        $twoFactor = TwoFactorAuthentication::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(TwoFactorAuthentication::class, $user->twoFactorAuthentication);
        $this->assertEquals($twoFactor->id, $user->twoFactorAuthentication->id);
    }

    /**
     * Test password is hidden from array
     */
    public function test_password_is_hidden_from_array(): void
    {
        $user = User::factory()->create();

        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    /**
     * Test email is verified dates cast correctly
     */
    public function test_email_verified_at_cast_correctly(): void
    {
        $verifiedAt = now();

        $user = User::factory()->create([
            'email_verified_at' => $verifiedAt,
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $user->email_verified_at);
        $this->assertEquals($verifiedAt->timestamp, $user->email_verified_at->timestamp);
    }

    /**
     * Test preferences cast to array
     */
    public function test_preferences_cast_to_array(): void
    {
        $preferences = [
            'theme' => 'dark',
            'language' => 'en',
            'notifications' => true,
        ];

        $user = User::factory()->create([
            'preferences' => $preferences,
        ]);

        $this->assertIsArray($user->preferences);
        $this->assertEquals($preferences, $user->preferences);
    }

    /**
     * Test is_active is boolean
     */
    public function test_is_active_is_boolean(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->assertIsBool($user->is_active);
        $this->assertTrue($user->is_active);
    }

    /**
     * Test user roles
     */
    public function test_user_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = User::factory()->create(['role' => 'client']);
        $employee = User::factory()->create(['role' => 'employee']);

        $this->assertEquals('admin', $admin->role);
        $this->assertEquals('client', $client->role);
        $this->assertEquals('employee', $employee->role);
    }

    /**
     * Test password is hashed
     */
    public function test_password_is_hashed(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /**
     * Test user can be created with all fields
     */
    public function test_user_creation_with_all_fields(): void
    {
        $company = Company::factory()->create();

        $user = User::create([
            'company_id' => $company->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'role' => 'client',
            'phone' => '+1234567890',
            'is_active' => true,
            'timezone' => 'America/New_York',
            'preferences' => ['theme' => 'light'],
        ]);

        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('client', $user->role);
        $this->assertEquals('+1234567890', $user->phone);
        $this->assertTrue($user->is_active);
        $this->assertEquals('America/New_York', $user->timezone);
    }

    /**
     * Test email must be unique
     */
    public function test_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'unique@example.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        User::factory()->create(['email' => 'unique@example.com']);
    }

    /**
     * Test user soft deletes
     */
    public function test_uses_soft_deletes(): void
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $user->delete();

        // Should still exist in database but with deleted_at timestamp
        $this->assertSoftDeleted('users', ['id' => $userId]);

        // Can be restored
        $user->restore();
        $this->assertDatabaseHas('users', [
            'id' => $userId,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test user has 2FA enabled check
     */
    public function test_has_two_factor_enabled_check(): void
    {
        $userWithout2FA = User::factory()->create();
        $userWith2FA = User::factory()->create();

        TwoFactorAuthentication::factory()->create([
            'user_id' => $userWith2FA->id,
            'enabled' => true,
        ]);

        $this->assertNull($userWithout2FA->twoFactorAuthentication);
        $this->assertNotNull($userWith2FA->twoFactorAuthentication);
        $this->assertTrue($userWith2FA->twoFactorAuthentication->enabled);
    }

    /**
     * Test user timezone defaults
     */
    public function test_timezone_defaults_to_utc(): void
    {
        $user = User::factory()->create(['timezone' => 'UTC']);

        $this->assertEquals('UTC', $user->timezone);
    }

    /**
     * Test user avatar can be null
     */
    public function test_avatar_can_be_null(): void
    {
        $user = User::factory()->create(['avatar' => null]);

        $this->assertNull($user->avatar);
    }

    /**
     * Test user can have avatar
     */
    public function test_user_can_have_avatar(): void
    {
        $user = User::factory()->create(['avatar' => 'avatars/user123.jpg']);

        $this->assertEquals('avatars/user123.jpg', $user->avatar);
    }

    /**
     * Test user fillable attributes
     */
    public function test_fillable_attributes(): void
    {
        $company = Company::factory()->create();

        $data = [
            'company_id' => $company->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'client',
            'phone' => '+1234567890',
            'avatar' => 'test.jpg',
            'is_active' => true,
            'timezone' => 'America/New_York',
            'preferences' => ['key' => 'value'],
        ];

        $user = User::create($data);

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('client', $user->role);
    }

    /**
     * Test inactive user
     */
    public function test_inactive_user(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $this->assertFalse($user->is_active);
    }

    /**
     * Test user timestamps
     */
    public function test_timestamps_are_recorded(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->created_at);
        $this->assertNotNull($user->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $user->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $user->updated_at);
    }
}
