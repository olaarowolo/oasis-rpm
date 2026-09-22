<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
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
                'required',
                'email',
                'max:255',
            ],
            'otp' => [
                'required',
                'string',
                'size:6',
                'regex:/^[0-9]{6}$/',
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
            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address',
            'otp.required' => 'OTP code is required',
            'otp.size' => 'OTP code must be exactly 6 digits',
            'otp.regex' => 'OTP code must contain only numbers',
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
            'otp' => 'OTP code',
        ];
    }
}
