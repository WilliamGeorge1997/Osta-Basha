<?php

namespace Modules\User\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserChooseType extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'in:client,service_provider,shop_owner'],
            'long' => [
                'required_if:type,service_provider,shop_owner',
                'sometimes',
                'numeric',
                'min:-180',
                'max:180'
            ],
            'lat' => [
                'required_if:type,service_provider,shop_owner',
                'sometimes',
                'numeric',
                'min:-90',
                'max:90'
            ],
            'city' => [
                'required',
                'string',
                'max:255'
            ],
            'country' => [
                'required',
                'string',
                'max:255'
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'type' => trans('attributes.type'),
            'long' => trans('attributes.long'),
            'lat' => trans('attributes.lat'),
            'city' => trans('attributes.city'),
            'country' => trans('attributes.country'),
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth('user')->user();
        if ($user->is_active !== 1)
            throw new HttpResponseException(
                returnUnauthorizedMessage(
                    false,
                    trans('validation.user_not_verified'),
                    null
                )
            );
        return true;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            returnValidationMessage(
                false,
                trans('validation.rules_failed'),
                $validator->errors()->messages(),
                'unprocessable_entity'
            )
        );
    }
}
