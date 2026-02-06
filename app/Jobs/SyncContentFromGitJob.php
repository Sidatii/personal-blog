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
        return [
            (new ThrottlesExceptions(
                config('git-sync.job_max_exceptions', 3),
                config('git-sync.job_backoff_minutes', 5) * 60
            ))->backoff(config('git-sync.job_backoff_minutes', 5)),
        ];
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

        // Pull latest from git repository
        $gitSync->pullLatest();

        // Get the content path from the git repository
        $gitContentPath = $gitSync->getContentPath();

        // Create/update symlink from base_path('content/posts') to git content path
        $symlinkPath = base_path('content/posts');

        // Remove existing symlink if present
        if (is_link($symlinkPath)) {
            unlink($symlinkPath);
        }

        // Ensure parent directory exists
        $parentDir = dirname($symlinkPath);
        if (! is_dir($parentDir)) {
            mkdir($parentDir, 0755, true);
        }

        // Create symlink
        symlink($gitContentPath, $symlinkPath);

        Log::info('Content symlink created', [
            'delivery_id' => $this->deliveryId,
            'target' => $gitContentPath,
            'link' => $symlinkPath,
        ]);

        // Detect changed files
        $changedFiles = $indexer->detectChanges();

        if ($changedFiles->isNotEmpty()) {
            // Index each changed file
            $count = 0;
            foreach ($changedFiles as $filepath) {
                $indexer->indexFile($filepath);
                $count++;
            }

            Log::info('Content sync completed - changed files indexed', [
                'delivery_id' => $this->deliveryId,
                'files_processed' => $count,
            ]);
        } else {
            // No changed files, run full index as fallback (handles first sync after clone)
            $count = $indexer->indexAll();

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
