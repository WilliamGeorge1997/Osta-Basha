<?php

namespace Modules\ShopOwner\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopOwnerProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $data =
            [
                "id" => $this->id ?? null,
                "user_id" => $this->user_id ?? null,
                "sub_category_id" => $this->sub_category_id ?? null,
                "shop_name" => $this->shop_name ?? null,
                "products_description" => $this->products_description ?? null,
                "experience_years" => $this->experience_years ?? null,
                "address" => $this->address ?? null,
                "start_date" => $this->start_date ?? null,
                "end_date" => $this->end_date ?? null,
                "status" => $this->status ?? null,
                "is_active" => $this->is_active ?? null,
                "created_at" => $this->created_at->format('Y-m-d h:i A'),
                "updated_at" => $this->updated_at->format('Y-m-d h:i A'),
                "sub_category" => $this->whenLoaded('subCategory')
            ];
        return $data;
    }
}
