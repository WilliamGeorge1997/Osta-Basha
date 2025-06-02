<?php

namespace Modules\Client\App\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\User\App\Models\User;
use Modules\Common\App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Client\App\Models\ClientContact;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClientContactRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'contactable_id' => [
                'required',
                'exists:users,id',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->whereIn('type', ['service_provider', 'shop_owner'])->where('is_active', 1);
                }),
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'contactable_id' => 'Contactable ID',
        ];
    }


public function authorize(): bool
{
    $contactableId = $this->input('contactable_id');

    $contactable = User::find($contactableId);
    $contactCount = ClientContact::where('contactable_id', $contactableId)->count();
    $freeTrialContactCount = Setting::where('key', 'free_trial_contacts_count')->first();

    if (($contactable->type == 'service_provider' && $contactable->providerProfile->status == 'free_trial') ||
        ($contactable->type == 'shop_owner' && $contactable->shopOwnerProfile->status == 'free_trial')) {

        if ($contactCount >= $freeTrialContactCount->value) {
            throw new HttpResponseException(
                returnMessage(
                    false,
                    'This contactable free trial has ended',
                    null
                )
            );
        }
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
