<?php

namespace Modules\Client\App\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Client\App\Models\Rate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RateRequest extends FormRequest
{
    public function rules()
    {
        if ($this->isMethod('post')) {
            return [
                'rateable_id' => [
                    'required',
                    'exists:users,id',
                    Rule::exists('users', 'id')->where(function ($query) {
                        $query->whereIn('type', ['service_provider', 'shop_owner'])->where('is_active', 1);
                    }),
                ],
                'rate' => ['required', 'numeric', 'min:1', 'max:5'],
            ];
        } elseif ($this->isMethod('put')) {
            return [
                'rate' => ['required', 'numeric', 'min:1', 'max:5'],
            ];
        } else {
            return [];
        }
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'rateable_id' => 'Rateable ID',
            'rate' => 'Rate',
        ];
    }


    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            $rateableId = $this->input('rateable_id');

            $rate = Rate::where('client_id', auth()->id())
                ->where('rateable_id', $rateableId)
                ->exists();

            if ($rate) {
                throw new HttpResponseException(
                    returnMessage(
                        false,
                        'You have already rated this rateable before',
                        null
                    )
                );
            }
        } else {
            $clientId = auth('user')->id();
            $rate = $this->route('rate');
            if ($rate->client_id != $clientId) {
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
