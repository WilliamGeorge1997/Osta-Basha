<?php

namespace Modules\User\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Common\Helpers\ArabicNumeralsConverterTrait;

class UserUpdateProfileRequest extends FormRequest
{
    // use ArabicNumeralsConverterTrait {
    //     prepareForValidation as private traitPrepareForValidation;
    // }

    protected function prepareForValidation()
    {
        $data = $this->all();
    //     if (isset($data['whatsapp'])) {
    //         $data['whatsapp'] = $this->convertToWestern($data['whatsapp']);
    //     }
    //     if (isset($data['experience_years'])) {
    //         $data['experience_years'] = $this->convertToWestern($data['experience_years']);
    //     }
    //     if (isset($data['price'])) {
    //         $data['price'] = $this->convertToWestern($data['price']);
    //     }
    //     if (isset($data['card_number'])) {
    //         $data['card_number'] = $this->convertToWestern($data['card_number']);
    //     }
        if (isset($data['working_times']) && is_array($data['working_times'])) {
            $processedWorkingTimes = [];

            foreach ($data['working_times'] as $index => $workingTime) {
                if (!is_array($workingTime)) {
                    continue;
                }
    //             if (isset($workingTime['start_at'])) {
    //                 $workingTime['start_at'] = $this->convertToWestern($workingTime['start_at']);
    //             }
    //             if (isset($workingTime['end_at'])) {
    //                 $workingTime['end_at'] = $this->convertToWestern($workingTime['end_at']);
    //             }
                if (isset($workingTime['day']) && is_string($workingTime['day']) && strpos($workingTime['day'], ',') !== false) {
                    $days = explode(',', $workingTime['day']);
                    foreach ($days as $day) {
                        $processedWorkingTimes[] = [
                            'day' => trim($day),
                            'start_at' => $workingTime['start_at'] ?? null,
                            'end_at' => $workingTime['end_at'] ?? null,
                        ];
                    }
                } else {
                    $processedWorkingTimes[] = $workingTime;
                }
            }
            $data['working_times'] = $processedWorkingTimes;
        }
        $this->replace($data);
    //     foreach ($data as $key => $value) {
    //         $this->request->set($key, $value);
    //     }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {

        $user = auth('user')->user();

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'whatsapp' => ['required', 'string', 'max:255'],
            'whatsapp_country_code' => ['required', 'string', 'max:255'],
        ];

        if ($user->type === 'service_provider') {
            $rules = array_merge($rules, [
                'sub_category_id' => ['required', 'exists:sub_categories,id,is_active,1'],
                'card_number' => ['required', 'string', 'max:255'],
                'card_image' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
                'address' => ['required', 'string', 'max:255'],
                'experience_years' => ['required', 'numeric', 'min:0'],
                'experience_description' => ['required', 'string'],
                'price' => ['required', 'numeric', 'min:0'],
                'certificates' => ['sometimes', 'array'],
                'certificates.*' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
                'working_times' => ['required', 'array'],
                'working_times.*.day' => ['required', 'string'],
                'working_times.*.start_at' => ['required', 'date_format:H:i'],
                'working_times.*.end_at' => ['required', 'date_format:H:i', 'after:working_times.*.start_at'],
            ]);
        } elseif ($user->type === 'shop_owner') {
            $rules = array_merge($rules, [
                'sub_category_id' => ['required', 'exists:sub_categories,id,is_active,1'],
                'address' => ['required', 'string', 'max:255'],
                'shop_name' => ['required', 'string', 'max:255'],
                'products_description' => ['required', 'string'],
                'experience_years' => ['required', 'numeric', 'min:0'],
                'shop_images' => ['sometimes', 'array'],
                'shop_images.*' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
                'working_times' => ['required', 'array'],
                'working_times.*.day' => ['required', 'string'],
                'working_times.*.start_at' => ['required', 'date_format:H:i'],
                'working_times.*.end_at' => ['required', 'date_format:H:i', 'after:working_times.*.start_at'],
            ]);
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'card_number' => trans('attributes.card_number'),
            'card_image' => trans('attributes.card_image'),
            'address' => trans('attributes.address'),
            'experience_years' => trans('attributes.experience_years'),
            'experience_description' => trans('attributes.experience_description'),
            'price' => trans('attributes.price'),
            'certificates' => trans('attributes.certificates'),
            'certificates.*' => trans('attributes.certificate'),
            'working_times' => trans('attributes.working_times'),
            'working_times.*.day' => trans('attributes.day'),
            'working_times.*.start_at' => trans('attributes.start_at'),
            'working_times.*.end_at' => trans('attributes.end_at'),
            'first_name' => trans('attributes.first_name'),
            'last_name' => trans('attributes.last_name'),
            'email' => trans('attributes.email'),
            'sub_category_id' => trans('attributes.sub_category_id'),
            'shop_name' => trans('attributes.shop_name'),
            'products_description' => trans('attributes.products_description'),
            'shop_images' => trans('attributes.shop_images'),
            'shop_images.*' => trans('attributes.shop_image'),
            'whatsapp_country_code' => trans('attributes.whatsapp_country_code'),
            'whatsapp' => trans('attributes.whatsapp'),
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
