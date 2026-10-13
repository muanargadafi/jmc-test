<?php

namespace App\Services;

use App\Models\SubCategory;
use App\Repositories\Contracts\SubCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

/**
 * Class SubCategoryService
 *
 * Handles business logic and operations related to subcategories.
 *
 * @package App\Services
 */
class SubCategoryService
{
    /**
     * Create a new service instance.
     *
     * @param SubCategoryRepositoryInterface $subCategoryRepository
     */
    public function __construct(private readonly SubCategoryRepositoryInterface $subCategoryRepository)
    {
    }

    /**
     * Paginate subcategory records.
     *
     * @param int $perPage
     * @param array<string, mixed> $filters
     * @return LengthAwarePaginator<int, SubCategory>
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->subCategoryRepository->paginate($perPage, $filters);
    }

    /**
     * Get all subcategory options for dropdown selections.
     *
     * @return Collection<int, SubCategory>
     */
    public function options(): Collection
    {
        return $this->subCategoryRepository->options();
    }

    /**
     * Find a subcategory record by its primary key ID.
     *
     * @param int $id
     * @return SubCategory
     */
    public function find(int $id): SubCategory
    {
        return $this->subCategoryRepository->findById($id);
    }

    /**
     * Create a new subcategory record.
     *
     * @param array<string, mixed> $data
     * @return SubCategory
     */
    public function create(array $data): SubCategory
    {
        return $this->subCategoryRepository->create($data);
    }

    /**
     * Update an existing subcategory record.
     *
     * @param SubCategory $subCategory
     * @param array<string, mixed> $data
     * @return SubCategory
     */
    public function update(SubCategory $subCategory, array $data): SubCategory
    {
        return $this->subCategoryRepository->update($subCategory, $data);
    }

    /**
     * Delete a subcategory record.
     *
     * @param SubCategory $subCategory
     * @return bool
     * @throws ValidationException
     */
    public function delete(SubCategory $subCategory): bool
    {
        return $this->subCategoryRepository->delete($subCategory);
    }
}
