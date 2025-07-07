<?php

namespace Modules\User\App\resources;

use Modules\Common\App\Models\Setting;
use Modules\Country\App\Models\Country;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Common\Helpers\ArabicNumeralsConverterTrait;
use Modules\Provider\App\resources\ProviderProfileResource;
use Modules\ShopOwner\App\resources\ShopOwnerProfileResource;
use Modules\Provider\App\resources\ProviderWorkingTimeResource;
use Modules\ShopOwner\App\resources\ShopOwnerWorkingTimeResource;

class UserResource extends JsonResource
{
    // use ArabicNumeralsConverterTrait;

    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
         $locale = app()->getLocale();
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
            if ($this->providerProfile && $this->providerProfile->status === 'free_trial') {
                $data['free_trial_remaining_times'] = Setting::where('key', 'free_trial_contacts_count')->first()->value - $this->providerContacts->count();
            }
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
            $data['profile'] = $this->whenLoaded('providerProfile', function ($profile) {
                return new ProviderProfileResource($profile);
            });
            $data['working_times'] = $this->whenLoaded('providerWorkingTimes', function ($workingTimes) {
                return ProviderWorkingTimeResource::collection($workingTimes);
            });
            $data['certificates'] = $this->whenLoaded('providerCertificates');
            $data['package'] = $this->whenLoaded('package');
        } elseif ($this->type == 'shop_owner') {
            if ($this->shopOwnerProfile && $this->shopOwnerProfile->status === 'free_trial') {
                $data['free_trial_remaining_times'] = Setting::where('key', 'free_trial_contacts_count')->first()->value - $this->shopOwnerContacts->count();
            }
            $data['profile'] = $this->whenLoaded('shopOwnerProfile', function () {
                return new ShopOwnerProfileResource($this->shopOwnerProfile);
            });
            $data['working_times'] = $this->whenLoaded('shopOwnerWorkingTimes', function ($workingTimes) {
                return ShopOwnerWorkingTimeResource::collection($workingTimes);
            });
            $data['shop_images'] = $this->whenLoaded('shopOwnerShopImages');
            $data['package'] = $this->whenLoaded('package');
        }
        return $data;
        // return $this->convertNumericToArabic($data, [
        //     'whatsapp', 'whatsapp_country_code', 'free_trial_remaining_times',
        //     'working_times'
        // ]);
    }
}
