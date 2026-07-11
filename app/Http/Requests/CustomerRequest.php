<?php

namespace App\Http\Requests;

use App\Enums\ClientValidation;
use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone_1' => 'required|string|max:20',
            'phone_2' => 'nullable|string|max:20',
            'birthdate' => 'nullable|date',
            'notes' => 'nullable|string',
        ];

        $mode = Setting::current()->client_validation;

        // ID do cliente em edição, para não se acusar a si próprio.
        // Ajusta o nome do parâmetro à tua rota (ver nota abaixo).
        $clientId = $this->route('id') ?? $this->route('customer');

        $checkEmail = in_array($mode, [ClientValidation::Email, ClientValidation::EmailAndPhone], true);
        $checkPhone = in_array($mode, [ClientValidation::Phone, ClientValidation::EmailAndPhone], true);

        if ($checkEmail) {
            $rules['email'] = [
                'nullable', 'email', 'max:255',
                Rule::unique('clients', 'email')->ignore($clientId)->whereNull('deleted_at'),
            ];
        }

        if ($checkPhone) {
            $rules['phone_1'] = [
                'required', 'string', 'max:20',
                Rule::unique('clients', 'phone_1')->ignore($clientId)->whereNull('deleted_at'),
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validations.client_name_required'),
            'phone_1.required' => __('validations.client_phone_1_required'),
            'email.email' => __('validations.client_email_invalid'),
            'birthdate.date' => __('validations.client_birthdate_invalid'),
            'email.unique' => __('validations.client_email_duplicate'),
            'phone_1.unique' => __('validations.client_phone_1_duplicate'),
        ];
    }
}
