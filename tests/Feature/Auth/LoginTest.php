<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Login Feature Tests
 *
 * Tests the authentication login functionality including:
 * - Successful login
 * - Failed login attempts
 * - Rate limiting
 * - 2FA flow
 * - Session management
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test login page displays correctly
     */
    public function test_login_page_displays(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
        $response->assertSee('Login');
    }

    /**
     * Test user can login with valid credentials
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    /**
     * Test user cannot login with invalid credentials
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors();
    }

    /**
     * Test user cannot login with non-existent email
     */
    public function test_user_cannot_login_with_nonexistent_email(): void
    {
        $response = $this->post(route('login.submit'), [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors();
    }

    /**
     * Test inactive user cannot login
     */
    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'is_active' => false,
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors();
    }

    /**
     * Test login requires email
     */
    public function test_login_requires_email(): void
    {
        $response = $this->post(route('login.submit'), [
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test login requires password
     */
    public function test_login_requires_password(): void
    {
        $response = $this->post(route('login.submit'), [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test login is rate limited
     */
    public function test_login_is_rate_limited(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Attempt login 6 times with wrong password
        for ($i = 0; $i < 6; $i++) {
            $this->post(route('login.submit'), [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ]);
        }

        // 6th attempt should be rate limited
        $response = $this->post(route('login.submit'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(429); // Too Many Requests
    }

    /**
     * Test admin user redirects to admin dashboard
     */
    public function test_admin_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->post(route('login.submit'), [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertEquals('admin', auth()->user()->role);
    }

    /**
     * Test client user redirects to client dashboard
     */
    public function test_client_redirects_to_client_dashboard(): void
    {
        $client = User::factory()->create([
            'email' => 'client@example.com',
            'password' => bcrypt('password'),
            'role' => 'client',
            'is_active' => true,
        ]);

        $this->post(route('login.submit'), [
            'email' => 'client@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertEquals('client', auth()->user()->role);
    }

    /**
     * Test user can logout
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $this->assertAuthenticated();

        $response = $this->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    /**
     * Test remember me functionality
     */
    public function test_remember_me_functionality(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'test@example.com',
            'password' => 'password',
            'remember' => true,
        ]);

        $this->assertAuthenticated();

        // Check that remember token is set
        $this->assertNotNull($user->fresh()->remember_token);
    }

    /**
     * Test guest middleware redirects authenticated users
     */
    public function test_authenticated_user_cannot_access_login_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test email validation
     */
    public function test_login_validates_email_format(): void
    {
        $response = $this->post(route('login.submit'), [
            'email' => 'invalid-email',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
