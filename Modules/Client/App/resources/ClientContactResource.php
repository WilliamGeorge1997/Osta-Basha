<?php

namespace Modules\Client\App\resources;

use Modules\User\App\Models\User;
use Modules\User\App\resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            "client_id" => $this->client_id,
            'contactable_type' => $this->contactable_type  == 'Modules\Provider\App\Models\Provider' ? User::TYPE_SERVICE_PROVIDER : User::TYPE_SHOP_OWNER,
            "contactable_id" => $this->contactable_id,
            "created_at" => $this->created_at->format('Y-m-d H:i:s'),
            "updated_at" => $this->updated_at->format('Y-m-d H:i:s'),
            'user' => $this->whenLoaded('user', function () {
                $user = new UserResource($this->user);
                if ($this->user->type == 'service_provider') {
                    $user->load('providerProfile');
                } elseif ($this->user->type == 'shop_owner') {
                    $user->load('shopOwnerProfile');
                }
                return $user;
            }),
        ];
    }
}
