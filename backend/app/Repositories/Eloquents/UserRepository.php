<?php

namespace App\Repositories\Eloquents;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class UserRepository
 *
 * Eloquent implementation of the UserRepositoryInterface.
 *
 * @package App\Repositories\Eloquents
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * Create a new repository instance.
     *
     * @param User $model
     */
    public function __construct(private readonly User $model)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('role');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('username', 'ilike', "%{$search}%");
            });
        }

        if (!empty($filters['role_id'])) {
            $query->where('role_id', $filters['role_id']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?User
    {
        return $this->model->with('role')->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh('role');
    }

    /**
     * {@inheritdoc}
     */
    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function toggleLock(User $user): User
    {
        $user->update(['is_locked' => !$user->is_locked]);

        return $user->fresh();
    }
}
