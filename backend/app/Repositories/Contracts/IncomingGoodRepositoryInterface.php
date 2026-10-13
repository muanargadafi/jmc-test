<?php

namespace App\Repositories\Contracts;

use App\Models\IncomingGood;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface IncomingGoodRepositoryInterface
 *
 * Defines the contract for incoming goods repository data operations.
 *
 * @package App\Repositories\Contracts
 */
interface IncomingGoodRepositoryInterface
{
    /**
     * Paginate incoming goods records based on per-page limit and optional filters.
     *
     * @param int $perPage
     * @param array<string, mixed> $filters
     * @return LengthAwarePaginator<int, IncomingGood>
     */
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator;

    /**
     * Find an incoming good record by its primary key ID.
     *
     * @param int $id
     * @return IncomingGood|null
     */
    public function findById(int $id): ?IncomingGood;

    /**
     * Create a new incoming good record.
     *
     * @param array<string, mixed> $data
     * @return IncomingGood
     */
    public function create(array $data): IncomingGood;

    /**
     * Update an existing incoming good record.
     *
     * @param IncomingGood $incomingGood
     * @param array<string, mixed> $data
     * @return IncomingGood
     */
    public function update(IncomingGood $incomingGood, array $data): IncomingGood;

    /**
     * Replace all item rows belonging to an incoming good record.
     *
     * @param IncomingGood $incomingGood
     * @param array<int, array<string, mixed>> $items
     * @return IncomingGood
     */
    public function replaceItems(IncomingGood $incomingGood, array $items): IncomingGood;

    /**
     * Mark an incoming good record as verified.
     *
     * @param IncomingGood $incomingGood
     * @param int $verifiedBy
     * @return IncomingGood
     */
    public function verify(IncomingGood $incomingGood, int $verifiedBy): IncomingGood;

    /**
     * Delete an incoming good record.
     *
     * @param IncomingGood $incomingGood
     * @return bool
     */
    public function delete(IncomingGood $incomingGood): bool;
}
