<?php

namespace Modules\Client\App\Http\Requests;

use Modules\Client\App\Models\Rate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RateRequest extends FormRequest
{
    public function rules()
    {
        if ($this->isMethod('delete')) return [];
        $data = [
            'rate' => ['required', 'numeric', 'min:1', 'max:5'],
            'comment' => ['required', 'string'],
        ];
        return $data;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'rate' => 'Rate',
            'comment' => 'Comment',
        ];
    }


    public function authorize(): bool
    {
        $clientContactRate = $this->route('clientContact');
        if ($this->isMethod('post')) {
            if ($clientContactRate->rate != null && $clientContactRate->comment != null) {
                throw new HttpResponseException(
                    returnMessage(
                        false,
                        'You have already rated and commented on this contact before',
                        null
                    )
                );
            }
        } elseif ($this->isMethod('put') || $this->isMethod('delete')) {
            $clientId = auth('user')->id();
            if ($clientContactRate->client_id !== $clientId) {
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
