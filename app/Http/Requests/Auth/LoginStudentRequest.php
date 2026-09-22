<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginStudentRequest extends FormRequest
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
            'matric_number' => [
                'required',
                'string',
                'min:5',
                'max:20',
                'regex:/^[A-Z0-9\-]+$/i',
            ],
            'lastname' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[A-Za-zÀ-ÿ\-\' ]+$/',
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
            'matric_number.required' => 'Matriculation number is required',
            'matric_number.regex' => 'Matriculation number can only contain letters, numbers, and hyphens',
            'lastname.required' => 'Last name is required',
            'lastname.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes',
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
            'matric_number' => 'matriculation number',
            'lastname' => 'last name',
        ];
    }
}
