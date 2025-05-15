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
        $data =
            [
                "id" => $this->id,
                "first_name" => $this->first_name ?? null,
                "last_name" => $this->last_name ?? null,
                "email" => $this->email ?? null,
                "phone" => $this->phone,
                "whatsapp" => $this->whatsapp ?? null,
                "image" => $this->image ?? null,
                "type" => $this->type ?? null,
                "is_active" => $this->is_active,
            ];
            if (isset($this->is_contacted)) {
                $data['is_contacted'] = $this->is_contacted;
            }
            if (isset($this->contacts_count)) {
                $data['contacts_count'] = $this->contacts_count;
            }
            $data['created_at'] = $this->created_at->format('Y-m-d h:i A');
            $data['updated_at'] = $this->updated_at->format('Y-m-d h:i A');
            $data['profile'] = $this->whenLoaded('providerProfile');
            $data['working_times'] = $this->whenLoaded('providerWorkingTimes');
            $data['certificates'] = $this->whenLoaded('providerCertificates');

        return $data;
    }
}
