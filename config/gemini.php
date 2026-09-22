<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Gemini AI Configuration
    |--------------------------------------------------------------------------
    | Google Gemini AI integration settings for research assistance
    |
    */

    'api_key' => env('GEMINI_API_KEY'),
    'enabled' => env('GEMINI_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Models Configuration
    |--------------------------------------------------------------------------
    | Primary and fallback AI models
    |
    */
    'models' => [
        'primary' => 'gemini-pro',
        'fallback' => 'gpt-4-turbo',
    ],

    /*
    |--------------------------------------------------------------------------
    | Request Parameters
    |--------------------------------------------------------------------------
    | Parameters for API requests
    |
    */
    'parameters' => [
        'temperature' => env('GEMINI_TEMPERATURE', 0.7),
        'max_tokens' => env('GEMINI_MAX_TOKENS', 2048),
        'timeout' => env('GEMINI_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    | Rate limits for API usage
    |
    */
    'rate_limits' => [
        'requests_per_minute' => 60,
        'requests_per_day' => 1000,
    ],

    /*
    |--------------------------------------------------------------------------
    | System Prompts
    |--------------------------------------------------------------------------
    | Custom prompts for different query types
    |
    */
    'prompts' => [
        'research_assistant' => 'You are an expert research advisor. Help the student with research questions, providing clear, actionable guidance.',
        'methodology_guide' => 'You are a research methodology expert. Help refine research methods, approach, and design.',
        'literature_assistant' => 'You are a literature review expert. Help identify relevant sources and synthesize literature.',
        'writing_coach' => 'You are an academic writing coach. Provide feedback on clarity, structure, and academic tone.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    | Optional features and integrations
    |
    */
    'features' => [
        'caching' => true,
        'history_tracking' => true,
        'feedback_collection' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Response Handling
    |--------------------------------------------------------------------------
    | How to handle API responses
    |
    */
    'response' => [
        'include_confidence_score' => false,
        'include_sources' => true,
        'format_as_html' => false,
    ],
];
