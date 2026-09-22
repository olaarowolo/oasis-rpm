<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return session('role') === 'student';
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|min:10',
            'location' => 'required|string|max:255|min:5',
            'abstract' => 'required|string|min:100|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Research topic title is required',
            'title.min' => 'Title must be at least 10 characters',
            'location.required' => 'Research location is required',
            'abstract.required' => 'Research abstract is required',
            'abstract.min' => 'Abstract must be at least 100 characters',
        ];
    }
}
