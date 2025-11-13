<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Performance Monitoring Middleware
 *
 * Tracks request performance metrics including:
 * - Response time
 * - Database query count
 * - Memory usage
 * - Peak memory usage
 */
class PerformanceMonitoring
{
    /**
     * Performance thresholds for alerting
     */
    private const SLOW_RESPONSE_THRESHOLD = 1000; // 1 second
    private const QUERY_COUNT_THRESHOLD = 50;
    private const MEMORY_THRESHOLD = 50 * 1024 * 1024; // 50MB

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip performance monitoring for specific paths
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        // Record start time and memory
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        // Enable query logging
        DB::enableQueryLog();

        // Process request
        $response = $next($request);

        // Calculate performance metrics
        $metrics = $this->calculateMetrics($startTime, $startMemory);

        // Add performance headers to response
        $this->addPerformanceHeaders($response, $metrics);

        // Log slow requests
        if ($metrics['response_time_ms'] > self::SLOW_RESPONSE_THRESHOLD) {
            $this->logSlowRequest($request, $metrics);
        }

        // Log requests with excessive queries
        if ($metrics['query_count'] > self::QUERY_COUNT_THRESHOLD) {
            $this->logExcessiveQueries($request, $metrics);
        }

        // Log high memory usage
        if ($metrics['peak_memory_mb'] > self::MEMORY_THRESHOLD / 1024 / 1024) {
            $this->logHighMemoryUsage($request, $metrics);
        }

        // Disable query logging
        DB::disableQueryLog();

        return $response;
    }

    /**
     * Check if performance monitoring should be skipped
     *
     * @param Request $request
     * @return bool
     */
    private function shouldSkip(Request $request): bool
    {
        $skipPaths = [
            '/health',
            '/health/*',
            '/_debugbar',
            '/telescope',
        ];

        foreach ($skipPaths as $path) {
            if ($request->is($path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate performance metrics
     *
     * @param float $startTime
     * @param int $startMemory
     * @return array<string, mixed>
     */
    private function calculateMetrics(float $startTime, int $startMemory): array
    {
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        $peakMemory = memory_get_peak_usage();

        $queries = DB::getQueryLog();

        return [
            'response_time_ms' => round(($endTime - $startTime) * 1000, 2),
            'memory_used_mb' => round(($endMemory - $startMemory) / 1024 / 1024, 2),
            'peak_memory_mb' => round($peakMemory / 1024 / 1024, 2),
            'query_count' => count($queries),
            'query_time_ms' => $this->calculateQueryTime($queries),
        ];
    }

    /**
     * Calculate total query execution time
     *
     * @param array $queries
     * @return float
     */
    private function calculateQueryTime(array $queries): float
    {
        $totalTime = 0;

        foreach ($queries as $query) {
            $totalTime += $query['time'] ?? 0;
        }

        return round($totalTime, 2);
    }

    /**
     * Add performance headers to response
     *
     * @param Response $response
     * @param array<string, mixed> $metrics
     * @return void
     */
    private function addPerformanceHeaders(Response $response, array $metrics): void
    {
        $response->headers->set('X-Response-Time', (string) $metrics['response_time_ms'] . 'ms');
        $response->headers->set('X-Query-Count', (string) $metrics['query_count']);
        $response->headers->set('X-Query-Time', (string) $metrics['query_time_ms'] . 'ms');
        $response->headers->set('X-Memory-Peak', (string) $metrics['peak_memory_mb'] . 'MB');
    }

    /**
     * Log slow request
     *
     * @param Request $request
     * @param array<string, mixed> $metrics
     * @return void
     */
    private function logSlowRequest(Request $request, array $metrics): void
    {
        Log::warning('Slow request detected', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'response_time_ms' => $metrics['response_time_ms'],
            'query_count' => $metrics['query_count'],
            'query_time_ms' => $metrics['query_time_ms'],
            'user_id' => $request->user()?->id,
        ]);
    }

    /**
     * Log request with excessive queries
     *
     * @param Request $request
     * @param array<string, mixed> $metrics
     * @return void
     */
    private function logExcessiveQueries(Request $request, array $metrics): void
    {
        Log::warning('Excessive database queries detected', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'query_count' => $metrics['query_count'],
            'query_time_ms' => $metrics['query_time_ms'],
            'user_id' => $request->user()?->id,
            'queries' => DB::getQueryLog(),
        ]);
    }

    /**
     * Log high memory usage
     *
     * @param Request $request
     * @param array<string, mixed> $metrics
     * @return void
     */
    private function logHighMemoryUsage(Request $request, array $metrics): void
    {
        Log::warning('High memory usage detected', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'peak_memory_mb' => $metrics['peak_memory_mb'],
            'memory_used_mb' => $metrics['memory_used_mb'],
            'user_id' => $request->user()?->id,
        ]);
    }
}
