<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface UserRepositoryInterface
 *
 * Defines the contract for user repository data operations.
 *
 * @package App\Repositories\Contracts
 */
interface UserRepositoryInterface
{
    /**
     * Paginate user records based on per-page limit and optional filters.
     *
     * @param int $perPage
     * @param array<string, mixed> $filters
     * @return LengthAwarePaginator<int, User>
     */
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator;

    /**
     * Get all user options (id, username, name) for dropdown menus.
     *
     * @return Collection<int, User>
     */
    public function options(): Collection;


    /**
     * Find a user record by its primary key ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User;

    /**
     * Create a new user record.
     *
     * @param array<string, mixed> $data
     * @return User
     */
    public function create(array $data): User;

    /**
     * Update an existing user record.
     *
     * @param User $user
     * @param array<string, mixed> $data
     * @return User
     */
    public function update(User $user, array $data): User;

    /**
     * Delete a user record.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool;

    /**
     * Toggle the lock or status of a user record.
     *
     * @param User $user
     * @return User
     */
    public function toggleLock(User $user): User;
}
