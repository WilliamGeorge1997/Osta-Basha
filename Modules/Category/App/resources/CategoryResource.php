<?php

namespace Modules\Category\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title_ar' => $this->title_ar ?? null,
            'title_en' => $this->title_en ?? null,
            'description_ar' => $this->description_ar ?? null,
            'description_en' => $this->description_en ?? null,
            'image' => $this->image ?? null,
            'is_active' => $this->is_active,
            "created_at" => $this->created_at?->format('Y-m-d h:i A') ?? null,
            "updated_at" => $this->updated_at?->format('Y-m-d h:i A') ?? null,
            'sub_categories' => $this->whenLoaded('subCategories'),
            'localizations' => $this->whenLoaded('localizations'),
        ];
    }
}
