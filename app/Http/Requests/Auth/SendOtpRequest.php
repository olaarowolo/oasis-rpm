<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SendOtpRequest extends FormRequest
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
            'role' => [
                'required',
                'string',
                'in:student,supervisor,admin,super_admin',
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'password' => [
                'nullable',
                'string',
                'min:12',
                'max:255',
            ],
            'university_code' => [
                'nullable',
                'string',
                'uppercase',
                'exists:universities,code',
            ],
            'matric_number' => [
                'nullable',
                'string',
                'min:5',
                'max:20',
                'regex:/^[A-Z0-9\-]+$/i',
            ],
            'lastname' => [
                'nullable',
                'string',
                'min:2',
                'max:100',
                'regex:/^[A-Za-zÀ-ÿ\-\' ]+$/',
            ],
            'pin_code' => [
                'nullable',
                'string',
                'min:4',
                'max:20',
            ],
            'passphrase' => [
                'nullable',
                'string',
                'min:8',
                'max:255',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'role.required' => 'User role is required',
            'role.in' => 'Invalid role selected',
            'email.email' => 'Please enter a valid email address',
            'password.min' => 'Password must be at least 12 characters',
            'university_code.exists' => 'Invalid university code',
            'matric_number.regex' => 'Matriculation number can only contain letters, numbers, and hyphens',
            'lastname.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes',
            'pin_code.min' => 'PIN code must be at least 4 characters',
            'passphrase.min' => 'Passphrase must be at least 8 characters',
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
            'role' => 'role',
            'email' => 'email address',
            'password' => 'password',
            'university_code' => 'university code',
            'matric_number' => 'matriculation number',
            'lastname' => 'last name',
            'pin_code' => 'PIN code',
            'passphrase' => 'passphrase',
        ];
    }
}
