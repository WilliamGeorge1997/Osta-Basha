<?php

namespace Modules\Admin\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateNotificationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
            'target_type' => ['nullable', 'string', 'in:clients,providers,shop_owners,all'],
            'target_ids' => ['nullable', 'array'],
            'target_ids.*' => ['integer', 'exists:users,id'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => trans('attributes.title'),
            'description' => trans('attributes.description'),
            'image' => trans('attributes.image'),
            'target_type' => trans('attributes.target_type'),
            'target_ids' => trans('attributes.target_ids'),
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'target_type.in' => 'Target type must be one of: clients, providers, shop_owners, or all',
            'target_ids.*.exists' => 'One or more selected users do not exist',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasTargetType = !empty($this->target_type);
            $hasTargetIds = !empty($this->target_ids);

            // Either target_type OR target_ids must be provided, but not both
            if (!$hasTargetType && !$hasTargetIds) {
                $validator->errors()->add('target_type', 'Either target_type or target_ids must be provided');
                $validator->errors()->add('target_ids', 'Either target_type or target_ids must be provided');
            }

            if ($hasTargetType && $hasTargetIds) {
                $validator->errors()->add('target_type', 'Cannot use both target_type and target_ids. Choose one.');
                $validator->errors()->add('target_ids', 'Cannot use both target_type and target_ids. Choose one.');
            }
        });
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