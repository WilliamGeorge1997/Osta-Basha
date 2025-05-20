<?php

namespace Modules\User\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Provider\App\resources\ProviderProfileResource;

class UserSearchResource extends JsonResource
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
                "lat" => $this->lat ?? null,
                "long" => $this->long ?? null,
                "city" => $this->city ?? null,
                "country" => $this->country ?? null,
                "is_active" => $this->is_active,
                "is_available" => $this->is_available,
                "created_at" => $this->created_at->format('Y-m-d h:i A'),
                "updated_at" => $this->updated_at->format('Y-m-d h:i A'),
            ];
        if ($this->type == 'service_provider') {
            $data['profile'] = $this->whenLoaded('providerProfile', function ($profile) {
                return new ProviderProfileResource($profile);
            });
            $data['working_times'] = $this->providerWorkingTimes;
            $data['certificates'] = $this->providerCertificates;
        } elseif ($this->type == 'shop_owner') {
            $data['profile'] = $this->shopOwnerProfile;
            $data['working_times'] = $this->shopOwnerWorkingTimes;
            $data['shop_images'] = $this->shopOwnerShopImages;
        }
        return $data;
    }
}
