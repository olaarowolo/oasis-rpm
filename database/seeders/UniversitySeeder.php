<?php

namespace Database\Seeders;

use App\Models\University;
use Illuminate\Database\Seeder;

class UniversitySeeder extends Seeder
{
    public function run(): void
    {
        $universities = [
            [
                'name' => 'Lagos State University',
                'code' => 'LASU',
                'email' => 'research@lasu.edu.ng',
                'department' => 'Journalism and Media Studies',
                'phone' => '+234 (0)1 123-4567',
                'logo_url' => null,
                'branding_color' => '#003366',
                'ai_model_config' => [
                    'primary' => 'gemini-pro',
                    'fallback' => 'gpt-4-turbo',
                    'timeout' => 30,
                ],
                'email_config' => [
                    'from_address' => 'research@lasu.edu.ng',
                    'from_name' => 'LASU Research Portal',
                    'reply_to' => 'support@lasu.edu.ng',
                ],
                'google_chat_webhook_url' => null,
                'features_enabled' => [
                    'ai_assistant' => true,
                    'google_chat' => true,
                    'analytics' => true,
                    'resource_tracking' => true,
                ],
            ],
            [
                'name' => 'University of Ibadan',
                'code' => 'UI',
                'email' => 'research@ui.edu.ng',
                'department' => 'Academic Affairs',
                'phone' => '+234 (0)2 123-4567',
                'logo_url' => null,
                'branding_color' => '#004B87',
                'ai_model_config' => [
                    'primary' => 'gemini-pro',
                    'fallback' => 'gpt-4-turbo',
                    'timeout' => 30,
                ],
                'email_config' => [
                    'from_address' => 'research@ui.edu.ng',
                    'from_name' => 'UI Research Portal',
                    'reply_to' => 'support@ui.edu.ng',
                ],
                'google_chat_webhook_url' => null,
                'features_enabled' => [
                    'ai_assistant' => true,
                    'google_chat' => true,
                    'analytics' => true,
                    'resource_tracking' => true,
                ],
            ],
        ];

        foreach ($universities as $university) {
            University::updateOrCreate(
                ['code' => $university['code']],
                $university
            );
        }
    }
}
