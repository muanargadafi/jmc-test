<?php

namespace App\Models;

use App\Traits\UserStampsTrait;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['category_id', 'name', 'price_limit', 'created_by', 'updated_by'])]
class SubCategory extends Model
{
    use HasFactory, UserStampsTrait;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_limit' => 'decimal:2',
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            if ($model->incomingGoods()->exists()) {
                throw new \Exception(__("Incoming Goods already use, can't be deleted."));
            }
        });
    }

    /**
     * Get the category that owns the sub-category.
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the incoming goods for the sub category.
     *
     * @return HasMany<IncomingGood, $this>
     */
    public function incomingGoods(): HasMany
    {
        return $this->hasMany(IncomingGood::class);
    }
}
