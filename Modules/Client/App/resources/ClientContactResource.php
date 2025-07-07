<?php

namespace Modules\Client\App\resources;

use Modules\User\App\Models\User;
use Modules\Country\App\Models\Country;
use Modules\User\App\resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "client_id" => $this->client_id,
            'contactable_type' => $this->contactable_type == 'Modules\Provider\App\Models\Provider' ? User::TYPE_SERVICE_PROVIDER : User::TYPE_SHOP_OWNER,
            "contactable_id" => $this->contactable_id,
            'rate' => $this->rate ?? null,
            'comment' => $this->comment ?? null,
            "created_at" => $this->created_at->format('Y-m-d H:i:s'),
            "updated_at" => $this->updated_at->format('Y-m-d H:i:s'),
            'user' => $this->whenLoaded('user', function () {
                $user = new UserResource($this->user);
                if ($this->user->type == 'service_provider') {
                    if ($this->country != null) {
                        $country = Country::select(['currency_ar','currency_en'])->where('title_ar', $this->country)->orWhere('title_en', $this->country)->first();
                        if ($country) {
                            $data['currency'] = $locale === 'en'
                                    ? ($country->currency_en ?? null)
                                    : ($country->currency_ar ?? null);
                        } else {
                            $data['currency'] = null;
                        }
                    }
                    $user->load('providerProfile.subCategory.category', 'providerWorkingTimes', 'providerCertificates', 'providerProfile.package', 'providerContacts.client');
                } elseif ($this->user->type == 'shop_owner') {
                    $user->load('shopOwnerProfile.subCategory.category', 'shopOwnerWorkingTimes', 'shopOwnerShopImages', 'shopOwnerProfile.package', 'shopOwnerContacts.client');
                }
                return $user;
            }),
        ];
    }
}
