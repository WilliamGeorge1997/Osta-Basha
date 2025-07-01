<?php

namespace Modules\Category\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Category\App\Models\SubCategoryLocalization;

class SubCategoryLocalizationRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'country_ids' => 'required|array',
            'country_ids.*' => [
                'exists:countries,id',
                function ($attribute, $value, $fail) {
                    $subCategoryId = $this->input('sub_category_id');
                    $exists = SubCategoryLocalization::where('sub_category_id', $subCategoryId)
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
            'title_ar' => trans('attributes.title_ar'),
            'title_en' => trans('attributes.title_en'),
            'sub_category_id' => trans('attributes.sub_category_id'),
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