<?php

namespace Modules\Category\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class SubCategoryRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'sometimes|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:1024',
            'category_id' => 'required|exists:categories,id',
            'sub_title' => 'sometimes|string|max:255',
            'country_ids' => 'required_with:sub_title|array',
            'country_ids.*' => 'exists:countries,id',
        ];
    }
    public function attributes(): array
    {
        return [
            'title' => trans('attributes.title'),
            'description' => trans('attributes.description'),
            'image' => trans('attributes.image'),
            'category_id' => trans('attributes.category_id'),
            'sub_title' => trans('attributes.sub_title'),
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