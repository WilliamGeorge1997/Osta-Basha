<?php

namespace Modules\Common\App\resources;

use Modules\User\App\resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id ?? null,
            'image_ar' => $this->image_ar ?? null,
            'image_en' => $this->image_en ?? null,
            'is_active' => $this->is_active ?? null,
            'user_id' => $this->user_id ?? null,
            'created_at' => $this->created_at->format('Y-m-d h:i A') ?? null,
            'updated_at' => $this->updated_at->format('Y-m-d h:i A') ?? null,
            'user' => $this->whenLoaded('user', function () {
                $user = new UserResource($this->user);
                if ($this->user->type == 'service_provider') {
                    $user->load('providerProfile.subCategory.category');
                    $user->load('providerWorkingTimes');
                    $user->load('providerCertificates');
                } elseif ($this->user->type == 'shop_owner') {
                    $user->load('shopOwnerProfile.subCategory.category');
                    $user->load('shopOwnerWorkingTimes');
                    $user->load('shopOwnerShopImages');
                }
                return $user;
            }),
        ];
    }
}
