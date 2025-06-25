<?php

namespace Modules\ShopOwner\App\resources;

use Modules\Common\App\Models\Setting;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Common\Helpers\ArabicNumeralsConverterTrait;
use Modules\ShopOwner\App\resources\ShopOwnerProfileResource;
use Modules\ShopOwner\App\resources\ShopOwnerWorkingTimeResource;

class ShopOwnerResource extends JsonResource
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
                "rates_avg" => $this->rates_avg ?? 0,
                "comments_count" => $this->comments_count ?? null,
                "is_active" => $this->is_active,
                "is_available" => $this->is_available,
            ];
        if (isset($this->is_contacted)) {
            $data['is_contacted'] = $this->is_contacted;
        }
        $data['created_at'] = $this->created_at->format('Y-m-d h:i A');
        $data['updated_at'] = $this->updated_at->format('Y-m-d h:i A');
        if ($this->shopOwnerProfile && $this->shopOwnerProfile->status === 'free_trial') {
            $data['free_trial_remaining_times'] = Setting::where('key', 'free_trial_contacts_count')->first()->value - $this->shopOwnerContacts->count();
        }
        $data['profile'] = $this->whenLoaded('shopOwnerProfile', function ($profile) {
            return new ShopOwnerProfileResource($profile);
        });
        $data['working_times'] = $this->whenLoaded('shopOwnerWorkingTimes', function ($workingTimes) {
            return ShopOwnerWorkingTimeResource::collection($workingTimes);
        });
        $data['shop_images'] = $this->whenLoaded('shopOwnerShopImages');
        $data['shop_owner_contacts'] = $this->whenLoaded('shopOwnerContacts');

        return $this->convertNumericToArabic($data, [
             'whatsapp', 'whatsapp_country_code', 'free_trial_remaining_times',
        ]);
    }
}
