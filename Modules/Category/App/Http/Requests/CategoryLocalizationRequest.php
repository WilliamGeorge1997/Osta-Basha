<?php

namespace Modules\Category\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Category\App\Models\CategoryLocalization;

class CategoryLocalizationRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'country_ids' => 'required|array',
            'country_ids.*' => [
                'exists:countries,id',
                function ($attribute, $value, $fail) {
                    $categoryId = $this->input('category_id');
                    $exists = CategoryLocalization::where('category_id', $categoryId)
                        ->where('country_id', $value)
                        ->exists();
                    if ($exists) {
                        $fail(trans('validation.unique', ['attribute' => trans('attributes.country_ids.*')]));
                    }
                }
            ],
        ];
    }
    public function attributes(): array
    {
        return [
            'title' => trans('attributes.title'),
            'category_id' => trans('attributes.category_id'),
            'country_ids' => trans('attributes.country_ids'),
            'country_ids.*' => trans('attributes.country_ids.*'),
        ];
    }
    public function authorize(): bool
    {
        return true;
    }

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