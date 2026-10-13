<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Class UserService
 *
 * Handles business logic and operations related to users.
 *
 * @package App\Services
 */
class UserService
{
    /**
     * Create a new service instance.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    /**
     * Paginate user records.
     *
     * @param int $perPage
     * @param array<string, mixed> $filters
     * @return LengthAwarePaginator<int, User>
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->userRepository->paginate($perPage, $filters);
    }

    /**
     * Find a user record by its primary key ID.
     *
     * @param int $id
     * @return User
     */
    public function find(int $id): User
    {
        return $this->userRepository->findById($id);
    }

    /**
     * Create a new user record with hashed password.
     *
     * @param array<string, mixed> $data
     * @return User
     */
    public function create(array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = $this->userRepository->create($data);

        return $user->load('role');
    }

    /**
     * Update an existing user record.
     *
     * @param User $user
     * @param array<string, mixed> $data
     * @return User
     */
    public function update(User $user, array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user = $this->userRepository->update($user, $data);

        return $user->load('role');
    }

    /**
     * Delete a user record with self-deletion protection.
     *
     * @param User $user
     * @return bool
     * @throws ValidationException
     */
    public function delete(User $user): bool
    {
        if ($user->getKey() === Auth::id()) {
            throw ValidationException::withMessages([
                'user' => ['You cannot delete your own account.'],
            ]);
        }

        return $this->userRepository->delete($user);
    }

    /**
     * Toggle the lock or status of a user by ID.
     *
     * @param User $user
     * @return User
     */
    public function toggleLock(User $user): User
    {
        return $this->userRepository->toggleLock($user);
    }
}
