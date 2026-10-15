<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 *
 * Handles API requests and responses related to user management.
 *
 * @package App\Http\Controllers\Api\V1
 */
class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param UserService $userService
     */
    public function __construct(private readonly UserService $userService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('limit', 10);
        $filters = $request->only(['search', 'role_id']);

        $users = $this->userService->paginate($perPage, $filters);

        return $this->successResponse(
            data: UserResource::collection($users->items()),
            meta: [
                'page' => $users->currentPage(),
                'limit' => $users->perPage(),
                'totalItems' => $users->total(),
                'totalPages' => $users->lastPage(),
            ],
            message: 'User data retrieved successfully.'
        );
    }

    /**
     * Get all user options for dropdown selections.
     *
     * @return JsonResponse
     */
    public function options(): JsonResponse
    {
        return $this->successResponse(
            data: UserResource::collection($this->userService->options())->resolve(),
            message: 'User options retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());

        return $this->successResponse(
            data: new UserResource($user),
            message: 'User created successfully.',
            code: Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return $this->successResponse(
            data: new UserResource($user->load('role')),
            message: 'User data retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->update($user, $request->validated());

        return $this->successResponse(
            data: new UserResource($user),
            message: 'User updated successfully.'
        );
    }

    /**
     * Toggle the lock or status of the specified user.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function toggleLock(User $user): JsonResponse
    {
        $user = $this->userService->toggleLock($user);

        return $this->successResponse(
            data: new UserResource($user),
            message: 'User status updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        $this->userService->delete($user);

        return $this->successResponse(
            message: 'User deleted successfully.'
        );
    }
}
