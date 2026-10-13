<?php

namespace Database\Seeders;

use App\Enums\RoleNameEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserSeeder
 *
 * Seeds application users and assigns roles and status.
 *
 * @package Database\Seeders
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $role = Role::where('code', RoleNameEnum::ADMIN)->first();

        $email = 'admin@gmail.com';

        $admin = User::query()->where('email', $email)->first();

        $admin = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'role_id' => $role?->getKey(),
                'name' => 'Super Admin',
                'username' => 'admin',
                'email_verified_at' => now(),
                'password' => Hash::make(env('ADMIN_DEFAULT_PASSWORD', 'Admin@12345')),
            ]
        );

        if (empty($admin->getAttribute('created_by'))) {
            $admin->update([
                'created_by' => $admin->getKey(),
                'updated_by' => $admin->getKey(),
            ]);
        }
    }
}
