<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class IncomingGoodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $attachmentPath = $this->resource->getAttribute('attachment_path');

        return [
            'id' => $this->resource->getAttribute('id'),
            'operator' => UserResource::make($this->whenLoaded('operator')),
            'sub_category' => SubCategoryResource::make($this->whenLoaded('subCategory')),
            'received_date' => $this->resource->getAttribute('received_date')?->toDateString(),
            'origin' => $this->resource->getAttribute('origin'),
            'letter_number' => $this->resource->getAttribute('letter_number'),
            'attachment_name' => $this->resource->getAttribute('attachment_name'),
            'attachment_url' => $attachmentPath ? Storage::url($attachmentPath) : null,
            'total_price' => $this->resource->getAttribute('total_price'),
            'is_verified' => $this->resource->getAttribute('is_verified'),
            'verified_at' => $this->resource->getAttribute('verified_at')?->toIso8601String(),
            'verifier' => UserResource::make($this->whenLoaded('verifier')),
            'items' => IncomingGoodItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->resource->getAttribute('created_at')?->toIso8601String(),
            'updated_at' => $this->resource->getAttribute('updated_at')?->toIso8601String(),
        ];
    }
}
