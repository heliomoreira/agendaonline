<?php

namespace App\Http\Requests;

use App\Enums\ClientValidation;
use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'zip_code' => ['nullable', 'string', 'max:10'],
            'city' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_1' => ['nullable', 'string', 'max:20'],
            'phone_2' => ['nullable', 'string', 'max:20'],
            'birthdate' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'is_minor' => ['boolean'],
            'parent_name' => ['nullable', 'required_if:is_minor,1', 'string', 'max:255'],
            'parent_phone_1_country_code' => ['nullable', 'string', 'max:10'],
            'parent_phone_1' => ['nullable', 'required_if:is_minor,1', 'string', 'max:30'],
            'parent_email' => ['nullable', 'email', 'max:255'],
            'parent_notes' => ['nullable', 'string'],
            'send_sms_to_parent' => ['boolean'],
            'parent_sms_template_id' => ['nullable', 'exists:sms_templates,id'],
        ];

        $mode = Setting::current()->client_validation;

        if ($mode === ClientValidation::Email) {
            $rules['email'] = ['required', 'email', 'max:255'];
        } elseif ($mode === ClientValidation::Phone) {
            $rules['phone_1'] = ['required', 'string', 'max:20'];
        } elseif ($mode === ClientValidation::EmailAndPhone) {
            $rules['email'] = ['required', 'email', 'max:255'];
            $rules['phone_1'] = ['required', 'string', 'max:20'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => __('modules.client_name_required'),
            'phone_1.required' => __('modules.client_phone_1_required'),
            'email.required' => __('modules.client_email_required'),
            'email.email' => __('modules.client_email_invalid'),
            'birthdate.date' => __('modules.client_birthdate_invalid'),
        ];
    }
}
