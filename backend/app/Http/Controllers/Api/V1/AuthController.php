<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class AuthController
 *
 * Handles API authentication endpoints (login, logout, and authenticated user profile).
 *
 * @package App\Http\Controllers\Api\V1
 */
class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Create a new controller instance.
     *
     * @param AuthService $authService
     */
    public function __construct(private readonly AuthService $authService)
    {
    }

    /**
     * Handle an incoming authentication login request.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->authService->login($request->validated(), $request);

        return $this->successResponse(
            data: [
                'user' => new UserResource($user),
                'abilities' => $this->authService->allowedAbilities($user),
            ],
            message: 'Login successful.'
        );
    }

    /**
     * Handle user logout and invalidate the active session.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request);

        return $this->successResponse(
            message: 'Logout successful.'
        );
    }

    /**
     * Get the currently authenticated user profile and allowed abilities.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return $this->successResponse(
            data: [
                'user' => new UserResource($user),
                'abilities' => $this->authService->allowedAbilities($user),
            ],
            message: 'User profile retrieved successfully.'
        );
    }
}
