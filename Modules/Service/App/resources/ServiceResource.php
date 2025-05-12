<?php

namespace Modules\Service\App\resources;

use Modules\User\App\resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'image' => $this->image ?? null,
            'user_id' => $this->user_id,
            'sub_category_id' => $this->sub_category_id,
            'start_date' => $this->start_date ?? null,
            'end_date' => $this->end_date ?? null,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->format('Y-m-d h:i A'),
            'updated_at' => $this->updated_at->format('Y-m-d h:i A'),
            'sub_category' => $this->whenLoaded('subCategory'),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
