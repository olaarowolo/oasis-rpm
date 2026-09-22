<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Universities Configuration
    |--------------------------------------------------------------------------
    | Configuration for multi-tenant university support
    |
    */

    'default' => env('DEFAULT_UNIVERSITY_CODE', 'LASU'),

    /*
    |--------------------------------------------------------------------------
    | University Presets
    |--------------------------------------------------------------------------
    | Pre-configured universities with their specific settings
    |
    */
    'presets' => [
        'LASU' => [
            'name' => 'Lagos State University',
            'code' => 'LASU',
            'email' => 'research@lasu.edu.ng',
            'department' => 'Journalism and Media Studies',
            'phone' => '+234 (0)1 123-4567',
            'branding_color' => '#003366',
            'logo_url' => '/logos/lasu-logo.png',
            'ai_model_config' => [
                'primary' => 'gemini-pro',
                'fallback' => 'gpt-4-turbo',
            ],
            'email_config' => [
                'from_address' => 'research@lasu.edu.ng',
                'from_name' => 'LASU Research Portal',
                'reply_to' => 'support@lasu.edu.ng',
            ],
            'features_enabled' => [
                'ai_assistant' => true,
                'google_chat' => true,
                'analytics' => true,
                'resource_tracking' => true,
            ],
        ],
        'UI' => [
            'name' => 'University of Ibadan',
            'code' => 'UI',
            'email' => 'research@ui.edu.ng',
            'department' => 'Academic Affairs',
            'phone' => '+234 (0)2 123-4567',
            'branding_color' => '#004B87',
            'logo_url' => '/logos/ui-logo.png',
            'ai_model_config' => [
                'primary' => 'gemini-pro',
                'fallback' => 'gpt-4-turbo',
            ],
            'email_config' => [
                'from_address' => 'research@ui.edu.ng',
                'from_name' => 'UI Research Portal',
                'reply_to' => 'support@ui.edu.ng',
            ],
            'features_enabled' => [
                'ai_assistant' => true,
                'google_chat' => true,
                'analytics' => true,
                'resource_tracking' => true,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    | Global feature flags affecting all universities
    |
    */
    'features' => [
        'api_enabled' => true,
        'web_enabled' => true,
        'mobile_enabled' => false,
        'maintenance_mode' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Limits
    |--------------------------------------------------------------------------
    | Limits for resources per university
    |
    */
    'limits' => [
        'max_students_per_supervisor' => 50,
        'max_proposals_per_student' => 1,
        'max_file_upload_mb' => 50,
        'max_meeting_logs_per_student' => 100,
        'max_resources_per_stage' => 20,
    ],

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    | Default configuration values
    |
    */
    'defaults' => [
        'timezone' => 'Africa/Lagos',
        'locale' => 'en_NG',
        'date_format' => 'Y-m-d',
        'time_format' => 'H:i:s',
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Retention
    |--------------------------------------------------------------------------
    | Data retention policies
    |
    */
    'retention' => [
        'audit_logs_days' => 365,
        'deleted_records_days' => 90,
        'cache_ttl_hours' => 24,
    ],
];
