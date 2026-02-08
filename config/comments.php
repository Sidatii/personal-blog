<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Comment System Configuration
    |--------------------------------------------------------------------------
    |
    | These settings control the behavior of the self-hosted comment system.
    |
    */

    /**
     * Automatically approve comments without moderation.
     * When false, comments require manual approval before appearing publicly.
     */
    'auto_approve' => env('COMMENTS_AUTO_APPROVE', false),

    /**
     * Maximum nesting depth for threaded comments.
     * Prevents runaway recursion in database queries.
     */
    'max_depth' => 5,

    /**
     * Maximum comment length in characters.
     */
    'max_length' => 5000,

    /**
     * Maximum number of comments allowed per hour from a single IP.
     * Helps prevent spam and abuse.
     */
    'throttle_per_hour' => 5,
];
