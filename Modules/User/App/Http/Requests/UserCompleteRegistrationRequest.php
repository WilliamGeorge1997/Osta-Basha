<?php

namespace Modules\User\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserCompleteRegistrationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users'],
            'type' => ['required', 'in:client,service_provider,shop_owner'],
            //Provider Or Shop Owner
            'card_number' => ['required_if:type,service_provider,shop_owner', 'string', 'max:255'],
            'card_image' => ['required_if:type,service_provider,shop_owner', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
            'address' => ['required_if:type,service_provider,shop_owner', 'string', 'max:255'],
            'experience_years' => ['required_if:type,service_provider,shop_owner', 'numeric', 'min:0'],
            'experience_description' => ['required_if:type,service_provider,shop_owner', 'string'],
            'min_price' => ['required_if:type,service_provider,shop_owner', 'numeric', 'min:0'],
            'max_price' => ['required_if:type,service_provider,shop_owner', 'numeric', 'min:0'],
            //Certificates
            'certificates' => ['required_if:type,service_provider,shop_owner', 'array'],
            'certificates.*' => ['required_if:type,service_provider,shop_owner', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
            //Working Times
            'working_times' => ['required_if:type,service_provider,shop_owner', 'array'],
            'working_times.*.day' => ['required_if:type,service_provider,shop_owner', 'string', 'in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday'],
            'working_times.*.start_at' => ['required_if:type,service_provider,shop_owner', 'date_format:H:i'],
            'working_times.*.end_at' => ['required_if:type,service_provider,shop_owner', 'date_format:H:i', 'after:provider_working_times.*.start_at'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'phone' => 'Phone Number',
            'password' => 'Password',
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
