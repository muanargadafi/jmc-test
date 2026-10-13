<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\RoleRepositoryInterface::class,
            \App\Repositories\Eloquents\RoleRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\Eloquents\UserRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\CategoryRepositoryInterface::class,
            \App\Repositories\Eloquents\CategoryRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\SubCategoryRepositoryInterface::class,
            \App\Repositories\Eloquents\SubCategoryRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
