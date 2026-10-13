<?php

namespace App\Services;

use App\Models\IncomingGood;
use App\Repositories\Contracts\IncomingGoodRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Class IncomingGoodService
 *
 * Handles business logic and operations related to incoming goods.
 *
 * @package App\Services
 */
class IncomingGoodService
{
    private const ATTACHMENT_DISK = 'public';

    private const ATTACHMENT_PATH = 'incoming-goods';

    /**
     * Create a new service instance.
     *
     * @param IncomingGoodRepositoryInterface $incomingGoodRepository
     */
    public function __construct(private readonly IncomingGoodRepositoryInterface $incomingGoodRepository)
    {
    }

    /**
     * Paginate incoming goods records.
     *
     * @param int $perPage
     * @param array<string, mixed> $filters
     * @return LengthAwarePaginator<int, IncomingGood>
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->incomingGoodRepository->paginate($perPage, $filters);
    }

    /**
     * Find an incoming good record by its primary key ID.
     *
     * @param int $id
     * @return IncomingGood
     */
    public function find(int $id): IncomingGood
    {
        return $this->incomingGoodRepository->findById($id);
    }

    /**
     * Create a new incoming good record along with its items.
     *
     * @param array<string, mixed> $data
     * @return IncomingGood
     */
    public function create(array $data): IncomingGood
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        $items = $this->prepareItems($items);
        $data['total_price'] = $this->sumItemsTotal($items);

        if (isset($data['attachment']) && $data['attachment'] instanceof UploadedFile) {
            $data = array_merge($data, $this->storeAttachment($data['attachment']));
        }
        unset($data['attachment']);

        $data['created_by'] = Auth::id();

        return DB::transaction(function () use ($data, $items) {
            $incomingGood = $this->incomingGoodRepository->create($data);

            return $this->incomingGoodRepository->replaceItems($incomingGood, $items);
        });
    }

    /**
     * Update an existing incoming good record.
     *
     * @param IncomingGood $incomingGood
     * @param array<string, mixed> $data
     * @return IncomingGood
     */
    public function update(IncomingGood $incomingGood, array $data): IncomingGood
    {
        $items = $data['items'] ?? null;
        unset($data['items']);

        if ($items !== null) {
            $items = $this->prepareItems($items);
            $data['total_price'] = $this->sumItemsTotal($items);
        }

        if (!empty($data['remove_attachment'])) {
            $this->deleteAttachment($incomingGood);
            $data['attachment_path'] = null;
            $data['attachment_name'] = null;
        } elseif (isset($data['attachment']) && $data['attachment'] instanceof UploadedFile) {
            $this->deleteAttachment($incomingGood);
            $data = array_merge($data, $this->storeAttachment($data['attachment']));
        }

        unset($data['attachment'], $data['remove_attachment']);

        $data['updated_by'] = Auth::id();

        return DB::transaction(function () use ($incomingGood, $data, $items) {
            $incomingGood = $this->incomingGoodRepository->update($incomingGood, $data);

            if ($items !== null) {
                $incomingGood = $this->incomingGoodRepository->replaceItems($incomingGood, $items);
            }

            return $incomingGood;
        });
    }

    /**
     * Mark an incoming good record as verified.
     *
     * @param IncomingGood $incomingGood
     * @return IncomingGood
     */
    public function verify(IncomingGood $incomingGood): IncomingGood
    {
        return $this->incomingGoodRepository->verify($incomingGood, Auth::id());
    }

    /**
     * Delete an incoming good record along with its attachment.
     *
     * @param IncomingGood $incomingGood
     * @return bool
     */
    public function delete(IncomingGood $incomingGood): bool
    {
        $this->deleteAttachment($incomingGood);

        return $this->incomingGoodRepository->delete($incomingGood);
    }

    /**
     * Calculate the total for each item row and append created_by.
     *
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    private function prepareItems(array $items): array
    {
        return array_map(function (array $item) {
            $item['total'] = round(((float) $item['price']) * ((float) $item['quantity']), 2);
            $item['created_by'] = Auth::id();

            return $item;
        }, $items);
    }

    /**
     * Sum the total of all prepared items.
     *
     * @param array<int, array<string, mixed>> $items
     * @return float
     */
    private function sumItemsTotal(array $items): float
    {
        return round(array_sum(array_column($items, 'total')), 2);
    }

    /**
     * Store an uploaded attachment file and return its path/name.
     *
     * @param UploadedFile $file
     * @return array{attachment_path: string, attachment_name: string}
     */
    private function storeAttachment(UploadedFile $file): array
    {
        $path = $file->store(self::ATTACHMENT_PATH, self::ATTACHMENT_DISK);

        return [
            'attachment_path' => $path,
            'attachment_name' => $file->getClientOriginalName(),
        ];
    }

    /**
     * Delete the attachment file of an incoming good record, if any.
     *
     * @param IncomingGood $incomingGood
     * @return void
     */
    private function deleteAttachment(IncomingGood $incomingGood): void
    {
        if ($incomingGood->getAttribute('attachment_path')) {
            Storage::disk(self::ATTACHMENT_DISK)->delete($incomingGood->getAttribute('attachment_path'));
        }
    }
}
