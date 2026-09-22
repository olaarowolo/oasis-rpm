<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Email Configuration
    |--------------------------------------------------------------------------
    | Email settings for notifications and communications
    |
    */

    'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@research.edu'),
    'from_name' => env('MAIL_FROM_NAME', 'Research Supervision Portal'),
    'reply_to' => env('MAIL_REPLY_TO_ADDRESS', 'support@research.edu'),

    /*
    |--------------------------------------------------------------------------
    | Email Templates
    |--------------------------------------------------------------------------
    | Configuration for different email notification types
    |
    */
    'templates' => [
        'student_welcome' => [
            'subject' => 'Welcome to Research Supervision Portal',
            'description' => 'Sent when student account is created',
        ],
        'supervisor_welcome' => [
            'subject' => 'Welcome to Research Supervision Portal',
            'description' => 'Sent when supervisor account is created',
        ],
        'proposal_submitted' => [
            'subject' => 'Research Proposal Submitted - Awaiting Review',
            'description' => 'Sent when student submits a proposal',
        ],
        'proposal_approved' => [
            'subject' => 'Research Proposal Approved!',
            'description' => 'Sent when supervisor approves a proposal',
        ],
        'proposal_revision_required' => [
            'subject' => 'Research Proposal - Revision Required',
            'description' => 'Sent when supervisor requests revision',
        ],
        'meeting_scheduled' => [
            'subject' => 'Supervision Meeting Scheduled',
            'description' => 'Sent when meeting is scheduled',
        ],
        'meeting_reminder' => [
            'subject' => 'Reminder: Supervision Meeting Tomorrow',
            'description' => 'Sent 24 hours before meeting',
        ],
        'meeting_submitted' => [
            'subject' => 'Meeting Log Submitted - Awaiting Approval',
            'description' => 'Sent when meeting log is submitted',
        ],
        'meeting_approved' => [
            'subject' => 'Meeting Log Approved',
            'description' => 'Sent when meeting log is approved by supervisor',
        ],
        'stage_advanced' => [
            'subject' => 'Research Progress: Stage Advanced',
            'description' => 'Sent when student advances to next stage',
        ],
        'resource_assigned' => [
            'subject' => 'New Learning Resource Available',
            'description' => 'Sent when resource is assigned',
        ],
        'resource_completed' => [
            'subject' => 'Resource Completion Acknowledged',
            'description' => 'Sent when resource is marked complete',
        ],
        'ai_response' => [
            'subject' => 'AI Assistant Response to Your Query',
            'description' => 'Sent as response to AI query',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SendGrid Configuration
    |--------------------------------------------------------------------------
    | SendGrid email service integration settings
    |
    */
    'sendgrid' => [
        'api_key' => env('SENDGRID_API_KEY'),
        'enabled' => env('SENDGRID_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Queue Settings
    |--------------------------------------------------------------------------
    | Whether to queue emails for async sending
    |
    */
    'queue_emails' => env('QUEUE_EMAILS', true),
    'queue_name' => env('MAIL_QUEUE_NAME', 'emails'),

    /*
    |--------------------------------------------------------------------------
    | Email Retry Settings
    |--------------------------------------------------------------------------
    | Retry settings for failed email sends
    |
    */
    'max_retries' => 3,
    'retry_delay_minutes' => 5,
];
