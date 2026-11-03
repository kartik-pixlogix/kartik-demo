<?php

return [
    /*
    |---------------------------------------------------------------------------
    | Posture-review form
    |---------------------------------------------------------------------------
    |
    | Spam defences for the public lead form. The honeypot and the minimum fill
    | time cost a visitor nothing; Turnstile is off by default and only worth
    | turning on if the first two stop being enough.
    |
    */

    // A live DNS lookup on the email domain. Worth it in production for lead
    // quality; disabled in the test suite so validation never depends on DNS.
    'validate_email_dns' => (bool) env('REVIEW_VALIDATE_EMAIL_DNS', true),

    // Seconds a human needs, at minimum, to read the block and fill four fields.
    'minimum_fill_seconds' => (int) env('REVIEW_MIN_FILL_SECONDS', 3),

    // Reject a render timestamp older than this: a replayed or scraped form.
    'maximum_form_age_seconds' => (int) env('REVIEW_MAX_FORM_AGE_SECONDS', 60 * 60 * 12),

    // Submissions per hour per IP.
    'rate_limit' => [
        'attempts' => (int) env('REVIEW_RATE_LIMIT', 5),
        'per_minutes' => (int) env('REVIEW_RATE_DECAY_MINUTES', 60),
    ],

    'turnstile' => [
        'enabled' => (bool) env('TURNSTILE_ENABLED', false),
        'site_key' => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
        'verify_url' => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
    ],

    // Where new leads are announced. The `admin_notification_email` setting wins
    // if the client has set one in the admin.
    'notification_email' => env('MAIL_ADMIN_ADDRESS'),

    // Shown on success, and swapped into the button label by the JS path.
    'success_message' => 'Request received. We reply within one business day',
];
