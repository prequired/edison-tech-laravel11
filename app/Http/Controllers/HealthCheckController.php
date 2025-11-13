<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

/**
 * Health Check Controller
 *
 * Provides comprehensive health check endpoints for monitoring application status.
 * Used by load balancers, monitoring tools, and deployment scripts.
 */
class HealthCheckController extends Controller
{
    /**
     * Basic health check - application is running
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'service' => config('app.name'),
            'version' => config('app.version', '1.0.0'),
        ]);
    }

    /**
     * Comprehensive health check - all services
     *
     * Checks:
     * - Database connectivity
     * - Cache connectivity
     * - Storage accessibility
     * - Queue connectivity
     *
     * @return JsonResponse
     */
    public function comprehensive(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'queue' => $this->checkQueue(),
        ];

        $allHealthy = collect($checks)->every(fn ($check) => $check['status'] === 'healthy');

        $httpStatus = $allHealthy ? 200 : 503;

        return response()->json([
            'status' => $allHealthy ? 'healthy' : 'degraded',
            'timestamp' => now()->toIso8601String(),
            'service' => config('app.name'),
            'version' => config('app.version', '1.0.0'),
            'checks' => $checks,
        ], $httpStatus);
    }

    /**
     * Readiness check - application is ready to serve traffic
     *
     * @return JsonResponse
     */
    public function readiness(): JsonResponse
    {
        try {
            // Check database connection
            DB::connection()->getPdo();

            // Check if we can query the database
            DB::table('migrations')->count();

            return response()->json([
                'status' => 'ready',
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'not_ready',
                'timestamp' => now()->toIso8601String(),
                'error' => 'Database not accessible',
            ], 503);
        }
    }

    /**
     * Liveness check - application is alive (not deadlocked)
     *
     * @return JsonResponse
     */
    public function liveness(): JsonResponse
    {
        return response()->json([
            'status' => 'alive',
            'timestamp' => now()->toIso8601String(),
            'uptime' => $this->getUptime(),
        ]);
    }

    /**
     * Check database connectivity and performance
     *
     * @return array<string, mixed>
     */
    protected function checkDatabase(): array
    {
        try {
            $start = microtime(true);

            DB::connection()->getPdo();
            $result = DB::select('SELECT 1 as test');

            $duration = round((microtime(true) - $start) * 1000, 2);

            return [
                'status' => 'healthy',
                'response_time_ms' => $duration,
                'connection' => DB::connection()->getDatabaseName(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check cache connectivity
     *
     * @return array<string, mixed>
     */
    protected function checkCache(): array
    {
        try {
            $start = microtime(true);

            $testKey = 'health_check_' . time();
            $testValue = 'test';

            Cache::put($testKey, $testValue, 10);
            $retrieved = Cache::get($testKey);
            Cache::forget($testKey);

            $duration = round((microtime(true) - $start) * 1000, 2);

            if ($retrieved === $testValue) {
                return [
                    'status' => 'healthy',
                    'response_time_ms' => $duration,
                    'driver' => config('cache.default'),
                ];
            }

            return [
                'status' => 'unhealthy',
                'error' => 'Cache read/write test failed',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check storage accessibility
     *
     * @return array<string, mixed>
     */
    protected function checkStorage(): array
    {
        try {
            $start = microtime(true);

            $testFile = 'health_check_' . time() . '.txt';
            $testContent = 'test';

            Storage::put($testFile, $testContent);
            $retrieved = Storage::get($testFile);
            Storage::delete($testFile);

            $duration = round((microtime(true) - $start) * 1000, 2);

            if ($retrieved === $testContent) {
                return [
                    'status' => 'healthy',
                    'response_time_ms' => $duration,
                    'disk' => config('filesystems.default'),
                ];
            }

            return [
                'status' => 'unhealthy',
                'error' => 'Storage read/write test failed',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check queue connectivity
     *
     * @return array<string, mixed>
     */
    protected function checkQueue(): array
    {
        try {
            $connection = config('queue.default');

            // For Redis queue
            if ($connection === 'redis') {
                Redis::connection()->ping();

                return [
                    'status' => 'healthy',
                    'connection' => $connection,
                ];
            }

            // For database queue
            if ($connection === 'database') {
                DB::table('jobs')->count();

                return [
                    'status' => 'healthy',
                    'connection' => $connection,
                ];
            }

            return [
                'status' => 'healthy',
                'connection' => $connection,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get application uptime (if available)
     *
     * @return string|null
     */
    protected function getUptime(): ?string
    {
        try {
            if (function_exists('proc_open')) {
                $uptime = shell_exec('uptime -p');
                return $uptime ? trim($uptime) : null;
            }
        } catch (\Exception $e) {
            // Uptime not available
        }

        return null;
    }
}
