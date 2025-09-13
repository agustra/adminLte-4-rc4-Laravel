<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains security-related configuration options for the
    | application including rate limiting, file upload restrictions, etc.
    |
    */

    'rate_limiting' => [
        'api' => [
            'max_attempts' => 60,
            'decay_minutes' => 1,
        ],
        'login' => [
            'max_attempts' => 5,
            'decay_minutes' => 15,
        ],
        'password_reset' => [
            'max_attempts' => 3,
            'decay_minutes' => 60,
        ],
    ],

    'file_upload' => [
        'max_size' => 5 * 1024 * 1024, // 5MB
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'allowed_mime_types' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ],
    ],

    'password_policy' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_symbols' => true,
        'max_age_days' => 90,
    ],

    'session' => [
        'timeout_minutes' => 120,
        'secure_cookies' => env('SESSION_SECURE_COOKIE', true),
        'same_site' => 'strict',
    ],

    'headers' => [
        'hsts_max_age' => 31536000, // 1 year
        'content_security_policy' => "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:;",
    ],
];
