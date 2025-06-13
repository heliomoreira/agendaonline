<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'duration' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',

            'duration.required' => 'A duração é obrigatória.',
            'duration.integer' => 'A duração deve ser um número inteiro.',
            'duration.min' => 'A duração deve ser no mínimo :min minutos.',

            'price.required' => 'O preço é obrigatório.',
            'price.numeric' => 'O preço deve ser um número.',
            'price.min' => 'O preço não pode ser negativo.',

            'image.string' => 'A imagem deve ser um texto (URL ou nome de ficheiro).',
            'image.max' => 'A imagem não pode ter mais de 255 caracteres.',

            'order.integer' => 'A ordem deve ser um número inteiro.',
            'order.min' => 'A ordem deve ser no mínimo :min.',

            'status.required' => 'O estado é obrigatório.',
            'status.boolean' => 'O estado deve ser verdadeiro ou falso.',

            'notes.string' => 'As notas devem ser um texto.',
        ];
    }


}
