<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
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
    }

    public function messages(): array{
        return [
            'name.required' => __('modules.client_name_required'),
            'phone_1.required' => __('modules.client_phone_1_required'),
            'email.email' => __('modules.client_email_invalid'),
            'birthdate.date' => __('modules.client_birthdate_invalid'),
        ];
    }
}
