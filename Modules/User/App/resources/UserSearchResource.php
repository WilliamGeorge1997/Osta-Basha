<?php

namespace Modules\User\App\resources;

use Modules\Country\App\Models\Country;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Provider\App\resources\ProviderProfileResource;
use Modules\ShopOwner\App\resources\ShopOwnerProfileResource;

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
                "country_code" => $this->country_code ?? null,
                "phone" => $this->phone,
                "whatsapp_country_code" => $this->whatsapp_country_code ?? null,
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
            if ($this->country != null) {
                $country = Country::select('currency')->where('title', $this->country)->first();
                if ($country) {
                    $data['currency'] = $country->currency;
                } else {
                    $data['currency'] = null;
                }
            }
            $data['profile'] = $this->whenLoaded('providerProfile', function () {
                return new ProviderProfileResource($this->providerProfile);
            });
            $data['working_times'] = $this->providerWorkingTimes;
            $data['certificates'] = $this->providerCertificates;
            $data['package'] = $this->package;
        } elseif ($this->type == 'shop_owner') {
            $data['profile'] = $this->whenLoaded('shopOwnerProfile', function () {
                return new ShopOwnerProfileResource($this->shopOwnerProfile);
            });
            $data['working_times'] = $this->shopOwnerWorkingTimes;
            $data['shop_images'] = $this->shopOwnerShopImages;

        }
        return $data;
    }
}
