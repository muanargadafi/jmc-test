<?php

namespace App\Models;

use App\Traits\UserStampsTrait;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['operator_id', 'sub_category_id', 'received_date', 'origin', 'letter_number', 'attachment_path', 'attachment_name', 'total_price', 'created_by', 'updated_by'])]
class IncomingGood extends Model
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
            'received_date' => 'date',
            'total_price' => 'decimal:2',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the operator (user) who handled this record.
     *
     * @return BelongsTo<User, $this>
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    /**
     * Get the sub category of this record (category is reached through it).
     *
     * @return BelongsTo<SubCategory, $this>
     */
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    /**
     * Get the item rows of this record.
     *
     * @return HasMany<IncomingGoodItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(IncomingGoodItem::class);
    }

    /**
     * Get the user who verified this record.
     *
     * @return BelongsTo<User, $this>
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Determine if the record has been verified.
     *
     * @return bool
     */
    public function isVerified(): bool
    {
        return (bool) $this->is_verified;
    }
}
