<?php

namespace Database\Factories;

use App\Models\MeetingLog;
use App\Models\Student;
use App\Models\University;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeetingLogFactory extends Factory
{
    protected $model = MeetingLog::class;

    public function definition(): array
    {
        return [
            'university_id' => University::factory(),
            'student_id' => Student::factory(),
            'log_id' => 'LOG-' . strtoupper(str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT)),
            'meeting_number' => $this->faker->numberBetween(1, 50),
            'meeting_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'meeting_mode' => $this->faker->randomElement(['in_person', 'virtual', 'hybrid']),
            'duration' => $this->faker->numberBetween(30, 180),
            'previous_actions' => $this->faker->paragraphs(2, true),
            'progress_since' => $this->faker->paragraphs(2, true),
            'discussion_points' => $this->faker->paragraphs(3, true),
            'work_reviewed' => $this->faker->text,
            'chapter_focus' => $this->faker->word,
            'risks' => null,
            'support_required' => null,
            'next_meeting_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'next_meeting_focus' => $this->faker->text,
            'student_signature' => null,
            'feedback_summary' => null,
            'areas_revision' => null,
            'agreed_actions' => null,
            'supervisor_signature' => null,
            'signoff_date' => null,
            'status' => $this->faker->randomElement(['draft', 'submitted', 'approved', 'reviewed']),
            'feedback' => null,
        ];
    }

    public function approved(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
                'supervisor_signature' => random_int(1, 10),
                'signoff_date' => now(),
            ];
        });
    }
}
