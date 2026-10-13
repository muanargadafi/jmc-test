<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Trait UserStampsTrait
 *
 * Automatically manages audit fields (`created_by` and `updated_by`) for Eloquent models
 * based on the currently authenticated user.
 *
 * @package App\Traits
 *
 * @method static void creating(callable $callback)
 * @method static void updating(callable $callback)
 */
trait UserStampsTrait
{
    /**
     * Boot the trait to register model event listeners for creating and updating.
     *
     * @return void
     */
    public static function bootUserStampsTrait(): void
    {
        static::creating(function ($model) {
            if (empty($model->created_by) && Auth::check()) {
                $model->created_by = Auth::id();
            }

            if (empty($model->updated_by) && Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    /**
     * Get the user who created the record.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the record.
     *
     * @return BelongsTo<User, $this>
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
