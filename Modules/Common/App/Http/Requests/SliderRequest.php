<?php

namespace Modules\Common\App\Http\Requests;

use Modules\User\App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SliderRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {


        return [
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:1024',
            'user_id' => [
                'sometimes',
                'exists:users,id,is_active,1',
                function ($attribute, $value, $fail) {
                    $user = User::find($value);
                    if (!$user || $user->type == User::TYPE_CLIENT) {
                        $fail('The selected user must be a service provider or shop owner.');
                    }
                }
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title_ar' => trans('attributes.title_ar'),
            'title_en' => trans('attributes.title_en'),
            'description_ar' => trans('attributes.description_ar'),
            'description_en' => trans('attributes.description_en'),
            'image' => trans('attributes.image'),
            'user_id' => trans('attributes.user_id'),
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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