<?php

namespace Modules\Client\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "first_name" => $this->first_name ?? null,
            "last_name" => $this->last_name ?? null,
            "email" => $this->email ?? null,
            "phone" => $this->phone,
            "image" => $this->image ?? null,
            "is_active" => $this->is_active,
            "created_at" => $this->created_at->format('Y-m-d'),
            "updated_at" => $this->updated_at->format('Y-m-d'),
        ];
    }
}
