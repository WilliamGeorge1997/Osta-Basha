<?php
namespace Modules\Client\App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'client_id' => $this->client_id,
            'city_id' => $this->city_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'block' => $this->block,
            'street' => $this->street,
            'house_number' => $this->house_number,
            'notes' => $this->notes,
            'default' => $this->default,
            'phone' => [
                'id' => $this->phone?->id,
                'number' => $this->phone?->phone,
            'is_verified' => $this->phone?->is_verified,
            ]
        ];
    }
}
