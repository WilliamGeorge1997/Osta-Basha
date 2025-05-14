<?php

namespace Modules\Common\App\Http\Requests;

use Modules\Common\App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SettingRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'settings' => 'required|array',
        ];

        $settings = $this->input('settings');
        if (!is_array($settings)) {
            return $rules;
        }

        foreach ($settings as $index => $setting) {
            $rules["settings.{$index}.key"] = 'required|string|exists:settings,key';
            $rules["settings.{$index}.value"] = 'required';

            if (isset($setting['key'])) {
                $dbSetting = Setting::where('key', $setting['key'])->first();
                if ($dbSetting) {
                    $rules["settings.{$index}.value"] = "required|{$dbSetting->type}";
                }
            }
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'settings' => 'Settings',
            'settings.*.key' => 'Key',
            'settings.*.value' => 'Value',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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