<?php

namespace Database\Seeders;

use App\Models\SubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class SubCategorySeeder
 *
 * Seeds application sub categories from a CSV file using Laravel Excel.
 *
 * @package Database\Seeders
 */
class SubCategorySeeder extends Seeder
{
    private const ALLOWED_COLUMNS = ['id', 'category_id', 'name', 'price_limit'];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = $this->getSeederFilePath();

        if (!file_exists($filePath)) {
            echo "Error: File not found at {$filePath}\n";
            return;
        }

        try {
            DB::beginTransaction();

            $count = Excel::toCollection($this->import(), $filePath)
                ->first()
                ->filter(fn($row) => !empty(array_filter($row->toArray())))
                ->map(function ($row) {
                    $data = array_map(
                        fn($value) => $value === 'NULL' ? null : $value,
                        $row->toArray()
                    );

                    $data = array_intersect_key($data, array_flip(self::ALLOWED_COLUMNS));

                    return SubCategory::unguarded(fn() => SubCategory::updateOrCreate(
                        ['id' => $data['id'] ?? null],
                        $data
                    ));
                })
                ->count();

            DB::commit();

            echo "Successfully imported {$count} Sub Category records\n";
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
     * Get the absolute seeder file path.
     *
     * @return string
     */
    public static function getSeederFilePath(): string
    {
        return base_path('database/seeders/files/sub_categories.csv');
    }
}
