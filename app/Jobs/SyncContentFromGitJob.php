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
        return [];
    }

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = [10, 30, 60]; // 10s, 30s, 60s between retries

    /**
     * Execute the job.
     */
    public function handle(GitSyncService $gitSync, ContentIndexer $indexer): void
    {
        $attemptNumber = $this->attempts();
        Log::info('Content sync started', [
            'delivery_id' => $this->deliveryId,
            'attempt' => $attemptNumber,
            'max_tries' => $this->tries,
        ]);

        try {
            $this->doHandle($gitSync, $indexer);
            Log::info('Content sync: completed successfully', [
                'delivery_id' => $this->deliveryId,
                'attempt' => $attemptNumber,
            ]);
        } catch (\Throwable $e) {
            Log::error('Content sync: failed', [
                'delivery_id' => $this->deliveryId,
                'attempt' => $attemptNumber,
                'error' => $e->getMessage(),
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
            ]);
            throw $e;
        }

        // Get the content path from the git repository
        $gitContentPath = $gitSync->getContentPath();
        Log::debug('Content sync: Got content path', ['path' => $gitContentPath, 'delivery_id' => $this->deliveryId]);

        // Verify the content path exists in the repository
        if (! is_dir($gitContentPath)) {
            $repoPath = config('git-sync.repo_storage_path');
            $actualContents = is_dir($repoPath) ? scandir($repoPath) : ['DIRECTORY_NOT_FOUND'];
            Log::error('Content sync: Content path does not exist in repository', [
                'expected_path' => $gitContentPath,
                'repo_path' => $repoPath,
                'repo_contents' => $actualContents,
                'delivery_id' => $this->deliveryId,
            ]);
            throw new \RuntimeException('Content path does not exist in repository: '.$gitContentPath);
        }

        // Create/update symlink from base_path('content/posts') to git content path
        $symlinkPath = base_path('content/posts');
        $absoluteTarget = realpath($gitContentPath);

        if (! $absoluteTarget) {
            Log::error('Content sync: Cannot resolve target path', [
                'target' => $gitContentPath,
                'delivery_id' => $this->deliveryId,
            ]);
            throw new \RuntimeException('Cannot resolve target path: '.$gitContentPath);
        }

        clearstatcache(true, $symlinkPath);

        // Manage posts symlink
        if (is_link($symlinkPath)) {
            $currentTarget = @readlink($symlinkPath);
            if (is_dir($currentTarget)) {
                Log::debug('Content sync: Symlink valid', ['symlink' => $symlinkPath, 'target' => $currentTarget]);
            } else {
                Log::warning('Content sync: Symlink exists but target invalid, recreating', [
                    'symlink' => $symlinkPath,
                    'current_target' => $currentTarget,
                    'new_target' => $absoluteTarget,
                ]);
                @unlink($symlinkPath);
                $this->createSymlink($absoluteTarget, $symlinkPath);
            }
        } else {
            $this->createSymlink($absoluteTarget, $symlinkPath);
        }

        // Create/update symlink for public images (storage/app/public/content/images → git images)
        $this->createImagesSymlink($gitContentPath);

        // Detect changed files
        $changedFiles = $indexer->detectChanges();
        Log::debug('Content sync: Changes detected', ['count' => $changedFiles->count(), 'delivery_id' => $this->deliveryId]);

        if ($changedFiles->isNotEmpty()) {
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
            Log::debug('Content sync: No changed files', ['delivery_id' => $this->deliveryId]);
        }

        // Delete posts that no longer exist in git
        try {
            $deletedCount = $indexer->deleteMissing();
            if ($deletedCount > 0) {
                Log::info('Content sync: Deleted missing posts', [
                    'delivery_id' => $this->deliveryId,
                    'deleted_count' => $deletedCount,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Content sync: deleteMissing failed', [
                'delivery_id' => $this->deliveryId,
                'error' => $e->getMessage(),
            ]);
        }

    }

    /**
     * Create/update symlink: storage/app/public/content/images → git repo images.
     * Mirrors the posts symlink approach so images are served directly at /storage/content/images/...
     */
    private function createImagesSymlink(string $gitContentPath): void
    {
        $gitImagesPath = dirname($gitContentPath).'/images';
        $symlinkPath = storage_path('app/public/content/images');

        if (! is_dir($gitImagesPath)) {
            Log::warning('Content sync: No images directory in git repo', [
                'delivery_id' => $this->deliveryId,
                'path' => $gitImagesPath,
            ]);

            return;
        }

        $absoluteTarget = realpath($gitImagesPath);

        if (! $absoluteTarget) {
            Log::error('Content sync: Cannot resolve images path', [
                'delivery_id' => $this->deliveryId,
                'path' => $gitImagesPath,
            ]);

            return;
        }

        // Ensure storage/app/public/content/ parent exists
        $parentDir = dirname($symlinkPath);
        if (! is_dir($parentDir)) {
            mkdir($parentDir, 0755, true);
        }

        clearstatcache(true, $symlinkPath);

        if (is_link($symlinkPath)) {
            $currentTarget = @readlink($symlinkPath);
            if ($currentTarget === $absoluteTarget) {
                Log::debug('Content sync: Images symlink already valid', [
                    'delivery_id' => $this->deliveryId,
                    'symlink' => $symlinkPath,
                    'target' => $absoluteTarget,
                ]);

                return;
            }
            Log::info('Content sync: Updating images symlink', [
                'delivery_id' => $this->deliveryId,
                'old_target' => $currentTarget,
                'new_target' => $absoluteTarget,
            ]);
            @unlink($symlinkPath);
        } elseif (is_dir($symlinkPath)) {
            // Real directory exists (leftover from old copy-based approach) — remove it
            Log::warning('Content sync: Replacing real images directory with symlink', [
                'delivery_id' => $this->deliveryId,
                'path' => $symlinkPath,
            ]);
            $this->removeDirectory($symlinkPath);
        }

        $this->createSymlink($absoluteTarget, $symlinkPath);
        Log::info('Content sync: Images symlink created', [
            'delivery_id' => $this->deliveryId,
            'symlink' => $symlinkPath,
            'target' => $absoluteTarget,
        ]);
    }

    /**
     * Recursively remove a directory and all its contents.
     */
    private function removeDirectory(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($path);
    }

    /**
     * Create a symlink with proper error handling.
     */
    private function createSymlink(string $target, string $link): void
    {
        $parentDir = dirname($link);
        if (! is_dir($parentDir)) {
            mkdir($parentDir, 0755, true);
        }

        if (! @symlink($target, $link)) {
            $error = error_get_last();
            throw new \RuntimeException('Failed to create symlink: '.($error['message'] ?? 'Unknown error'));
        }

        Log::info('Content symlink created', ['target' => $target, 'link' => $link]);
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

        $adminEmail = config('git-sync.admin_email', config('mail.from.address'));

        if ($adminEmail) {
            Notification::route('mail', $adminEmail)
                ->notify(new ContentSyncFailedNotification($exception, $this->deliveryId));
        }
    }
}
