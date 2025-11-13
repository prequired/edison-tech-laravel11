<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTOs\CreateUserDTO;
use App\Models\Company;
use App\Models\TwoFactorAuthentication;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * User Service Unit Tests
 *
 * Tests the business logic in UserService including:
 * - User creation with password hashing
 * - User updates
 * - Two-factor authentication management
 * - Transaction integrity
 */
class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserService $userService;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = app(UserService::class);
        $this->company = Company::factory()->create();
    }

    /**
     * Test user creation with all fields
     */
    public function test_create_user_with_all_fields(): void
    {
        $dto = new CreateUserDTO(
            company_id: $this->company->id,
            name: 'John Doe',
            email: 'john@example.com',
            password: 'password123',
            role: 'client',
            phone: '+1234567890',
            avatar: null,
            is_active: true,
            timezone: 'America/New_York',
            preferences: ['theme' => 'dark']
        );

        $user = $this->userService->create($dto);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals($this->company->id, $user->company_id);
        $this->assertEquals('client', $user->role);
        $this->assertTrue($user->is_active);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ]);
    }

    /**
     * Test password is hashed during user creation
     */
    public function test_password_is_hashed_during_creation(): void
    {
        $dto = new CreateUserDTO(
            company_id: $this->company->id,
            name: 'Test User',
            email: 'test@example.com',
            password: 'plaintext-password',
            role: 'client',
            phone: null,
            avatar: null,
            is_active: true,
            timezone: 'UTC',
            preferences: []
        );

        $user = $this->userService->create($dto);

        // Password should be hashed, not plain text
        $this->assertNotEquals('plaintext-password', $user->password);
        $this->assertTrue(Hash::check('plaintext-password', $user->password));
    }

    /**
     * Test created user loads company relationship
     */
    public function test_created_user_loads_company_relationship(): void
    {
        $dto = new CreateUserDTO(
            company_id: $this->company->id,
            name: 'Test User',
            email: 'test@example.com',
            password: 'password',
            role: 'client',
            phone: null,
            avatar: null,
            is_active: true,
            timezone: 'UTC',
            preferences: []
        );

        $user = $this->userService->create($dto);

        $this->assertTrue($user->relationLoaded('company'));
        $this->assertInstanceOf(Company::class, $user->company);
        $this->assertEquals($this->company->id, $user->company->id);
    }

    /**
     * Test user update without password
     */
    public function test_update_user_without_password(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $updated = $this->userService->update($user, [
            'name' => 'Updated Name',
            'phone' => '+1234567890',
        ]);

        $this->assertEquals('Updated Name', $updated->name);
        $this->assertEquals('+1234567890', $updated->phone);
        $this->assertEquals('original@example.com', $updated->email);
    }

    /**
     * Test user update with password hashes the new password
     */
    public function test_update_user_with_password_hashes_it(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('old-password'),
        ]);

        $originalPassword = $user->password;

        $updated = $this->userService->update($user, [
            'password' => 'new-password',
        ]);

        // Password should be different from original
        $this->assertNotEquals($originalPassword, $updated->password);

        // New password should be properly hashed
        $this->assertTrue(Hash::check('new-password', $updated->password));
        $this->assertFalse(Hash::check('old-password', $updated->password));
    }

    /**
     * Test activate two-factor authentication
     */
    public function test_activate_two_factor_authentication(): void
    {
        $user = User::factory()->create();

        $secret = 'test-secret-key-12345';
        $recoveryCodes = ['code1', 'code2', 'code3', 'code4', 'code5'];

        $twoFactor = $this->userService->activateTwoFactor($user, $secret, $recoveryCodes);

        $this->assertInstanceOf(TwoFactorAuthentication::class, $twoFactor);
        $this->assertEquals($user->id, $twoFactor->user_id);
        $this->assertTrue($twoFactor->enabled);
        $this->assertNotNull($twoFactor->enabled_at);

        // Secret and recovery codes should be encrypted
        $this->assertNotEquals($secret, $twoFactor->secret);
        $this->assertEquals($secret, decrypt($twoFactor->secret));
    }

    /**
     * Test activate 2FA deletes existing 2FA record
     */
    public function test_activate_two_factor_deletes_existing_record(): void
    {
        $user = User::factory()->create();

        // Create first 2FA
        $first = TwoFactorAuthentication::factory()->create([
            'user_id' => $user->id,
        ]);

        $firstId = $first->id;

        // Activate new 2FA
        $second = $this->userService->activateTwoFactor(
            $user,
            'new-secret',
            ['code1', 'code2']
        );

        // First 2FA should be deleted
        $this->assertDatabaseMissing('two_factor_authentications', [
            'id' => $firstId,
        ]);

        // Second 2FA should exist
        $this->assertDatabaseHas('two_factor_authentications', [
            'id' => $second->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test deactivate two-factor authentication
     */
    public function test_deactivate_two_factor_authentication(): void
    {
        $user = User::factory()->create();

        $twoFactor = TwoFactorAuthentication::factory()->create([
            'user_id' => $user->id,
            'enabled' => true,
        ]);

        $result = $this->userService->deactivateTwoFactor($user);

        $this->assertTrue($result);

        $twoFactor->refresh();
        $this->assertFalse($twoFactor->enabled);
        $this->assertNotNull($twoFactor->disabled_at);
    }

    /**
     * Test deactivate 2FA throws exception when not enabled
     */
    public function test_deactivate_two_factor_throws_exception_when_not_enabled(): void
    {
        $user = User::factory()->create();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Two-factor authentication is not enabled for this user');

        $this->userService->deactivateTwoFactor($user);
    }

    /**
     * Test user creation is transactional
     */
    public function test_user_creation_is_transactional(): void
    {
        $this->expectException(\Exception::class);

        // This would test that if something fails during creation,
        // the user is not created
        $dto = new CreateUserDTO(
            company_id: 99999, // Non-existent company
            name: 'Test User',
            email: 'test@example.com',
            password: 'password',
            role: 'client',
            phone: null,
            avatar: null,
            is_active: true,
            timezone: 'UTC',
            preferences: []
        );

        try {
            $this->userService->create($dto);
        } catch (\Exception $e) {
            $this->assertDatabaseMissing('users', [
                'email' => 'test@example.com',
            ]);
            throw $e;
        }
    }

    /**
     * Test preferences are stored as JSON
     */
    public function test_preferences_stored_as_json(): void
    {
        $preferences = [
            'theme' => 'dark',
            'language' => 'en',
            'notifications' => true,
        ];

        $dto = new CreateUserDTO(
            company_id: $this->company->id,
            name: 'Test User',
            email: 'test@example.com',
            password: 'password',
            role: 'client',
            phone: null,
            avatar: null,
            is_active: true,
            timezone: 'UTC',
            preferences: $preferences
        );

        $user = $this->userService->create($dto);

        $this->assertEquals($preferences, $user->preferences);
        $this->assertIsArray($user->preferences);
    }

    /**
     * Test user can be created as inactive
     */
    public function test_user_can_be_created_as_inactive(): void
    {
        $dto = new CreateUserDTO(
            company_id: $this->company->id,
            name: 'Inactive User',
            email: 'inactive@example.com',
            password: 'password',
            role: 'client',
            phone: null,
            avatar: null,
            is_active: false,
            timezone: 'UTC',
            preferences: []
        );

        $user = $this->userService->create($dto);

        $this->assertFalse($user->is_active);
        $this->assertDatabaseHas('users', [
            'email' => 'inactive@example.com',
            'is_active' => false,
        ]);
    }
}
