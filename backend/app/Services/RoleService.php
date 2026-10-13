<?php

namespace App\Services;

use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class RoleService
 *
 * Handles business logic and operations related to roles.
 *
 * @package App\Services
 */
class RoleService
{
    /**
     * Create a new service instance.
     *
     * @param RoleRepositoryInterface $roleRepository
     */
    public function __construct(private readonly RoleRepositoryInterface $roleRepository)
    {
    }

    /**
     * Get all role records.
     *
     * @return Collection<int, Role>
     */
    public function getAll(): Collection
    {
        return $this->roleRepository->getAll();
    }
}
