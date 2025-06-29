<?php

namespace Modules\Category\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryLocalizationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title ?? null,
            'country' => $this->whenLoaded('country'),
            "created_at" => $this->created_at?->format('Y-m-d h:i A') ?? null,
            "updated_at" => $this->updated_at?->format('Y-m-d h:i A') ?? null,
        ];
    }
}
