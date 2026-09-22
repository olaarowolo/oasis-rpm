<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMeetingLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return session('role') === 'student';
    }

    public function rules(): array
    {
        return [
            'meeting_number' => 'required|integer|min:1',
            'meeting_date' => 'required|date|before_or_equal:today',
            'meeting_mode' => 'required|in:in_person,virtual,hybrid',
            'duration' => 'required|integer|min:15|max:480',
            'previous_actions' => 'required|string|min:10',
            'progress_since' => 'required|string|min:10',
            'discussion_points' => 'required|string|min:10',
            'work_reviewed' => 'required|string|min:10',
            'chapter_focus' => 'required|string|max:255',
            'next_meeting_date' => 'required|date|after:meeting_date',
            'next_meeting_focus' => 'required|string|min:10|max:255',
            'risks' => 'nullable|string|max:1000',
            'support_required' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'meeting_date.before_or_equal' => 'Meeting date cannot be in the future',
            'next_meeting_date.after' => 'Next meeting date must be after current meeting date',
            'duration.min' => 'Meeting duration must be at least 15 minutes',
        ];
    }
}
