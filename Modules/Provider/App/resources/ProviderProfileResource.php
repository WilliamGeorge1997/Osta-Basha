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
                "id" => $this->id,
                "user_id" => $this->user_id,
                "sub_category_id" => $this->sub_category_id,
                "card_number" => $this->card_number,
                "card_image" => $this->card_image,
                "address" => $this->address,
                "experience_years" => $this->experience_years,
                "experience_description" => $this->experience_description,
                "price" => $this->price,
                "currency" => $this->currency->title,
                "start_date" => $this->start_date,
                "end_date" => $this->end_date,
                "status" => $this->status,
                "is_active" => $this->is_active,
                "created_at" => $this->created_at->format('Y-m-d h:i A'),
                "updated_at" => $this->updated_at->format('Y-m-d h:i A')
            ];
        return $data;
    }
}
