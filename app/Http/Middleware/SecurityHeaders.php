<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security Headers Middleware
 *
 * Adds comprehensive security headers to all HTTP responses to protect against
 * common web vulnerabilities including XSS, clickjacking, MIME sniffing, and more.
 */
class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Content Security Policy - Prevents XSS and injection attacks
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com; " .
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; " .
            "font-src 'self' https://fonts.gstatic.com; " .
            "img-src 'self' data: https:; " .
            "connect-src 'self'; " .
            "frame-ancestors 'self'; " .
            "base-uri 'self'; " .
            "form-action 'self';"
        );

        // HTTP Strict Transport Security - Enforces HTTPS
        $response->headers->set(
            'Strict-Transport-Security',
            'max-age=31536000; includeSubDomains; preload'
        );

        // X-Frame-Options - Prevents clickjacking attacks
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // X-Content-Type-Options - Prevents MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // X-XSS-Protection - Enables browser XSS filtering
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer-Policy - Controls referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions-Policy - Controls browser features
        $response->headers->set(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=(), payment=()'
        );

        // Remove server identification headers
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
