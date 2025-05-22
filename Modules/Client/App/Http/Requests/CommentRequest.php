<?php

namespace Modules\Client\App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CommentRequest extends FormRequest
{
    public function rules(): array
    {
        if($this->isMethod('post')){
        return [
            'commentable_id' => [
                'required',
                'exists:users,id',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->whereIn('type', ['service_provider', 'shop_owner'])->where('is_active', 1);
                }),
            ],
                'comment' => ['required', 'string'],
            ];
        } elseif ($this->isMethod('put')) {
            return [
                'comment' => ['required', 'string'],
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
            'commentable_id' => 'Commentable ID',
            'comment' => 'Comment',
        ];
    }


    public function authorize(): bool
    {
        if (!$this->isMethod('post')) {
            $comment = $this->route('comment');
            if ($comment->client_id != auth('user')->id()) {
                throw new HttpResponseException(
                    returnMessage(
                        false,
                        'You are not authorized to update or delete this comment',
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
