<?php

namespace Modules\Country\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Common\App\resources\CurrencyResource;

class CountryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'currency' => $this->whenLoaded('currency', function() {
                return new CurrencyResource($this->currency);
            }),
        ];
    }
}
