<?php

namespace App\Repositories\Eloquents;

use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class RoleRepository
 *
 * Eloquent implementation of the RoleRepositoryInterface.
 *
 * @package App\Repositories\Eloquents
 */
class RoleRepository implements RoleRepositoryInterface
{
    /**
     * Create a new repository instance.
     *
     * @param Role $model
     */
    public function __construct(private readonly Role $model)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(): Collection
    {
        return $this->model->newQuery()->orderBy('name')->get();
    }
}
