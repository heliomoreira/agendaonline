<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
            'product_code'     => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'product_code')->ignore($this->route('id')),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price_1' => ['required', 'numeric', 'min:0'],
            'price_2' => ['nullable', 'numeric', 'min:0'],
            'stock_movement' => ['required', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'], // 2MB
            'status' => ['required', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_code.required' => 'O código do produto é obrigatório.',
            'product_code.string' => 'O código do produto deve ser uma cadeia de texto.',
            'product_code.max' => 'O código do produto não pode ter mais de :max caracteres.',
            'product_code.unique' => 'Este código de produto já existe.',

            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser uma cadeia de texto.',
            'name.max' => 'O nome não pode ter mais de :max caracteres.',

            'description.string' => 'A descrição deve ser uma cadeia de texto.',

            'price_1.required' => 'O preço principal é obrigatório.',
            'price_1.numeric' => 'O preço principal deve ser um número.',
            'price_1.min' => 'O preço principal não pode ser negativo.',

            'price_2.numeric' => 'O preço secundário deve ser um número.',
            'price_2.min' => 'O preço secundário não pode ser negativo.',

            'stock_movement.required' => 'O campo de controlo de stock é obrigatório.',
            'stock_movement.boolean' => 'O campo de controlo de stock deve ser verdadeiro ou falso.',

            'image.image' => 'O ficheiro da imagem deve ser uma imagem válida.',
            'image.max' => 'A imagem não pode ter mais de 2MB.',

            'status.required' => 'O estado do produto é obrigatório.',
            'status.boolean' => 'O estado deve ser verdadeiro ou falso.',

            'notes.string' => 'As notas devem ser uma cadeia de texto.',
        ];
    }
}
