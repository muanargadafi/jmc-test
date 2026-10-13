<?php

namespace App\Http\Requests;

use App\Rules\StrongPasswordRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id'),
            ],
            'username' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'username'),
            ],
            'password' => [
                'required',
                'string',
                new StrongPasswordRule(),
            ],
            'name' => [
                'required',
                'string',
                'min:3',
                'max:100',
            ],
            'email' => [
                'required',
                'email',
                'min:3',
                'max:255',
                Rule::unique('users', 'email'),
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
            'role_id' => __('role'),
            'username' => __('username'),
            'password' => __('password'),
            'name' => __('name'),
            'email' => __('email'),

        ];
    }
}
