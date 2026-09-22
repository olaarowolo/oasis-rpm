<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginSupervisorRequestRequest extends FormRequest
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
            'university_code' => [
                'required',
                'string',
                'uppercase',
                'exists:universities,code',
            ],
            'pin_code' => [
                'required',
                'string',
                'min:4',
                'max:20',
            ],
            'passphrase' => [
                'required',
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
            'university_code.required' => 'University code is required',
            'university_code.exists' => 'Invalid university code',
            'pin_code.required' => 'PIN code is required',
            'pin_code.min' => 'PIN code must be at least 4 characters',
            'passphrase.required' => 'Passphrase is required',
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
            'university_code' => 'university code',
            'pin_code' => 'PIN code',
            'passphrase' => 'passphrase',
        ];
    }
}
