<?php

namespace App\Repositories\Eloquents;

use App\Models\IncomingGood;
use App\Repositories\Contracts\IncomingGoodRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class IncomingGoodRepository
 *
 * Eloquent implementation of the IncomingGoodRepositoryInterface.
 *
 * @package App\Repositories\Eloquents
 */
class IncomingGoodRepository implements IncomingGoodRepositoryInterface
{
    /**
     * Create a new repository instance.
     *
     * @param IncomingGood $model
     */
    public function __construct(private readonly IncomingGood $model)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['items', 'operator', 'subCategory']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('origin', 'ilike', "%{$search}%")
                    ->orWhere('letter_number', 'ilike', "%{$search}%");
            });
        }

        if (!empty($filters['category_id'])) {
            $query->whereHas('subCategory', function ($q) use ($filters) {
                $q->where('category_id', $filters['category_id']);
            });
        }

        if (!empty($filters['sub_category_id'])) {
            $query->where('sub_category_id', $filters['sub_category_id']);
        }

        if (!empty($filters['operator_id'])) {
            $query->where('operator_id', $filters['operator_id']);
        }

        if (!empty($filters['year'])) {
            $query->whereYear('received_date', $filters['year']);
        }


        if (isset($filters['is_verified']) && $filters['is_verified'] !== '') {
            $query->where('is_verified', filter_var($filters['is_verified'], FILTER_VALIDATE_BOOLEAN));
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?IncomingGood
    {
        return $this->model->with(['operator', 'subCategory.category', 'items', 'verifier'])->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): IncomingGood
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(IncomingGood $incomingGood, array $data): IncomingGood
    {
        $incomingGood->update($data);

        return $incomingGood->fresh(['operator', 'subCategory', 'items', 'verifier']);
    }

    /**
     * {@inheritdoc}
     */
    public function replaceItems(IncomingGood $incomingGood, array $items): IncomingGood
    {
        $incomingGood->items()->delete();
        $incomingGood->items()->createMany($items);

        return $incomingGood->fresh(['operator', 'subCategory', 'items', 'verifier']);
    }

    /**
     * {@inheritdoc}
     */
    public function verify(IncomingGood $incomingGood, int $verifiedBy): IncomingGood
    {
        $incomingGood->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => $verifiedBy,
        ]);

        return $incomingGood->fresh(['operator', 'subCategory', 'items', 'verifier']);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(IncomingGood $incomingGood): bool
    {
        return (bool) $incomingGood->delete();
    }
}
