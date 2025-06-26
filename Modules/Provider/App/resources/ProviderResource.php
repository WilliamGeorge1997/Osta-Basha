<?php

namespace Modules\Provider\App\resources;

use Modules\Common\App\Models\Setting;
use Modules\Country\App\Models\Country;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Common\Helpers\ArabicNumeralsConverterTrait;
use Modules\Provider\App\resources\ProviderProfileResource;
use Modules\Provider\App\resources\ProviderWorkingTimeResource;

class ProviderResource extends JsonResource
{
    use ArabicNumeralsConverterTrait;

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
                "rates_count" => $this->rates_count ?? null,
                "rates_avg" => $this->rates_avg ?? null,
                "comments_count" => $this->comments_count ?? null,
                "is_active" => $this->is_active,
                "is_available" => $this->is_available,
            ];
        if (isset($this->is_contacted)) {
            $data['is_contacted'] = $this->is_contacted;
        }
        if (isset($this->contacts_count)) {
            $data['contacts_count'] = $this->contacts_count;
        }
        if ($this->country != null) {
            $country = Country::select('currency')->where('title', $this->country)->first();
            if ($country) {
                $data['currency'] = $country->currency;
            } else {
                $data['currency'] = null;
            }
        }
        $data['created_at'] = $this->created_at->format('Y-m-d h:i A');
        $data['updated_at'] = $this->updated_at->format('Y-m-d h:i A');
        if ($this->providerProfile && $this->providerProfile->status === 'free_trial') {
            $data['free_trial_remaining_times'] = Setting::where('key', 'free_trial_contacts_count')->first()->value - $this->providerContacts->count();
        }
        $data['profile'] = $this->whenLoaded('providerProfile', function ($profile) {
            return new ProviderProfileResource($profile);
        });
        $data['working_times'] = $this->whenLoaded('providerWorkingTimes', function ($workingTimes) {
            return ProviderWorkingTimeResource::collection($workingTimes);
        });
        $data['certificates'] = $this->whenLoaded('providerCertificates');
        $data['provider_contacts'] = $this->whenLoaded('providerContacts');
        return $this->convertNumericToArabic($data, [
            'whatsapp', 'whatsapp_country_code', 'free_trial_remaining_times',
        ]);
    }
}
