<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @extends \Illuminate\Http\Resources\Json\JsonResource<\App\Models\User>
 * @property \App\Models\User $resource
 */
class UserResource extends JsonResource
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
            'role' => RoleResource::make($this->whenLoaded('role')),
            'name' => $this->resource->getAttribute('name'),
            'username' => $this->resource->getAttribute('username'),
            'email' => $this->resource->getAttribute('email'),
            'is_locked' => $this->resource->getAttribute('is_locked'),
            'created_at' => $this->resource->getAttribute('created_at')?->toIso8601String(),
            'updated_at' => $this->resource->getAttribute('updated_at')?->toIso8601String(),
        ];
    }
}
