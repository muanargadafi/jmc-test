<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubCategoryRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('categories', 'id'),
            ],
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:100'
            ],
            'price_limit' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
                'max:9999999999999.99',
                'decimal:0,2'
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'category_id' => __('category'),
            'name' => __('name'),
            'price_limit' => __('price limit'),

        ];
    }
}
