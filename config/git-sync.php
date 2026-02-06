<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Git Repository URL
    |--------------------------------------------------------------------------
    |
    | The URL of the git repository containing markdown content files.
    | Can be SSH (git@github.com:user/repo.git) or HTTPS.
    | Required for production deployment.
    |
    */
    'repository_url' => env('GIT_SYNC_REPOSITORY_URL'),

    /*
    |--------------------------------------------------------------------------
    | Branch to Sync
    |--------------------------------------------------------------------------
    |
    | The branch to pull from. Typically 'main' for production.
    | Change for staging environments or feature branch testing.
    |
    */
    'branch' => env('GIT_SYNC_BRANCH', 'main'),

    /*
    |--------------------------------------------------------------------------
    | Content Path Within Repository
    |--------------------------------------------------------------------------
    |
    | Subdirectory within the repository that contains markdown files.
    | Relative to repository root.
    |
    */
    'content_path' => env('GIT_SYNC_CONTENT_PATH', 'content/posts'),

    /*
    |--------------------------------------------------------------------------
    | Local Repository Storage Path
    |--------------------------------------------------------------------------
    |
    | Where the git clone will be stored locally.
    | Should be in storage/app to ensure it persists across deployments.
    |
    */
    'repo_storage_path' => storage_path('app/content-repo'),

    /*
    |--------------------------------------------------------------------------
    | Lock File Path
    |--------------------------------------------------------------------------
    |
    | Path to the file used for flock() advisory locking.
    | Prevents concurrent git operations.
    |
    */
    'lock_file' => storage_path('app/git-sync.lock'),

    /*
    |--------------------------------------------------------------------------
    | GitHub Webhook Secret
    |--------------------------------------------------------------------------
    |
    | Shared secret for HMAC signature verification of GitHub webhooks.
    | Used to verify webhook authenticity using X-Hub-Signature-256.
    |
    */
    'webhook_secret' => env('GITHUB_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Sync Branch (Alias)
    |--------------------------------------------------------------------------
    |
    | Alias for branch filtering in webhook controller.
    | Ensures webhooks only trigger sync for the configured branch.
    |
    */
    'sync_branch' => env('GIT_SYNC_BRANCH', 'main'),

];
