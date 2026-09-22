<?php

namespace Database\Factories;

use App\Models\ArchiveSubmission;
use App\Models\Student;
use App\Models\University;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArchiveSubmissionFactory extends Factory
{
    protected $model = ArchiveSubmission::class;

    public function definition(): array
    {
        return [
            'university_id' => University::factory(),
            'student_id' => Student::factory(),
            'degree_level' => $this->faker->randomElement(['BSc', 'MSc', 'PhD']),
            'project_type' => $this->faker->randomElement(['project', 'thesis', 'dissertation']),
            'title' => $this->faker->sentence(6),
            'abstract' => $this->faker->paragraphs(3, true),
            'keywords' => implode(', ', $this->faker->words(5)),
            'department' => $this->faker->word,
            'submission_status' => ArchiveSubmission::STATUS_DRAFT,
            'visibility' => $this->faker->randomElement(['private', 'institution_only', 'public']),
            'final_document_path' => null,
            'supplementary_document_path' => null,
            'submitted_at' => null,
            'reviewed_at' => null,
            'published_at' => null,
            'reviewed_by_user_id' => null,
            'reviewer_note' => null,
        ];
    }
}
