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
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_ar' => 'sometimes|string',
            'description_en' => 'sometimes|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:1024',
            'category_id' => 'required|exists:categories,id',
            'sub_title_ar' => 'sometimes|string|max:255',
            'sub_title_en' => 'sometimes|string|max:255',
            'country_ids' => 'required_with:sub_title|array',
            'country_ids.*' => 'exists:countries,id',
        ];
    }
    public function attributes(): array
    {
        return [
            'title_ar' => trans('attributes.title_ar'),
            'title_en' => trans('attributes.title_en'),
            'description_ar' => trans('attributes.description_ar'),
            'description_en' => trans('attributes.description_en'),
            'image' => trans('attributes.image'),
            'category_id' => trans('attributes.category_id'),
            'sub_title_ar' => trans('attributes.sub_title_ar'),
            'sub_title_en' => trans('attributes.sub_title_en'),
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