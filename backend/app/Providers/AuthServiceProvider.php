<?php

namespace App\Providers;

use App\Enums\AbilityEnum;
use App\Enums\RoleNameEnum;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define(AbilityEnum::BARANG_MASUK_MANAGE->value, function (User $user) {
            return $user->hasRole(RoleNameEnum::ADMIN, RoleNameEnum::OPERATOR);
        });

        Gate::define(AbilityEnum::MASTER_DATA_MANAGE->value, function (User $user) {
            return $user->hasRole(RoleNameEnum::ADMIN);
        });

        Gate::define(AbilityEnum::USERS_MANAGE->value, function (User $user) {
            return $user->hasRole(RoleNameEnum::ADMIN);
        });
    }
}
