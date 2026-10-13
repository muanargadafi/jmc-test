<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param RoleService $roleService
     */
    public function __construct(private readonly RoleService $roleService)
    {
    }

    /**
     * Display a listing of the roles.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $roles = $this->roleService->getAll();

        return $this->successResponse(
            data: RoleResource::collection($roles),
            message: 'Role data retrieved successfully.'
        );
    }
}
