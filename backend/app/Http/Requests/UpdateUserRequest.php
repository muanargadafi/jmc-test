<?php

namespace App\Http\Requests;

use App\Rules\StrongPasswordRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $user = $this->route('user');

        return [
            'role_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('roles', 'id'),
            ],
            'username' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'username')->ignore($user),
            ],

            'password' => [
                'sometimes',
                'nullable',
                'string',
                new StrongPasswordRule(),
                'confirmed'
            ],
            'name' => [
                'sometimes',
                'required',
                'string',
                'min:3',
                'max:100',
            ],
            'email' => [
                'sometimes',
                'required',
                'email',
                'min:3',
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
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
