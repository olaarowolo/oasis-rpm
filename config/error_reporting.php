<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return [
    /*
    |--------------------------------------------------------------------------
    | Error Alerting
    |--------------------------------------------------------------------------
    |
    | When an exception is reported, the application emails a diagnostic
    | report to the operations mailbox. The report is best effort: if the
    | mail server is unavailable the failure is only written to the log,
    | so a broken mail transport can never mask the original error.
    |
    */

    'enabled' => env('ERROR_ALERT_ENABLED', true),

    'to' => env('ERROR_ALERT_TO', 'tech@olaarowolo.com'),

    'from_address' => env('ERROR_ALERT_FROM', env('MAIL_FROM_ADDRESS', 'noreply@research.edu.ng')),

    'environments' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('ERROR_ALERT_ENVIRONMENTS', 'production'))
    ))),

    'throttle_minutes' => (int) env('ERROR_ALERT_THROTTLE_MINUTES', 10),

    'max_per_hour' => (int) env('ERROR_ALERT_MAX_PER_HOUR', 10),

    'include_request' => env('ERROR_ALERT_INCLUDE_REQUEST', true),

    'max_trace_lines' => (int) env('ERROR_ALERT_TRACE_LINES', 25),

    'max_value_length' => (int) env('ERROR_ALERT_MAX_VALUE_LENGTH', 500),

    'redact_keys' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('ERROR_ALERT_REDACT_KEYS', implode(',', [
            'password',
            'password_confirmation',
            'current_password',
            'new_password',
            'token',
            'api_key',
            'secret',
            'otp',
            'pin',
            'passphrase',
            'authorization',
            'access_token',
            'remember_token',
            '_token',
        ])))
    ))),

    /*
    |--------------------------------------------------------------------------
    | Suppressed Exceptions
    |--------------------------------------------------------------------------
    |
    | Laravel does not report validation, authentication and 404 failures.
    | The same classes are listed here so a code path that reports them
    | directly cannot flood the mailbox with noise from crawlers and stale
    | bookmarks.
    |
    */

    'ignore_exceptions' => [
        ValidationException::class,
        AuthenticationException::class,
        AuthorizationException::class,
        HttpResponseException::class,
        NotFoundHttpException::class,
        HttpException::class,
    ],

    'include_headers' => ['user-agent', 'referer', 'accept', 'x-requested-with'],

    'cache_store' => env('ERROR_ALERT_CACHE_STORE'),

    /*
    |--------------------------------------------------------------------------
    | User Facing Error Page
    |--------------------------------------------------------------------------
    |
    | Controls the branded error page shown to signed in and guest users
    | alike. The page never exposes an exception class, file path or stack
    | trace; the user only sees a reference code that also appears in the
    | alert email so support can trace it.
    |
    */

    'page' => [
        'countdown_seconds' => (int) env('ERROR_PAGE_COUNTDOWN', 10),
        'loop_guard_seconds' => (int) env('ERROR_PAGE_LOOP_GUARD', 60),
        'support_email' => env('ERROR_PAGE_SUPPORT_EMAIL', 'tech@olaarowolo.com'),
        'context_cache_seconds' => (int) env('ERROR_PAGE_CONTEXT_TTL', 600),
    ],
];
