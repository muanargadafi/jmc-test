<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIncomingGoodRequest extends FormRequest
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
            'operator_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'sub_category_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('sub_categories', 'id'),
            ],
            'received_date' => [
                'sometimes',
                'required',
                'date',
            ],
            'origin' => [
                'sometimes',
                'required',
                'string',
                'max:200',
            ],
            'letter_number' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
            ],
            'attachment' => [
                'sometimes',
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048',
            ],
            'remove_attachment' => [
                'sometimes',
                'boolean',
            ],
            'items' => [
                'sometimes',
                'required',
                'array',
                'min:1',
            ],
            'items.*.name' => [
                'required_with:items',
                'string',
                'max:200',
            ],
            'items.*.price' => [
                'required_with:items',
                'numeric',
                'min:0',
            ],
            'items.*.quantity' => [
                'required_with:items',
                'numeric',
                'min:0.01',
            ],
            'items.*.unit' => [
                'required_with:items',
                'string',
                'max:40',
            ],
            'items.*.expired_date' => [
                'nullable',
                'date',
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
            'operator_id' => __('operator'),
            'sub_category_id' => __('sub category'),
            'received_date' => __('received date'),
            'origin' => __('origin'),
            'letter_number' => __('letter number'),
            'attachment' => __('attachment'),
            'items' => __('items'),
            'items.*.name' => __('item name'),
            'items.*.price' => __('item price'),
            'items.*.quantity' => __('item quantity'),
            'items.*.unit' => __('item unit'),
            'items.*.expired_date' => __('item expired date'),
        ];
    }
}
