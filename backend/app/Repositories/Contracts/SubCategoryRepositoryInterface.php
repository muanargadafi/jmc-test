<?php

namespace App\Repositories\Contracts;

use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface SubCategoryRepositoryInterface
 *
 * Defines the contract for subcategory repository data operations.
 *
 * @package App\Repositories\Contracts
 */
interface SubCategoryRepositoryInterface
{
    /**
     * Paginate subcategory records based on per-page limit and optional filters.
     *
     * @param int $perPage
     * @param array<string, mixed> $filters
     * @return LengthAwarePaginator<int, SubCategory>
     */
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator;

    /**
     * Get all subcategory options (id, name, price_limit) for dropdown menus.
     *
     * @return Collection<int, SubCategory>
     */
    public function options(): Collection;

    /**
     * Find a subcategory record by its primary key ID.
     *
     * @param int $id
     * @return SubCategory|null
     */
    public function findById(int $id): ?SubCategory;

    /**
     * Create a new subcategory record.
     *
     * @param array<string, mixed> $data
     * @return SubCategory
     */
    public function create(array $data): SubCategory;

    /**
     * Update an existing subcategory record.
     *
     * @param SubCategory $subCategory
     * @param array<string, mixed> $data
     * @return SubCategory
     */
    public function update(SubCategory $subCategory, array $data): SubCategory;

    /**
     * Delete a subcategory record.
     *
     * @param SubCategory $subCategory
     * @return bool
     */
    public function delete(SubCategory $subCategory): bool;
}
