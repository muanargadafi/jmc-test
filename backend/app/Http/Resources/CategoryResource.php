<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'code' => $this->resource->getAttribute('code'),
            'name' => $this->resource->getAttribute('name'),
            'created_at' => $this->when(array_key_exists('created_at', $this->getAttributes()), fn() => $this->resource->getAttribute('created_at')?->toIso8601String()),
            'updated_at' => $this->when(array_key_exists('updated_at', $this->getAttributes()), fn() => $this->resource->getAttribute('updated_at')?->toIso8601String()),
        ];
    }
}
