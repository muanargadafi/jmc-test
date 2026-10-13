<?php

namespace App\Repositories\Eloquents;

use App\Models\SubCategory;
use App\Repositories\Contracts\SubCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class SubCategoryRepository
 *
 * Eloquent implementation of the SubCategoryRepositoryInterface.
 *
 * @package App\Repositories\Eloquents
 */
class SubCategoryRepository implements SubCategoryRepositoryInterface
{
    /**
     * Create a new repository instance.
     *
     * @param SubCategory $model
     */
    public function __construct(private readonly SubCategory $model)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('category');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhereHas('category', function ($category) use ($search) {
                        $category->where('name', 'ilike', "%{$search}%");
                    });
            });


        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function options(): Collection
    {
        return $this->model->newQuery()->orderBy('name')->get(['id', 'name', 'price_limit']);
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?SubCategory
    {
        return $this->model->with('category')->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): SubCategory
    {
        $subCategory = $this->model->create($data);

        return $subCategory->fresh('category');
    }

    /**
     * {@inheritdoc}
     */
    public function update(SubCategory $subCategory, array $data): SubCategory
    {
        $subCategory->update($data);

        return $subCategory->fresh('category');
    }

    /**
     * {@inheritdoc}
     */
    public function delete(SubCategory $subCategory): bool
    {
        return (bool) $subCategory->delete();
    }
}
