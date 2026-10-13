<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class RoleSeeder
 *
 * Seeds application modules from a CSV file using Laravel Excel.
 *
 * @package Database\Seeders
 */
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
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
                    $data = array_map(fn($value) => $value === 'NULL' ? null : $value, $row->toArray());

                    return Role::unguarded(fn() => Role::updateOrCreate(
                        ['id' => $data['id'] ?? null],
                        $data
                    ));
                })
                ->count();

            DB::commit();

            echo "Successfully imported {$count} Role records\n";
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
            /**
             * Handle the collection of rows from the Excel/CSV file.
             *
             * @param Collection $collection
             * @return void
             */
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
        $relativePath = 'database/seeders/files/roles.csv';

        return base_path($relativePath);
    }
}
