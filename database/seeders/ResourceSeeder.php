<?php

namespace Database\Seeders;

use App\Models\Resource;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        $resources = [
            // Stage 1 Resources
            ['university_id' => 1, 'section' => 'Stage 1', 'type' => 'document', 'title' => 'Topic Selection Guide', 'stage' => 1, 'points' => 10],
            ['university_id' => 1, 'section' => 'Stage 1', 'type' => 'video', 'title' => 'Research Topic Workshop', 'stage' => 1, 'points' => 15],
            
            // Stage 2 Resources
            ['university_id' => 1, 'section' => 'Stage 2', 'type' => 'document', 'title' => 'Literature Review Framework', 'stage' => 2, 'points' => 15],
            ['university_id' => 1, 'section' => 'Stage 2', 'type' => 'link', 'title' => 'Academic Database Access', 'stage' => 2, 'points' => 10],
            
            // Stage 3 Resources
            ['university_id' => 1, 'section' => 'Stage 3', 'type' => 'document', 'title' => 'Chapter 1 Template', 'stage' => 3, 'points' => 20],
            ['university_id' => 1, 'section' => 'Stage 3', 'type' => 'video', 'title' => 'Introduction Writing Tips', 'stage' => 3, 'points' => 15],
            
            // Stage 4 Resources
            ['university_id' => 1, 'section' => 'Stage 4', 'type' => 'document', 'title' => 'Literature Review Template', 'stage' => 4, 'points' => 25],
            ['university_id' => 1, 'section' => 'Stage 4', 'type' => 'quiz', 'title' => 'Citation Styles Quiz', 'stage' => 4, 'points' => 10],
        ];

        foreach ($resources as $resource) {
            Resource::create([
                'university_id' => $resource['university_id'],
                'section' => $resource['section'],
                'type' => $resource['type'],
                'title' => $resource['title'],
                'url' => 'https://example.com/resources/' . strtolower(str_replace(' ', '-', $resource['title'])),
                'description' => 'Resource for ' . $resource['title'],
                'sort_order' => 0,
                'is_mandatory' => true,
                'stage' => $resource['stage'],
                'points' => $resource['points']
            ]);
        }

        // Replicate for UI
        foreach ($resources as $resource) {
            $resource['university_id'] = 2;
            Resource::create([
                'university_id' => 2,
                'section' => $resource['section'],
                'type' => $resource['type'],
                'title' => $resource['title'],
                'url' => 'https://example.com/resources/' . strtolower(str_replace(' ', '-', $resource['title'])),
                'description' => 'Resource for ' . $resource['title'],
                'sort_order' => 0,
                'is_mandatory' => true,
                'stage' => $resource['stage'],
                'points' => $resource['points']
            ]);
        }
    }
}
