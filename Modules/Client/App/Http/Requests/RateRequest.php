<?php

namespace Modules\Client\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Client\App\Models\ClientContact;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'contact_id' => ['required', 'exists:client_contacts,id'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'comment' => ['required', 'string'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'contact_id' => 'Contact ID',
            'rating' => 'Rating',
            'comment' => 'Comment',
        ];
    }


    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            $contactId = $this->input('contact_id');

            $contact = ClientContact::findOrFail($contactId);

            if ($contact->comment && $contact->rating) {
                throw new HttpResponseException(
                    returnMessage(
                        false,
                        'You have already rated and commented on this contact before',
                        null
                    )
                );
            }
        } elseif ($this->isMethod('put')) {
            $clientId = auth('user')->id();
            $contact = $this->route('contact_id');
            if ($contact->client_id != $clientId) {
                throw new HttpResponseException(
                    returnMessage(
                        false,
                        'You are not authorized to update or delete this rate',
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
