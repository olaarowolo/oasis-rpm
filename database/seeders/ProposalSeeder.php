<?php

namespace Database\Seeders;

use App\Models\Proposal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProposalSeeder extends Seeder
{
    public function run(): void
    {
        Proposal::create([
            'university_id' => 1,
            'student_id' => 1,
            'proposal_id' => 'PROP-' . Str::upper(Str::random(8)),
            'title' => 'Digital Media and Social Change',
            'location' => 'Lagos, Nigeria',
            'abstract' => 'A comprehensive study of how digital media platforms influence social change in Nigeria.',
            'date_submitted' => now()->subDays(30),
            'status' => 'approved',
            'supervisor_comment' => 'Excellent proposal. Well-structured and timely research topic.',
            'conditions' => null
        ]);

        Proposal::create([
            'university_id' => 1,
            'student_id' => 2,
            'proposal_id' => 'PROP-' . Str::upper(Str::random(8)),
            'title' => 'Youth Engagement in Political Discourse',
            'location' => 'Abuja, Nigeria',
            'abstract' => 'An investigation into how young Nigerians engage with political discourse online.',
            'date_submitted' => now()->subDays(15),
            'status' => 'pending',
            'supervisor_comment' => null,
            'conditions' => null
        ]);

        Proposal::create([
            'university_id' => 2,
            'student_id' => 6,
            'proposal_id' => 'PROP-' . Str::upper(Str::random(8)),
            'title' => 'Higher Education Innovation',
            'location' => 'Ibadan, Nigeria',
            'abstract' => 'Exploring innovative teaching methodologies in higher education institutions.',
            'date_submitted' => now()->subDays(45),
            'status' => 'approved',
            'supervisor_comment' => 'Good research area with practical implications.',
            'conditions' => null
        ]);
    }
}
