<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

/**
 * Class CategoryService
 *
 * Handles business logic and operations related to categories.
 *
 * @package App\Services
 */
class CategoryService
{
    /**
     * Create a new service instance.
     *
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(private readonly CategoryRepositoryInterface $categoryRepository)
    {
    }

    /**
     * Paginate category records.
     *
     * @param int $perPage
     * @param array<string, mixed> $filters
     * @return LengthAwarePaginator<int, Category>
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->categoryRepository->paginate($perPage, $filters);
    }

    /**
     * Get all category options for dropdown selections.
     *
     * @return Collection<int, Category>
     */
    public function options(): Collection
    {
        return $this->categoryRepository->options();
    }

    /**
     * Find a category record by its primary key ID.
     *
     * @param int $id
     * @return Category
     */
    public function find(int $id): Category
    {
        return $this->categoryRepository->findById($id);
    }

    /**
     * Create a new category record.
     *
     * @param array<string, mixed> $data
     * @return Category
     */
    public function create(array $data): Category
    {
        return $this->categoryRepository->create($data);
    }

    /**
     * Update an existing category record.
     *
     * @param Category $category
     * @param array<string, mixed> $data
     * @return Category
     */
    public function update(Category $category, array $data): Category
    {
        return $this->categoryRepository->update($category, $data);
    }

    /**
     * Delete a category record.
     *
     * @param Category $category
     * @return bool
     * @throws ValidationException
     */
    public function delete(Category $category): bool
    {
        return $this->categoryRepository->delete($category);
    }
}
