<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Cache Service
 *
 * Centralized caching service for optimizing database queries
 * and reducing response times.
 *
 * Uses Redis by default with configurable TTL values.
 */
class CacheService
{
    /**
     * Cache TTL values (in seconds)
     */
    private const TTL_SHORT = 300;      // 5 minutes
    private const TTL_MEDIUM = 1800;    // 30 minutes
    private const TTL_LONG = 3600;      // 1 hour
    private const TTL_DAY = 86400;      // 24 hours

    /**
     * Cache key prefixes
     */
    private const PREFIX_USER = 'user';
    private const PREFIX_COMPANY = 'company';
    private const PREFIX_PROJECT = 'project';
    private const PREFIX_INVOICE = 'invoice';
    private const PREFIX_TASK = 'task';
    private const PREFIX_STATS = 'stats';

    /**
     * Get or cache user data
     *
     * @param int $userId
     * @param callable $callback
     * @return mixed
     */
    public function getUser(int $userId, callable $callback): mixed
    {
        $key = $this->buildKey(self::PREFIX_USER, $userId);
        return Cache::remember($key, self::TTL_MEDIUM, $callback);
    }

    /**
     * Get or cache company data
     *
     * @param int $companyId
     * @param callable $callback
     * @return mixed
     */
    public function getCompany(int $companyId, callable $callback): mixed
    {
        $key = $this->buildKey(self::PREFIX_COMPANY, $companyId);
        return Cache::remember($key, self::TTL_LONG, $callback);
    }

    /**
     * Get or cache project data
     *
     * @param int $projectId
     * @param callable $callback
     * @return mixed
     */
    public function getProject(int $projectId, callable $callback): mixed
    {
        $key = $this->buildKey(self::PREFIX_PROJECT, $projectId);
        return Cache::remember($key, self::TTL_MEDIUM, $callback);
    }

    /**
     * Get or cache invoice data
     *
     * @param int $invoiceId
     * @param callable $callback
     * @return mixed
     */
    public function getInvoice(int $invoiceId, callable $callback): mixed
    {
        $key = $this->buildKey(self::PREFIX_INVOICE, $invoiceId);
        return Cache::remember($key, self::TTL_SHORT, $callback);
    }

    /**
     * Get or cache task data
     *
     * @param int $taskId
     * @param callable $callback
     * @return mixed
     */
    public function getTask(int $taskId, callable $callback): mixed
    {
        $key = $this->buildKey(self::PREFIX_TASK, $taskId);
        return Cache::remember($key, self::TTL_SHORT, $callback);
    }

    /**
     * Get or cache dashboard statistics
     *
     * @param string $key
     * @param callable $callback
     * @return mixed
     */
    public function getStats(string $key, callable $callback): mixed
    {
        $cacheKey = $this->buildKey(self::PREFIX_STATS, $key);
        return Cache::remember($cacheKey, self::TTL_SHORT, $callback);
    }

    /**
     * Get or cache project list with filters
     *
     * @param array $filters
     * @param callable $callback
     * @return mixed
     */
    public function getProjectList(array $filters, callable $callback): mixed
    {
        $key = $this->buildKey(self::PREFIX_PROJECT, 'list', md5(serialize($filters)));
        return Cache::remember($key, self::TTL_SHORT, $callback);
    }

    /**
     * Invalidate user cache
     *
     * @param int $userId
     * @return bool
     */
    public function invalidateUser(int $userId): bool
    {
        $key = $this->buildKey(self::PREFIX_USER, $userId);
        return Cache::forget($key);
    }

    /**
     * Invalidate company cache
     *
     * @param int $companyId
     * @return bool
     */
    public function invalidateCompany(int $companyId): bool
    {
        $key = $this->buildKey(self::PREFIX_COMPANY, $companyId);
        return Cache::forget($key);
    }

    /**
     * Invalidate project cache
     *
     * @param int $projectId
     * @return bool
     */
    public function invalidateProject(int $projectId): bool
    {
        $key = $this->buildKey(self::PREFIX_PROJECT, $projectId);
        return Cache::forget($key);
    }

    /**
     * Invalidate invoice cache
     *
     * @param int $invoiceId
     * @return bool
     */
    public function invalidateInvoice(int $invoiceId): bool
    {
        $key = $this->buildKey(self::PREFIX_INVOICE, $invoiceId);
        return Cache::forget($key);
    }

    /**
     * Invalidate task cache
     *
     * @param int $taskId
     * @return bool
     */
    public function invalidateTask(int $taskId): bool
    {
        $key = $this->buildKey(self::PREFIX_TASK, $taskId);
        return Cache::forget($key);
    }

    /**
     * Invalidate all stats cache
     *
     * @return bool
     */
    public function invalidateAllStats(): bool
    {
        return Cache::tags([self::PREFIX_STATS])->flush();
    }

    /**
     * Invalidate project list cache
     *
     * @return bool
     */
    public function invalidateProjectList(): bool
    {
        $pattern = $this->buildKey(self::PREFIX_PROJECT, 'list', '*');

        // For Redis, use pattern matching
        if (config('cache.default') === 'redis') {
            $keys = Cache::getRedis()->keys($pattern);
            if (!empty($keys)) {
                foreach ($keys as $key) {
                    Cache::forget($key);
                }
            }
        }

        return true;
    }

    /**
     * Cache data with custom TTL
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @return bool
     */
    public function put(string $key, mixed $value, int $ttl = self::TTL_MEDIUM): bool
    {
        return Cache::put($key, $value, $ttl);
    }

    /**
     * Get cached data
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::get($key, $default);
    }

    /**
     * Check if key exists in cache
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return Cache::has($key);
    }

    /**
     * Remove item from cache
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        return Cache::forget($key);
    }

    /**
     * Clear all cache
     *
     * @return bool
     */
    public function flush(): bool
    {
        return Cache::flush();
    }

    /**
     * Remember value forever (until manually cleared)
     *
     * @param string $key
     * @param callable $callback
     * @return mixed
     */
    public function rememberForever(string $key, callable $callback): mixed
    {
        return Cache::rememberForever($key, $callback);
    }

    /**
     * Increment cached value
     *
     * @param string $key
     * @param int $value
     * @return int|bool
     */
    public function increment(string $key, int $value = 1): int|bool
    {
        return Cache::increment($key, $value);
    }

    /**
     * Decrement cached value
     *
     * @param string $key
     * @param int $value
     * @return int|bool
     */
    public function decrement(string $key, int $value = 1): int|bool
    {
        return Cache::decrement($key, $value);
    }

    /**
     * Build cache key from parts
     *
     * @param string ...$parts
     * @return string
     */
    private function buildKey(string ...$parts): string
    {
        return implode(':', $parts);
    }

    /**
     * Get TTL constants for external use
     *
     * @return array<string, int>
     */
    public static function getTTLConstants(): array
    {
        return [
            'short' => self::TTL_SHORT,
            'medium' => self::TTL_MEDIUM,
            'long' => self::TTL_LONG,
            'day' => self::TTL_DAY,
        ];
    }
}
