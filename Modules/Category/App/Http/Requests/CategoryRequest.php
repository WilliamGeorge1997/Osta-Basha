<?php

namespace Modules\Category\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class CategoryRequest extends FormRequest
{

    public function rules(): array
    {
        $rules =  [
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_ar' => 'sometimes|string',
            'description_en' => 'sometimes|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:1024',
        ];
        if ($this->route('category')) {
            $rules['localizations'] = 'sometimes|array';
            $rules['localizations.*.title_ar'] = 'required|string|max:255';
            $rules['localizations.*.title_en'] = 'required|string|max:255';
            $rules['localizations.*.country_id'] = 'required|exists:countries,id';
        } else {
            $rules['sub_title_ar'] = 'sometimes|string|max:255';
            $rules['sub_title_en'] = 'sometimes|string|max:255';
            $rules['country_ids'] = 'required_with:sub_title|array';
            $rules['country_ids.*'] = 'exists:countries,id';
        }
        return $rules;
    }
    public function attributes(): array
    {
        return [
            'title_ar' => trans('attributes.title_ar'),
            'title_en' => trans('attributes.title_en'),
            'description_ar' => trans('attributes.description_ar'),
            'description_en' => trans('attributes.description_en'),
            'category_id' => trans('attributes.category_id'),
            'sub_title_ar' => trans('attributes.sub_title_ar'),
            'sub_title_en' => trans('attributes.sub_title_en'),
            'country_ids' => trans('attributes.country_ids'),
            'country_ids.*' => trans('attributes.country_ids.*'),
            'localizations' => trans('attributes.localizations'),
            'localizations.*.title_ar' => trans('attributes.title_ar'),
            'localizations.*.title_en' => trans('attributes.title_en'),
            'localizations.*.country_id' => trans('attributes.country_id'),
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