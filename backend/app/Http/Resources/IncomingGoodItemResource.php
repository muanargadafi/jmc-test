<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncomingGoodItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getAttribute('id'),
            'name' => $this->resource->getAttribute('name'),
            'price' => $this->resource->getAttribute('price'),
            'quantity' => $this->resource->getAttribute('quantity'),
            'unit' => $this->resource->getAttribute('unit'),
            'total' => $this->resource->getAttribute('total'),
            'expired_date' => $this->resource->getAttribute('expired_date')?->toDateString(),
            'created_at' => $this->resource->getAttribute('created_at')?->toIso8601String(),
            'updated_at' => $this->resource->getAttribute('updated_at')?->toIso8601String(),
        ];
    }
}
