<?php

namespace Modules\Provider\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProviderProfileResource extends JsonResource
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
                "card_number" => $this->card_number ?? null,
                "card_image" => $this->card_image ?? null,
                "address" => $this->address ?? null,
                "experience_years" => $this->experience_years ?? null,
                "experience_description" => $this->experience_description ?? null,
                "price" => $this->price ?? null,
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
