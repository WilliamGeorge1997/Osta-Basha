<?php

namespace Modules\Country\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CountryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'currency_ar' => 'required|string|max:255',
            'currency_en' => 'required|string|max:255',
            'country_code' => 'sometimes|nullable|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'title_ar' => trans('attributes.title_ar'),
            'title_en' => trans('attributes.title_en'),
            'currency_ar' => trans('attributes.currency_ar'),
            'currency_en' => trans('attributes.currency_en'),
            'country_code' => trans('attributes.country_code'),
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
     * Handle a failed validation attempt for API requests.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
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
