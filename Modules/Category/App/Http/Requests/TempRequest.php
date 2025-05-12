<?php

namespace Modules\Category\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class TempRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        if (!$this->isMethod('delete') || !$this->is('*/toggle-activate')) {
            $admin = auth('admin')->user();
            if ($admin && !$admin->hasRole('Super Admin')) {
                $this->merge([
                    'restaurant_id' => $admin->restaurant_id
                ]);
            }
        }
    }

    public function authorize(): bool
    {
        $branch = $this->route('branch');
        if (!$branch) {
            return true;
        }
        $admin = auth('admin')->user();
        if ($admin && !$admin->hasRole('Super Admin')) {
            if ($admin->restaurant_id !== $branch->restaurant_id) {
                throw new HttpResponseException(
                    returnUnauthorizedMessage(
                        false,
                        trans('validation.unauthorized'),
                        null
                    )
                );
            }
        }
        return true;
    }
    public function rules(): array
    {
        if ($this->isMethod('delete') || $this->is('*/toggle-activate')) {
            return [];
        }
        return [
            'name' => 'required|string|max:255',
            'restaurant_id' => 'required|exists:restaurants,id',
            'city_id' => 'required|exists:cities,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'block' => 'required|string',
            'street' => 'required|string',
            'building_number' => 'required|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ];
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