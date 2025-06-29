<?php

namespace Modules\User\App\Http\Requests;

use Modules\User\App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserDeleteImageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }

    public function authorize(): bool
    {
        $image_id = $this->route('id');
        $user = auth('user')->user();

        switch ($user->type) {
            case User::TYPE_SERVICE_PROVIDER:
                $image = $user->providerCertificates()->find($image_id);
                break;
            case User::TYPE_SHOP_OWNER:
                $image = $user->shopOwnerShopImages()->find($image_id);
                break;
            default:
                throw new HttpResponseException(
                    returnUnauthorizedMessage(
                        false,
                        trans('validation.invalid_user_type'),
                        null
                    )
                );
        }

        if (!$image) {
            throw new HttpResponseException(
                returnUnauthorizedMessage(
                    false,
                    trans('validation.image_not_found'),
                    null
                )
            );
        }

        return true;
    }
}
