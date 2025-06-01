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
            "country_code" => $this->country_code ?? null,
            "phone" => $this->phone,
            'whatsapp' => $this->whatsapp ?? null,
            "image" => $this->image ?? null,
            "type" => $this->type ?? null,
            "lat" => $this->lat ?? null,
            "long" => $this->long ?? null,
            "city" => $this->city ?? null,
            "country" => $this->country ?? null,
            "is_active" => $this->is_active,
            "created_at" => $this->created_at->format('Y-m-d H:i:s'),
            "updated_at" => $this->updated_at->format('Y-m-d H:i:s'),
            'client_contacts' => $this->whenLoaded('clientContacts', function () {
                return ClientContactResource::collection($this->clientContacts);
            }),
        ];
    }
}
