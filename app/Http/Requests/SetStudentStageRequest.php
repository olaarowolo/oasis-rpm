<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetStudentStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return session('role') === 'supervisor';
    }

    public function rules(): array
    {
        return [
            'stage' => 'required|integer|min:1|max:12',
            'note' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'stage.required' => 'Stage number is required',
            'stage.min' => 'Stage number must be between 1 and 12',
            'stage.max' => 'Stage number must be between 1 and 12',
        ];
    }
}
