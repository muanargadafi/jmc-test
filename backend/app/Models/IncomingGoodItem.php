<?php

namespace App\Models;

use App\Traits\UserStampsTrait;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['incoming_good_id', 'name', 'price', 'quantity', 'unit', 'total', 'expired_date', 'created_by', 'updated_by'])]
class IncomingGoodItem extends Model
{
    use HasFactory, UserStampsTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'incoming_goods_items';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'quantity' => 'decimal:2',
            'total' => 'decimal:2',
            'expired_date' => 'date',
        ];
    }

    /**
     * Get the incoming goods record this item belongs to.
     *
     * @return BelongsTo<IncomingGood, $this>
     */
    public function incomingGood(): BelongsTo
    {
        return $this->belongsTo(IncomingGood::class);
    }
}
