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
        return
            [
                "id" => $this->id,
                "first_name" => $this->first_name ?? null,
                "last_name" => $this->last_name ?? null,
                "email" => $this->email ?? null,
                "phone" => $this->phone,
                "image" => $this->image ?? null,
                "type" => $this->type ?? null,
                "is_active" => $this->is_active,
                "created_at" => $this->created_at->format('Y-m-d h:i A'),
                "updated_at" => $this->updated_at->format('Y-m-d h:i A'),
                "profile" => $this->whenLoaded('providerProfile'),
                "working_times" => $this->whenLoaded('providerWorkingTimes'),
                "certificates" => $this->whenLoaded('providerCertificates'),
            ];
    }
}
