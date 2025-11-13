<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Database Query Cache Middleware
 *
 * Enables query result caching for GET requests to reduce database load.
 */
class DatabaseQueryCache
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only cache GET requests
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        // Skip caching for authenticated admin users (they need fresh data)
        if ($request->user()?->role === 'admin') {
            return $next($request);
        }

        // Generate cache key based on URL and query parameters
        $cacheKey = $this->generateCacheKey($request);

        // Check if cached response exists
        if (Cache::has($cacheKey)) {
            $cachedResponse = Cache::get($cacheKey);

            // Return cached response with header indicating it's cached
            return response($cachedResponse['content'], $cachedResponse['status'])
                ->withHeaders([
                    ...$cachedResponse['headers'],
                    'X-Cache' => 'HIT',
                    'X-Cache-Key' => $cacheKey,
                ]);
        }

        // Enable query logging for this request
        DB::enableQueryLog();

        // Process request
        $response = $next($request);

        // Get query log
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // Cache successful responses (200, 201, 204)
        if (in_array($response->getStatusCode(), [200, 201, 204]) && count($queries) > 0) {
            $ttl = $this->determineTTL($request, count($queries));

            Cache::put($cacheKey, [
                'content' => $response->getContent(),
                'status' => $response->getStatusCode(),
                'headers' => $this->getCacheableHeaders($response),
            ], $ttl);

            // Add cache headers
            $response->headers->set('X-Cache', 'MISS');
            $response->headers->set('X-Cache-Key', $cacheKey);
            $response->headers->set('X-Query-Count', (string) count($queries));
        }

        return $response;
    }

    /**
     * Generate cache key from request
     *
     * @param Request $request
     * @return string
     */
    private function generateCacheKey(Request $request): string
    {
        $url = $request->fullUrl();
        $user = $request->user();
        $userId = $user ? $user->id : 'guest';

        // Include user ID in cache key for personalized content
        return 'query_cache:' . md5($url . ':' . $userId);
    }

    /**
     * Determine cache TTL based on request characteristics
     *
     * @param Request $request
     * @param int $queryCount
     * @return int TTL in seconds
     */
    private function determineTTL(Request $request, int $queryCount): int
    {
        // Heavy queries (5+) - cache longer (1 hour)
        if ($queryCount >= 5) {
            return 3600;
        }

        // Medium queries (2-4) - cache medium (30 minutes)
        if ($queryCount >= 2) {
            return 1800;
        }

        // Light queries (1) - cache short (5 minutes)
        return 300;
    }

    /**
     * Get headers that should be cached
     *
     * @param Response $response
     * @return array<string, string>
     */
    private function getCacheableHeaders(Response $response): array
    {
        $cacheableHeaders = ['Content-Type', 'Content-Language'];
        $headers = [];

        foreach ($cacheableHeaders as $header) {
            if ($response->headers->has($header)) {
                $headers[$header] = $response->headers->get($header);
            }
        }

        return $headers;
    }
}
