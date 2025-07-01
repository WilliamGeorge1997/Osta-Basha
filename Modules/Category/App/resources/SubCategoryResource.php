<?php

namespace Modules\Category\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
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
            'category_id' => $this->category_id,
            'image' => $this->image ?? null,
            'is_active' => $this->is_active,
            "created_at" => $this->created_at->format('Y-m-d h:i A'),
            "updated_at" => $this->updated_at->format('Y-m-d h:i A'),
            'category' => $this->whenLoaded('category'),
            'localizations' => $this->whenLoaded('localizations'),
        ];
    }
}
