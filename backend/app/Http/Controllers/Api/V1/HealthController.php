<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\HealthCheckService;
use Illuminate\Http\JsonResponse;

/**
 * Class HealthController
 *
 * Manages API endpoints for application health checks (liveness and readiness).
 *
 * @package App\Http\Controllers\Api\V1
 */
class HealthController extends Controller
{
    /**
     * Creates a new HealthController instance.
     *
     * @param HealthCheckService $healthCheckService The system health check service
     */
    public function __construct(
        private readonly HealthCheckService $healthCheckService,
    ) {
    }

    /**
     * Checks the application liveness status (whether the application is running).
     *
     * @return JsonResponse
     */
    public function live(): JsonResponse
    {
        return response()->json([
            'status' => 'UP',
            'uptime' => $this->requestUptime(),
        ]);
    }

    /**
     * Checks the application readiness status (whether it's ready to handle traffic & database/Redis are operational).
     *
     * @return JsonResponse
     */
    public function ready(): JsonResponse
    {
        $readiness = $this->healthCheckService->getReadiness();

        return response()
            ->json([
                'status' => $readiness['isReady'] ? 'UP' : 'DEGRADED',
                'uptime' => $this->requestUptime(),
                'checkedAt' => $readiness['checkedAt'],
                'services' => $readiness['services'],
            ], $readiness['isReady'] ? 200 : 503)
            ->header('Cache-Control', 'no-store');
    }

    /**
     * Calculates the request duration/uptime since application boot (using the LARAVEL_START constant).
     *
     * @return float Elapsed time in seconds rounded to 3 decimal places
     */
    private function requestUptime(): float
    {
        return round(microtime(true) - LARAVEL_START, 3);
    }
}
