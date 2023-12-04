<?php

namespace App\Http\Requests;

use App\Rules\IngredientsAvailableInStock;
use Illuminate\Foundation\Http\FormRequest;

class ProcessOrderRequest extends FormRequest
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
            'products' => ['required', 'array', 'min:1', new IngredientsAvailableInStock],
            'products.*' => ['required_array_keys:product_id,quantity'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'products.required' => 'The products field is required.',
            'products.array' => 'The products field must be an array.',
            'products.min:1' => 'The products field must contain at least one product.',

            'products.*.required_array_keys' => 'Each product must have product_id and quantity keys.',
            'products.*.product_id.required' => 'The product_id field is required for each product.',
            'products.*.product_id.exists' => 'The specified product_id does not exist.',
            'products.*.quantity.required' => 'The quantity field is required for each product.',
            'products.*.quantity.integer' => 'The quantity field must be an integer.',
            'products.*.quantity.min:1' => 'The quantity field must be at least 1.',
        ];
    }
}
