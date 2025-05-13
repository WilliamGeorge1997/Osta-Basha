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

        $user = auth('user')->user();
        $isProviderOrShop = in_array($user->type, ['service_provider', 'shop_owner']);

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users'],
        ];

        if ($isProviderOrShop) {
            $rules = array_merge($rules, [
                'sub_category_id' => ['required', 'exists:sub_categories,id,is_active,1'],
                'card_number' => ['required', 'string', 'max:255'],
                'card_image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
                'address' => ['required', 'string', 'max:255'],
                'experience_years' => ['required', 'numeric', 'min:0'],
                'experience_description' => ['required', 'string'],
                'min_price' => ['required', 'numeric', 'min:0'],
                'max_price' => ['required', 'numeric', 'min:0'],
                'certificates' => ['required', 'array'],
                'certificates.*' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
                'working_times' => ['required', 'array'],
                'working_times.*.day' => ['required', 'string', 'in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday'],
                'working_times.*.start_at' => ['required', 'date_format:H:i'],
                'working_times.*.end_at' => ['required', 'date_format:H:i', 'after:working_times.*.start_at'],
            ]);
        }

        return $rules;
        // $user = auth('user')->user();
        // $type = $user->type;
        // return [
        //     'first_name' => ['required', 'string', 'max:255'],
        //     'last_name' => ['required', 'string', 'max:255'],
        //     'email' => ['sometimes', 'email', 'max:255', 'unique:users'],
        //     //Provider Or Shop Owner
        //     'card_number' => ['required_if:type,service_provider,shop_owner', 'string', 'max:255'],
        //     'card_image' => ['required_if:type,service_provider,shop_owner', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
        //     'address' => ['required_if:type,service_provider,shop_owner', 'string', 'max:255'],
        //     'experience_years' => ['required_if:type,service_provider,shop_owner', 'numeric', 'min:0'],
        //     'experience_description' => ['required_if:type,service_provider,shop_owner', 'string'],
        //     'min_price' => ['required_if:type,service_provider,shop_owner', 'numeric', 'min:0'],
        //     'max_price' => ['required_if:type,service_provider,shop_owner', 'numeric', 'min:0'],
        //     //Certificates
        //     'certificates' => ['required_if:type,service_provider,shop_owner', 'array'],
        //     'certificates.*' => ['required_if:type,service_provider,shop_owner', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
        //     //Working Times
        //     'working_times' => ['required_if:type,service_provider,shop_owner', 'array'],
        //     'working_times.*.day' => ['required_if:type,service_provider,shop_owner', 'string', 'in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday'],
        //     'working_times.*.start_at' => ['required_if:type,service_provider,shop_owner', 'date_format:H:i'],
        //     'working_times.*.end_at' => ['required_if:type,service_provider,shop_owner', 'date_format:H:i', 'after:provider_working_times.*.start_at'],
        // ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'card_number' => 'Card Number',
            'card_image' => 'Card Image',
            'address' => 'Address',
            'experience_years' => 'Experience Years',
            'experience_description' => 'Experience Description',
            'min_price' => 'Min Price',
            'max_price' => 'Max Price',
            'certificates' => 'Certificates',
            'certificates.*' => 'Certificate',
            'working_times' => 'Working Times',
            'working_times.*.day' => 'Day',
            'working_times.*.start_at' => 'Start At',
            'working_times.*.end_at' => 'End At',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth('user')->user();
        if ($user->type == null) {
            throw new HttpResponseException(
                returnUnauthorizedMessage(
                    false,
                    trans('validation.user_type_not_set'),
                    null
                )
            );
        }
        if ($user->completed_registration == 1) {
            throw new HttpResponseException(
                returnUnauthorizedMessage(
                    false,
                    trans('validation.user_already_completed_registration'),
                    null
                )
            );
        }
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
