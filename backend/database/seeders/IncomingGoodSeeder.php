<?php

namespace Database\Seeders;

use App\Models\IncomingGood;
use App\Models\IncomingGoodItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class IncomingGoodSeeder
 *
 * Seeds incoming goods and incoming goods items from CSV files.
 *
 * @package Database\Seeders
 */
class IncomingGoodSeeder extends Seeder
{
    private const ALLOWED_INCOMING_GOOD_COLUMNS = [
        'id',
        'operator_id',
        'sub_category_id',
        'received_date',
        'origin',
        'letter_number',
        'total_price',
        'is_verified',
        'verified_at',
        'verified_by',
    ];

    private const ALLOWED_ITEM_COLUMNS = [
        'id',
        'incoming_good_id',
        'name',
        'price',
        'quantity',
        'unit',
        'total',
        'expired_date',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $goodsPath = $this->getGoodsFilePath();
        $itemsPath = $this->getItemsFilePath();

        if (!file_exists($goodsPath)) {
            echo "Error: File not found at {$goodsPath}\n";
            return;
        }

        if (!file_exists($itemsPath)) {
            echo "Error: File not found at {$itemsPath}\n";
            return;
        }

        try {
            DB::beginTransaction();

            // --- Seed incoming_goods ---
            $goodsCount = Excel::toCollection($this->import(), $goodsPath)
                ->first()
                ->filter(fn($row) => !empty(array_filter($row->toArray())))
                ->map(function ($row) {
                    $data = array_map(
                        fn($value) => $value === 'NULL' ? null : $value,
                        $row->toArray()
                    );

                    $data = array_intersect_key($data, array_flip(self::ALLOWED_INCOMING_GOOD_COLUMNS));

                    // Cast boolean
                    $data['is_verified'] = filter_var($data['is_verified'] ?? false, FILTER_VALIDATE_BOOLEAN);

                    return IncomingGood::unguarded(fn() => IncomingGood::updateOrCreate(
                        ['id' => $data['id'] ?? null],
                        $data
                    ));
                })
                ->count();

            // --- Seed incoming_goods_items ---
            $itemsCount = Excel::toCollection($this->import(), $itemsPath)
                ->first()
                ->filter(fn($row) => !empty(array_filter($row->toArray())))
                ->map(function ($row) {
                    $data = array_map(
                        fn($value) => $value === 'NULL' ? null : $value,
                        $row->toArray()
                    );

                    $data = array_intersect_key($data, array_flip(self::ALLOWED_ITEM_COLUMNS));

                    return IncomingGoodItem::unguarded(fn() => IncomingGoodItem::updateOrCreate(
                        ['id' => $data['id'] ?? null],
                        $data
                    ));
                })
                ->count();

            DB::commit();

            echo "Successfully imported {$goodsCount} Incoming Good records\n";
            echo "Successfully imported {$itemsCount} Incoming Good Item records\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "Error importing " . $e->getMessage() . "\n";
        }
    }

    /**
     * Create an anonymous import class instance for Laravel Excel.
     *
     * @return ToCollection&WithHeadingRow
     */
    private function import(): ToCollection&WithHeadingRow
    {
        return new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $collection): void
            {
            }
        };
    }

    /**
     * Get the absolute file path for incoming_goods CSV.
     *
     * @return string
     */
    public static function getGoodsFilePath(): string
    {
        return base_path('database/seeders/files/incoming_goods.csv');
    }

    /**
     * Get the absolute file path for incoming_goods_items CSV.
     *
     * @return string
     */
    public static function getItemsFilePath(): string
    {
        return base_path('database/seeders/files/incoming_goods_items.csv');
    }
}
