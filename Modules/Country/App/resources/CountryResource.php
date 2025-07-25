<?php

namespace Modules\Country\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $locale = app()->getLocale();
        return [
            'id' => $this->id,
            'title' => $locale == 'en' ? ($this->title_en ?? null) : ($this->title_ar ?? null),
            'currency' => $locale == 'en' ? ($this->currency_en ?? null) : ($this->currency_ar ?? null),
            'image' => $this->image ?? null,
            'country_code' => $this->country_code ?? null,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
