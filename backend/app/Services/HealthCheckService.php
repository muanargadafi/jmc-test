<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Throwable;

/**
 * Class HealthCheckService
 *
 * Service responsible for performing health and readiness checks on external application
 * components such as the database and Redis, featuring caching and locking mechanisms.
 *
 * @package App\Services
 */
class HealthCheckService
{
    private const CACHE_KEY = 'health:readiness';
    private const CACHE_TTL_SECONDS = 5;
    private const LOCK_KEY = 'health:readiness:lock';
    private const LOCK_WAIT_SECONDS = 2;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Retrieves the application readiness status.
     * Uses cache if available, or runs checks directly with a distributed lock
     * to prevent high concurrency loads.
     *
     * @return array{isReady: bool, services: array<string, string>, checkedAt: string}
     */
    public function getReadiness(): array
    {
        $cached = Cache::get(self::CACHE_KEY);
        if ($cached !== null) {
            return $cached;
        }

        $lock = Cache::lock(self::LOCK_KEY, self::LOCK_WAIT_SECONDS);

        return $lock->block(self::LOCK_WAIT_SECONDS, function () {
            // Re-check in case another request already populated it
            // while we were waiting for the lock.
            $cached = Cache::get(self::CACHE_KEY);
            if ($cached !== null) {
                return $cached;
            }

            $result = $this->runReadinessChecks();
            Cache::put(self::CACHE_KEY, $result, self::CACHE_TTL_SECONDS);

            return $result;
        });
    }

    /**
     * Executes all individual service health checks (database and Redis).
     *
     * @return array{isReady: bool, services: array<string, string>, checkedAt: string}
     */
    private function runReadinessChecks(): array
    {
        $database = $this->checkService('database', fn() => DB::select('SELECT 1'));
        $redis = $this->checkService('redis', fn() => Redis::ping());

        return [
            'isReady' => $database === 'UP' && $redis === 'UP',
            'services' => [
                'database' => $database,
                'redis' => $redis,
            ],
            'checkedAt' => now()->toIso8601String(),
        ];
    }

    /**
     * Checks the availability of a specific service by executing a probe callback.
     *
     * @internal
     * @param string   $service The name of the service being checked (e.g., 'database', 'redis')
     * @param callable $probe   The callback function that executes the connection or test command
     * @return string Returns 'UP' on success, or 'DOWN' if an exception is thrown
     */
    private function checkService(string $service, callable $probe): string
    {
        try {
            $probe();

            return 'UP';
        } catch (Throwable $e) {
            Log::warning('Readiness check failed', [
                'service' => $service,
                'error' => $e->getMessage(),
            ]);

            return 'DOWN';
        }
    }
}
