<?php

namespace App\Models;

use App\Traits\UserStampsTrait;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'name', 'created_by', 'updated_by'])]
class Category extends Model
{
    use HasFactory, UserStampsTrait;

    /**
     * Get the sub-categories for the category.
     *
     * @return HasMany<SubCategory, $this>
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(SubCategory::class);
    }
}
