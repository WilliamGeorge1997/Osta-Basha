<?php

namespace Modules\Provider\App\resources;

use Modules\Country\App\Models\Country;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Provider\App\resources\ProviderProfileResource;

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
        if ($request->has('country') && $request->country != null) {
            $country = Country::select('currency')->where('title', $request->country)->first();
            if ($country) {
                $data['currency'] = $country->currency;
            } else {
                $data['currency'] = null;
            }
        }
        $data['created_at'] = $this->created_at->format('Y-m-d h:i A');
        $data['updated_at'] = $this->updated_at->format('Y-m-d h:i A');
        $data['profile'] = $this->whenLoaded('providerProfile', function ($profile) {
            return new ProviderProfileResource($profile);
        });
        $data['working_times'] = $this->whenLoaded('providerWorkingTimes');
        $data['certificates'] = $this->whenLoaded('providerCertificates');
        $data['rates'] = $this->whenLoaded('rates');
        $data['comments'] = $this->whenLoaded('comments');
        return $data;
    }
}
