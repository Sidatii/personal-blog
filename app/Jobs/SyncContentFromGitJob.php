<?php

namespace App\Jobs;

use App\Notifications\ContentSyncFailedNotification;
use App\Services\Content\ContentIndexer;
use App\Services\Content\GitSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class SyncContentFromGitJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The GitHub delivery ID for unique job tracking.
     */
    public string $deliveryId;

    /**
     * The number of seconds the job will be unique.
     */
    public int $uniqueFor = 3600;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 300;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $deliveryId)
    {
        $this->deliveryId = $deliveryId;
    }

    /**
     * Get the unique ID for this job.
     */
    public function uniqueId(): string
    {
        return $this->deliveryId;
    }

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array
    {
        // Temporarily disabled for debugging
        Log::debug('Content sync: middleware called');

        return [];
        /*
        return [
            (new ThrottlesExceptions(
                config('git-sync.job_max_exceptions', 3),
                config('git-sync.job_backoff_minutes', 5) * 60
            ))->backoff(config('git-sync.job_backoff_minutes', 5)),
        ];
        */
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(30);
    }

    /**
     * Execute the job.
     */
    public function handle(GitSyncService $gitSync, ContentIndexer $indexer): void
    {
        Log::info('Content sync started', ['delivery_id' => $this->deliveryId]);

        // Register shutdown handler to catch fatal errors
        $deliveryId = $this->deliveryId;
        register_shutdown_function(function () use ($deliveryId) {
            $error = error_get_last();
            if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
                Log::emergency('Content sync: FATAL ERROR', [
                    'delivery_id' => $deliveryId,
                    'error' => $error['message'],
                    'file' => $error['file'],
                    'line' => $error['line'],
                ]);
            }
        });

        try {
            $this->doHandle($gitSync, $indexer);
            Log::info('Content sync: doHandle completed successfully', ['delivery_id' => $this->deliveryId]);
        } catch (\Throwable $e) {
            Log::error('Content sync: Uncaught exception in handle', [
                'delivery_id' => $this->deliveryId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    private function doHandle(GitSyncService $gitSync, ContentIndexer $indexer): void
    {
        try {
            // Pull latest from git repository
            Log::debug('Content sync: Calling pullLatest', ['delivery_id' => $this->deliveryId]);
            $gitSync->pullLatest();
            Log::debug('Content sync: pullLatest completed', ['delivery_id' => $this->deliveryId]);
        } catch (\Throwable $e) {
            Log::error('Content sync: pullLatest failed', [
                'delivery_id' => $this->deliveryId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        // Get the content path from the git repository
        Log::debug('Content sync: Getting content path', ['delivery_id' => $this->deliveryId]);
        $gitContentPath = $gitSync->getContentPath();
        Log::debug('Content sync: Got content path', ['path' => $gitContentPath, 'delivery_id' => $this->deliveryId]);

        // Create/update symlink from base_path('content/posts') to git content path
        $symlinkPath = base_path('content/posts');
        Log::debug('Content sync: Symlink path', ['symlink_path' => $symlinkPath, 'delivery_id' => $this->deliveryId]);

        // Remove existing symlink if present
        if (is_link($symlinkPath)) {
            Log::debug('Content sync: Removing existing symlink', ['delivery_id' => $this->deliveryId]);
            $unlinkResult = unlink($symlinkPath);
            Log::debug('Content sync: Symlink removed', ['result' => $unlinkResult, 'delivery_id' => $this->deliveryId]);
        }

        // Ensure parent directory exists
        $parentDir = dirname($symlinkPath);
        Log::debug('Content sync: Checking parent dir', ['parent_dir' => $parentDir, 'exists' => is_dir($parentDir), 'delivery_id' => $this->deliveryId]);
        if (! is_dir($parentDir)) {
            Log::debug('Content sync: Creating parent dir', ['delivery_id' => $this->deliveryId]);
            $mkdirResult = mkdir($parentDir, 0755, true);
            Log::debug('Content sync: Parent dir created', ['result' => $mkdirResult, 'delivery_id' => $this->deliveryId]);
        }

        // Create symlink
        Log::debug('Content sync: Creating symlink', ['target' => $gitContentPath, 'link' => $symlinkPath, 'delivery_id' => $this->deliveryId]);
        $symlinkResult = @symlink($gitContentPath, $symlinkPath);
        if (! $symlinkResult) {
            $error = error_get_last();
            Log::error('Content sync: Symlink failed', [
                'error' => $error['message'] ?? 'Unknown error',
                'target' => $gitContentPath,
                'link' => $symlinkPath,
                'target_exists' => is_dir($gitContentPath),
                'link_parent_writable' => is_writable($parentDir),
                'delivery_id' => $this->deliveryId,
            ]);
            throw new \RuntimeException('Failed to create symlink: '.($error['message'] ?? 'Unknown error'));
        }

        Log::info('Content symlink created', [
            'delivery_id' => $this->deliveryId,
            'target' => $gitContentPath,
            'link' => $symlinkPath,
        ]);

        // Detect changed files
        Log::debug('Content sync: Detecting changes', ['delivery_id' => $this->deliveryId]);
        try {
            $changedFiles = $indexer->detectChanges();
            Log::debug('Content sync: Changes detected', ['count' => $changedFiles->count(), 'delivery_id' => $this->deliveryId]);
        } catch (\Throwable $e) {
            Log::error('Content sync: detectChanges failed', [
                'delivery_id' => $this->deliveryId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        if ($changedFiles->isNotEmpty()) {
            // Index each changed file
            $count = 0;
            foreach ($changedFiles as $filepath) {
                Log::debug('Content sync: Indexing file', ['filepath' => $filepath, 'delivery_id' => $this->deliveryId]);
                try {
                    $indexer->indexFile($filepath);
                    $count++;
                } catch (\Throwable $e) {
                    Log::error('Content sync: indexFile failed', [
                        'filepath' => $filepath,
                        'delivery_id' => $this->deliveryId,
                        'error' => $e->getMessage(),
                    ]);
                    throw $e;
                }
            }

            Log::info('Content sync completed - changed files indexed', [
                'delivery_id' => $this->deliveryId,
                'files_processed' => $count,
            ]);
        } else {
            // No changed files, run full index as fallback (handles first sync after clone)
            Log::debug('Content sync: Running full index', ['delivery_id' => $this->deliveryId]);
            try {
                $count = $indexer->indexAll();
            } catch (\Throwable $e) {
                Log::error('Content sync: indexAll failed', [
                    'delivery_id' => $this->deliveryId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }

            Log::info('Content sync completed - full index', [
                'delivery_id' => $this->deliveryId,
                'files_processed' => $count,
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::error('Content sync job failed permanently', [
            'delivery_id' => $this->deliveryId,
            'error' => $exception->getMessage(),
        ]);

        // Send failure notification
        $adminEmail = config('git-sync.admin_email', config('mail.from.address'));

        if ($adminEmail) {
            Notification::route('mail', $adminEmail)
                ->notify(new ContentSyncFailedNotification($exception, $this->deliveryId));
        }
    }
}
