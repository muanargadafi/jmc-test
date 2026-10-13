<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface CategoryRepositoryInterface
 *
 * Defines the contract for category repository data operations.
 *
 * @package App\Repositories\Contracts
 */
interface CategoryRepositoryInterface
{
    /**
     * Paginate category records based on per-page limit and optional filters.
     *
     * @param int $perPage
     * @param array<string, mixed> $filters
     * @return LengthAwarePaginator<int, Category>
     */
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator;

    /**
     * All categories (id, code, name) for dropdown options.
     *
     * @return Collection<int, Category>
     */
    public function options(): Collection;

    /**
     * Find a category record by its primary key ID.
     *
     * @param int $id
     * @return Category|null
     */
    public function findById(int $id): ?Category;

    /**
     * Create a new category record.
     *
     * @param array<string, mixed> $data
     * @return Category
     */
    public function create(array $data): Category;

    /**
     * Update an existing category record.
     *
     * @param Category $category
     * @param array<string, mixed> $data
     * @return Category
     */
    public function update(Category $category, array $data): Category;

    /**
     * Delete a category record.
     *
     * @param Category $category
     * @return bool
     */
    public function delete(Category $category): bool;
}
