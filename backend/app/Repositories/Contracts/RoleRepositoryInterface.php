<?php

namespace App\Repositories\Contracts;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface RoleRepositoryInterface
 *
 * Defines the contract for role repository data operations.
 *
 * @package App\Repositories\Contracts
 */
interface RoleRepositoryInterface
{
    /**
     * Get all role records ordered by name.
     *
     * @return Collection<int, Role>
     */
    public function getAll(): Collection;
}
