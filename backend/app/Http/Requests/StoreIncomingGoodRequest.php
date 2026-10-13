<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIncomingGoodRequest extends FormRequest
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
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'sub_category_id' => [
                'required',
                'integer',
                Rule::exists('sub_categories', 'id'),
            ],
            'received_date' => [
                'required',
                'date',
            ],
            'origin' => [
                'required',
                'string',
                'max:200',
            ],
            'letter_number' => [
                'nullable',
                'string',
                'max:100',
            ],
            'attachment' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048',
            ],
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*.name' => [
                'required',
                'string',
                'max:200',
            ],
            'items.*.price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'items.*.quantity' => [
                'required',
                'numeric',
                'min:0.01',
            ],
            'items.*.unit' => [
                'required',
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
