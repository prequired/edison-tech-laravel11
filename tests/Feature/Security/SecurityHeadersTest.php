<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use Tests\TestCase;

/**
 * Security Headers Tests
 *
 * Validates that all required security headers are properly configured.
 */
class SecurityHeadersTest extends TestCase
{
    /**
     * Test Content-Security-Policy header is present
     */
    public function test_content_security_policy_header_is_present(): void
    {
        $response = $this->get('/');

        $response->assertHeader('Content-Security-Policy');

        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("default-src 'self'", $csp);
    }

    /**
     * Test Strict-Transport-Security header is present
     */
    public function test_strict_transport_security_header_is_present(): void
    {
        $response = $this->get('/');

        $response->assertHeader('Strict-Transport-Security');

        $hsts = $response->headers->get('Strict-Transport-Security');
        $this->assertStringContainsString('max-age=', $hsts);
        $this->assertStringContainsString('includeSubDomains', $hsts);
    }

    /**
     * Test X-Frame-Options header is present
     */
    public function test_x_frame_options_header_is_present(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Frame-Options');

        $xfo = $response->headers->get('X-Frame-Options');
        $this->assertContains($xfo, ['DENY', 'SAMEORIGIN']);
    }

    /**
     * Test X-Content-Type-Options header is present
     */
    public function test_x_content_type_options_header_is_present(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    /**
     * Test Referrer-Policy header is present
     */
    public function test_referrer_policy_header_is_present(): void
    {
        $response = $this->get('/');

        $response->assertHeader('Referrer-Policy');

        $referrer = $response->headers->get('Referrer-Policy');
        $this->assertNotEmpty($referrer);
    }

    /**
     * Test Permissions-Policy header is present
     */
    public function test_permissions_policy_header_is_present(): void
    {
        $response = $this->get('/');

        $response->assertHeader('Permissions-Policy');

        $permissions = $response->headers->get('Permissions-Policy');
        $this->assertNotEmpty($permissions);
    }

    /**
     * Test X-XSS-Protection header is present
     */
    public function test_x_xss_protection_header_is_present(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-XSS-Protection');

        $xss = $response->headers->get('X-XSS-Protection');
        $this->assertEquals('1; mode=block', $xss);
    }

    /**
     * Test security headers are present on API endpoints
     */
    public function test_security_headers_on_api_endpoints(): void
    {
        $response = $this->get('/api/health');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options');
        $response->assertHeader('Referrer-Policy');
    }

    /**
     * Test no sensitive headers are exposed
     */
    public function test_no_sensitive_headers_exposed(): void
    {
        $response = $this->get('/');

        // Should not expose server information
        $response->assertHeaderMissing('X-Powered-By');
        $response->assertHeaderMissing('Server');
    }

    /**
     * Test CORS headers configuration
     */
    public function test_cors_headers_properly_configured(): void
    {
        $response = $this->withHeaders([
            'Origin' => config('app.frontend_url', 'http://localhost:3000'),
        ])->get('/api/health');

        // Check CORS headers if configured
        if (config('cors.allowed_origins') !== ['*']) {
            $response->assertHeader('Access-Control-Allow-Origin');
        }
    }

    /**
     * Test CSP prevents inline scripts
     */
    public function test_csp_prevents_inline_scripts(): void
    {
        $response = $this->get('/');

        $csp = $response->headers->get('Content-Security-Policy');

        // Should not allow unsafe-inline for scripts
        $this->assertStringNotContainsString("script-src 'unsafe-inline'", $csp);
    }

    /**
     * Test all security headers on health check
     */
    public function test_all_security_headers_on_health_check(): void
    {
        $response = $this->get('/health');

        $requiredHeaders = [
            'X-Content-Type-Options',
            'X-Frame-Options',
            'X-XSS-Protection',
            'Referrer-Policy',
            'Content-Security-Policy',
        ];

        foreach ($requiredHeaders as $header) {
            $response->assertHeader($header);
        }
    }

    /**
     * Test security headers on static assets
     */
    public function test_security_headers_on_static_assets(): void
    {
        // Test on a likely static route (assuming /up is a health check)
        $response = $this->get('/up');

        if ($response->status() === 200) {
            $response->assertHeader('X-Content-Type-Options', 'nosniff');
        }
    }

    /**
     * Test cache-control headers for sensitive pages
     */
    public function test_cache_control_for_authenticated_pages(): void
    {
        $response = $this->get('/login');

        // Login page should have proper cache control
        $cacheControl = $response->headers->get('Cache-Control');

        if ($cacheControl) {
            // Should discourage caching of sensitive pages
            $this->assertTrue(
                str_contains($cacheControl, 'no-store') ||
                str_contains($cacheControl, 'no-cache') ||
                str_contains($cacheControl, 'private')
            );
        }
    }

    /**
     * Test security headers remain consistent across requests
     */
    public function test_security_headers_consistency(): void
    {
        $response1 = $this->get('/health');
        $response2 = $this->get('/health');

        $headers = ['X-Frame-Options', 'X-Content-Type-Options', 'X-XSS-Protection'];

        foreach ($headers as $header) {
            $this->assertEquals(
                $response1->headers->get($header),
                $response2->headers->get($header),
                "Security header {$header} is not consistent across requests"
            );
        }
    }

    /**
     * Test no security headers are duplicated
     */
    public function test_no_duplicate_security_headers(): void
    {
        $response = $this->get('/');

        $headers = $response->headers->all();

        foreach ($headers as $name => $values) {
            if (str_starts_with($name, 'x-') || str_starts_with($name, 'content-security-policy')) {
                $this->assertCount(
                    1,
                    $values,
                    "Security header {$name} is duplicated"
                );
            }
        }
    }
}
