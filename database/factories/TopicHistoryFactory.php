<?php

namespace Database\Factories;

use App\Models\TopicHistory;
use App\Models\Proposal;
use App\Models\Student;
use App\Models\University;
use Illuminate\Database\Eloquent\Factories\Factory;

class TopicHistoryFactory extends Factory
{
    protected $model = TopicHistory::class;

    public function definition(): array
    {
        return [
            'university_id' => University::factory(),
            'student_id' => Student::factory(),
            'proposal_id' => Proposal::factory(),
            'topic_title' => $this->faker->sentence(6),
            'action' => $this->faker->randomElement(['submitted', 'approved', 'rejected', 'revision_required', 'resubmitted']),
            'note' => $this->faker->text,
        ];
    }
}
