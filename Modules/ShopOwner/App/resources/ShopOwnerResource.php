<?php

namespace Modules\ShopOwner\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\ShopOwner\App\resources\ShopOwnerProfileResource;

class ShopOwnerResource extends JsonResource
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
        $data['created_at'] = $this->created_at->format('Y-m-d h:i A');
        $data['updated_at'] = $this->updated_at->format('Y-m-d h:i A');
        $data['profile'] = $this->whenLoaded('shopOwnerProfile', function ($profile) {
            return new ShopOwnerProfileResource($profile);
        });
        $data['working_times'] = $this->whenLoaded('shopOwnerWorkingTimes');
        $data['shop_images'] = $this->whenLoaded('shopOwnerShopImages');
        $data['rates'] = $this->whenLoaded('rates');
        $data['comments'] = $this->whenLoaded('comments');

        return $data;
    }
}
