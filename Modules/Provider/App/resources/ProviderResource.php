<?php

namespace Modules\Provider\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProviderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "email" => $this->email ?? null,
            "phone" => $this->phone,
            "image" => $this->image ?? null,
            "lat" => $this->lat ?? null,
            "long" => $this->long ?? null,
            "role" => "provider",
            "is_active" => $this->is_active,
            "created_at" => $this->created_at->format('Y-m-d'),
            "updated_at" => $this->updated_at->format('Y-m-d'),
        ];
    }
}
