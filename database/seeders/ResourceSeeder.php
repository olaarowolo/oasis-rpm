<?php

namespace Database\Seeders;

use App\Models\Resource;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        $resources = [
            [
                'section' => 'Current Assignment',
                'type' => 'video',
                'title' => 'BSc Project Guide for Nigerian Students — Research Expectations Explained',
                'url' => 'https://youtu.be/v26gZYpOvmQ',
                'description' => 'Current assignment video 3.',
                'sort_order' => 1,
                'is_mandatory' => true,
                'stage' => 1,
                'points' => 10,
            ],
            [
                'section' => 'Required Worksheets',
                'type' => 'document',
                'title' => 'rg-worksheet.pdf',
                'url' => 'http://olaarowolo.com/OAsis-AA',
                'description' => 'Research gap worksheet (RG).',
                'sort_order' => 1,
                'is_mandatory' => true,
                'stage' => 2,
                'points' => 10,
            ],
            [
                'section' => 'Required Worksheets',
                'type' => 'document',
                'title' => 'rge-worksheet.pdf',
                'url' => 'http://olaarowolo.com/OAsis-AA',
                'description' => 'Research gap worksheet extended (RGE).',
                'sort_order' => 2,
                'is_mandatory' => true,
                'stage' => 2,
                'points' => 10,
            ],
            [
                'section' => 'Prerequisite Videos',
                'type' => 'video',
                'title' => 'How to Email Your Project Supervisor Correctly — Student Guide for Academic Emails',
                'url' => 'https://youtu.be/yZ5murFFSJs',
                'description' => 'Video 1 — must be completed.',
                'sort_order' => 1,
                'is_mandatory' => true,
                'stage' => 2,
                'points' => 10,
            ],
            [
                'section' => 'Prerequisite Videos',
                'type' => 'video',
                'title' => 'Undergraduate Dissertation Blueprint — 12 Week Roadmap, Supervision Strategy and AI Ethics',
                'url' => 'https://youtu.be/9LfcXS4wVzQ',
                'description' => 'Video 2 — must be completed.',
                'sort_order' => 2,
                'is_mandatory' => true,
                'stage' => 2,
                'points' => 10,
            ],
            [
                'section' => 'Prerequisite Videos',
                'type' => 'video',
                'title' => 'How to Email Your Project Supervisor Correctly — Student Guide for Academic Emails (Lesson 1)',
                'url' => 'https://youtu.be/yZ5murFFSJs?is=pOJ99TyootbwwcN6',
                'description' => 'Lesson 1 — supplementary.',
                'sort_order' => 3,
                'is_mandatory' => false,
                'stage' => 2,
                'points' => 5,
            ],
        ];

        foreach ([1, 2] as $universityId) {
            foreach ($resources as $resource) {
                Resource::firstOrCreate(
                    [
                        'university_id' => $universityId,
                        'section' => $resource['section'],
                        'title' => $resource['title'],
                    ],
                    [
                        'type' => $resource['type'],
                        'url' => $resource['url'],
                        'description' => $resource['description'],
                        'sort_order' => $resource['sort_order'],
                        'is_mandatory' => $resource['is_mandatory'],
                        'stage' => $resource['stage'],
                        'points' => $resource['points'],
                    ]
                );
            }
        }
    }
}
