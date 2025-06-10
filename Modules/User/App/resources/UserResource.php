<?php

namespace Modules\User\App\resources;

use Modules\Common\App\Models\Setting;
use Modules\Country\App\Models\Country;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Provider\App\resources\ProviderProfileResource;
use Modules\ShopOwner\App\resources\ShopOwnerProfileResource;

class UserResource extends JsonResource
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
            if ($this->providerProfile->status === 'free_trial') {
                $data['free_trial_remaining_times'] = Setting::where('key', 'free_trial_contacts_count')->first()->value - $this->providerContacts->count();
            }
            if ($this->country != null) {
                $country = Country::select('currency')->where('title', $this->country)->first();
                if ($country) {
                    $data['currency'] = $country->currency;
                } else {
                    $data['currency'] = null;
                }
            }
            $data['profile'] = $this->whenLoaded('providerProfile', function ($profile) {
                return new ProviderProfileResource($profile);
            });
            $data['working_times'] = $this->whenLoaded('providerWorkingTimes');
            $data['certificates'] = $this->whenLoaded('providerCertificates');
            $data['package'] = $this->whenLoaded('package');
        } elseif ($this->type == 'shop_owner') {
            if ($this->shopOwnerProfile->status === 'free_trial') {
                $data['free_trial_remaining_times'] = Setting::where('key', 'free_trial_contacts_count')->first()->value - $this->shopOwnerContacts->count();
            }
            $data['profile'] = $this->whenLoaded('shopOwnerProfile', function () {
                return new ShopOwnerProfileResource($this->shopOwnerProfile);
            });
            $data['working_times'] = $this->whenLoaded('shopOwnerWorkingTimes');
            $data['shop_images'] = $this->whenLoaded('shopOwnerShopImages');
            $data['package'] = $this->whenLoaded('package');
        }
        return $data;
    }
}
