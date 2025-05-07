<?php

namespace Modules\User\Validation;

use Illuminate\Support\Facades\Validator;

use Modules\User\Entities\User;

trait UserValidation
{
    public function validateUserRegister($data)
    {
        return Validator::make($data, [
            'type' => ['required', 'in:client,service_provider,shop_owner'],
            'title' => ['required_if:type,service_provider,shop_owner', 'string', 'max:255'],
            'description' => ['required_if:type,service_provider,shop_owner', 'string'],
            'service_image' => ['required_if:type,service_provider', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
            'price' => ['required_if:type,service_provider', 'numeric', 'min:0'],
            'experience_years' => ['required_if:type,service_provider', 'numeric', 'min:0'],
            'shop_image' => ['required_if:type,shop_owner', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
        ]);
    }
}
