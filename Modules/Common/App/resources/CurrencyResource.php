<?php

namespace Modules\Common\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Country\App\resources\CountryResource;

class CurrencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'country' => $this->whenLoaded('country', function() {
                return new CountryResource($this->country);
            }),
        ];
    }
}
