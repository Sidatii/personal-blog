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

        // If this is a retry, add delay to prevent hammering
        if ($attemptNumber > 1) {
            Log::warning('Content sync: Retry attempt', [
                'delivery_id' => $this->deliveryId,
                'attempt' => $attemptNumber,
            ]);
        }

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

            // Don't retry on certain errors
            if (str_contains($e->getMessage(), 'timed out')) {
                Log::error('Content sync: Lock timeout - not retrying', ['delivery_id' => $this->deliveryId]);
                $this->fail($e);

                return;
            }

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

        // Verify the content path exists in the repository
        if (! is_dir($gitContentPath)) {
            // Log the actual structure to help debug
            $repoPath = config('git-sync.repo_storage_path');
            $actualContents = is_dir($repoPath) ? scandir($repoPath) : ['DIRECTORY_NOT_FOUND'];
            Log::error('Content sync: Content path does not exist in repository', [
                'expected_path' => $gitContentPath,
                'repo_path' => $repoPath,
                'repo_contents' => $actualContents,
                'delivery_id' => $this->deliveryId,
            ]);
            throw new \RuntimeException('Content path does not exist in repository: '.$gitContentPath.'. Check your GIT_SYNC_CONTENT_PATH setting.');
        }

        Log::debug('Content sync: Content path verified', ['path' => $gitContentPath, 'delivery_id' => $this->deliveryId]);

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

        // Case 1: Symlink exists and points to valid target
        if (is_link($symlinkPath)) {
            $currentTarget = @readlink($symlinkPath);
            if (is_dir($currentTarget)) {
                Log::debug('Content sync: Symlink valid, continuing', [
                    'symlink' => $symlinkPath,
                    'target' => $currentTarget,
                    'delivery_id' => $this->deliveryId,
                ]);
            } else {
                // Symlink exists but target is invalid, recreate it
                Log::warning('Content sync: Symlink exists but target invalid, recreating', [
                    'symlink' => $symlinkPath,
                    'current_target' => $currentTarget,
                    'new_target' => $absoluteTarget,
                    'delivery_id' => $this->deliveryId,
                ]);
                @unlink($symlinkPath);
                $this->createSymlink($absoluteTarget, $symlinkPath);
            }
        } else {
            // Case 2: No symlink exists (first time)
            $this->createSymlink($absoluteTarget, $symlinkPath);
        }

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

        // Delete posts that no longer exist in git
        Log::debug('Content sync: Checking for deleted posts', ['delivery_id' => $this->deliveryId]);
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
            // Don't throw - this is cleanup, not critical
        }

        // Sync images from git to storage
        Log::debug('Content sync: Syncing images', ['delivery_id' => $this->deliveryId]);
        try {
            $this->syncImages($gitContentPath);
        } catch (\Throwable $e) {
            Log::error('Content sync: Image sync failed', [
                'delivery_id' => $this->deliveryId,
                'error' => $e->getMessage(),
            ]);
            // Don't throw - images are supplementary
        }
    }

    /**
     * Sync images from git repository to public storage.
     */
    private function syncImages(string $gitContentPath): void
    {
        $targetDir = storage_path('app/public/content/images');
        $syncedCount = 0;
        $deletedCount = 0;
        $sourceFiles = [];

        // Ensure target directory exists
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
            Log::info('Content sync: Created images directory', ['path' => $targetDir]);
        }

        // Find the git repo root from the content path
        $repoRoot = $this->findGitRepoRoot($gitContentPath);

        // Try multiple possible source locations
        $possibleSourceDirs = [
            $repoRoot.'/content/images',                          // Git repo content/images/ (repo root)
            dirname($gitContentPath).'/images',                     // Relative to posts dir
            base_path('content/images'),                            // Local content/images/ fallback
        ];

        Log::info('Content sync: Looking for images', [
            'delivery_id' => $this->deliveryId,
            'git_content_path' => $gitContentPath,
            'repo_root' => $repoRoot,
            'source_dirs' => $possibleSourceDirs,
        ]);

        // Find all images from all possible source directories
        foreach ($possibleSourceDirs as $sourceDir) {
            $exists = is_dir($sourceDir);
            Log::debug('Content sync: Checking images source', [
                'path' => $sourceDir,
                'exists' => $exists,
            ]);

            if ($exists) {
                $files = glob("$sourceDir/*");
                Log::debug('Content sync: Found files in source', [
                    'path' => $sourceDir,
                    'count' => count($files),
                ]);

                foreach ($files as $file) {
                    if (is_file($file) && basename($file) !== '.gitkeep') {
                        $filename = basename($file);
                        // Use most recent version if file exists in multiple sources
                        if (! isset($sourceFiles[$filename]) || filemtime($file) > filemtime($sourceFiles[$filename])) {
                            $sourceFiles[$filename] = $file;
                            Log::debug('Content sync: Found image file', [
                                'filename' => $filename,
                                'source' => $file,
                            ]);
                        }
                    }
                }
            }
        }

        Log::info('Content sync: Found images to sync', [
            'delivery_id' => $this->deliveryId,
            'count' => count($sourceFiles),
            'files' => array_keys($sourceFiles),
        ]);

        // Copy all found images to storage
        foreach ($sourceFiles as $filename => $sourceFile) {
            $targetFile = "$targetDir/$filename";

            // Only copy if file is new or changed
            if (! file_exists($targetFile) || filemtime($sourceFile) > filemtime($targetFile)) {
                if (copy($sourceFile, $targetFile)) {
                    $syncedCount++;
                    Log::info('Content sync: Copied image', [
                        'delivery_id' => $this->deliveryId,
                        'file' => $filename,
                        'from' => $sourceFile,
                        'to' => $targetFile,
                    ]);
                } else {
                    Log::error('Content sync: Failed to copy image', [
                        'delivery_id' => $this->deliveryId,
                        'file' => $filename,
                        'source' => $sourceFile,
                        'target' => $targetFile,
                        'error' => error_get_last(),
                    ]);
                }
            }
        }

        // Remove images from storage that don't exist in any source
        foreach (glob("$targetDir/*") as $file) {
            $filename = basename($file);
            if ($filename === '.gitkeep') {
                continue;
            }
            if (! isset($sourceFiles[$filename])) {
                if (unlink($file)) {
                    $deletedCount++;
                    Log::info('Content sync: Deleted orphaned image', [
                        'delivery_id' => $this->deliveryId,
                        'file' => $filename,
                    ]);
                }
            }
        }

        Log::info('Content sync: Images sync complete', [
            'delivery_id' => $this->deliveryId,
            'synced' => $syncedCount,
            'deleted' => $deletedCount,
            'source_dirs_checked' => count(array_filter($possibleSourceDirs, 'is_dir')),
            'target_dir' => $targetDir,
        ]);
    }

    /**
     * Find the git repository root from a content path.
     */
    private function findGitRepoRoot(string $gitContentPath): string
    {
        // Start from the content path and go up until we find .git directory
        $currentDir = $gitContentPath;
        $maxDepth = 10; // Prevent infinite loop
        $depth = 0;

        while ($depth < $maxDepth) {
            if (is_dir($currentDir.'/.git')) {
                return $currentDir;
            }

            $parentDir = dirname($currentDir);
            if ($parentDir === $currentDir) {
                // Reached root
                break;
            }
            $currentDir = $parentDir;
            $depth++;
        }

        // Fallback: assume content is 2 levels deep (repo/content/posts)
        return dirname(dirname($gitContentPath));
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

        Log::info('Content symlink created', [
            'target' => $target,
            'link' => $link,
        ]);
    }
}
