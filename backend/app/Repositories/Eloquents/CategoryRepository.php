<?php

namespace App\Repositories\Eloquents;


use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class CategoryRepository
 *
 * Eloquent implementation of the CategoryRepositoryInterface.
 *
 * @package App\Repositories\Eloquents
 */
class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * Create a new repository instance.
     *
     * @param Category $model
     */
    public function __construct(private readonly Category $model)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'ilike', "%{$search}%")
                    ->orWhere('name', 'ilike', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function options(): Collection
    {
        return $this->model->newQuery()->orderBy('name')->get(['id', 'code', 'name']);
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?Category
    {
        return $this->model->with('role')->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): Category
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category->refresh();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Category $category): bool
    {
        return (bool) $category->delete();
    }
}
