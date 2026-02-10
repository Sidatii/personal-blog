<?php

namespace App\Services\Content;

use Illuminate\Support\Facades\Log;
use RuntimeException;
use Symfony\Component\Process\Process;

class GitSyncService
{
    /**
     * Lock file handle. Stored as class property to prevent garbage collection
     * from releasing the lock prematurely (see RESEARCH.md Pitfall 1).
     *
     * @var resource|null
     */
    private $lockHandle = null;

    /**
     * Pull latest changes from git repository.
     * Uses flock() for exclusive locking to prevent concurrent operations.
     *
     * @throws RuntimeException
     */
    public function pullLatest(): void
    {
        $lockFile = config('git-sync.lock_file');
        Log::debug('GitSync: Attempting to acquire lock', ['lock_file' => $lockFile]);

        // Ensure parent directory exists for lock file
        $lockDir = dirname($lockFile);
        if (! is_dir($lockDir)) {
            Log::debug('GitSync: Creating lock directory', ['directory' => $lockDir]);
            if (! mkdir($lockDir, 0755, true)) {
                throw new RuntimeException('Failed to create lock directory: '.$lockDir);
            }
        }

        // Open lock file (create if not exists, don't truncate)
        $this->lockHandle = fopen($lockFile, 'c');

        if ($this->lockHandle === false) {
            $error = error_get_last();
            Log::error('GitSync: Failed to open lock file', [
                'lock_file' => $lockFile,
                'error' => $error['message'] ?? 'Unknown error',
            ]);
            throw new RuntimeException('Failed to open git sync lock file: '.$lockFile);
        }

        Log::debug('GitSync: Lock file opened');

        // Attempt non-blocking exclusive lock
        $wouldBlock = false;
        Log::debug('GitSync: Attempting flock');
        if (! flock($this->lockHandle, LOCK_EX | LOCK_NB, $wouldBlock)) {
            fclose($this->lockHandle);
            $this->lockHandle = null;

            if ($wouldBlock) {
                throw new RuntimeException('Another sync operation is in progress');
            }

            throw new RuntimeException('Failed to acquire git sync lock');
        }

        Log::debug('GitSync: Lock acquired');

        try {
            $repoPath = config('git-sync.repo_storage_path');
            Log::debug('GitSync: Checking repository', ['repo_path' => $repoPath]);

            // Check if repository already exists
            if (! is_dir($repoPath.'/.git')) {
                Log::debug('GitSync: Repository not found, cloning');
                $this->cloneRepository();
            } else {
                Log::debug('GitSync: Repository exists, pulling');
                $this->pullRepository();
            }

            Log::debug('GitSync: Git operation completed');
        } finally {
            // Always release lock and close handle
            Log::debug('GitSync: Entering finally block');
            if ($this->lockHandle) {
                Log::debug('GitSync: Releasing lock');
                $unlockResult = @flock($this->lockHandle, LOCK_UN);
                Log::debug('GitSync: Lock released', ['result' => $unlockResult]);

                Log::debug('GitSync: Closing handle');
                $closeResult = @fclose($this->lockHandle);
                Log::debug('GitSync: Handle closed', ['result' => $closeResult]);
                $this->lockHandle = null;
            } else {
                Log::debug('GitSync: No lock handle to release');
            }
            Log::debug('GitSync: Finally block complete');
        }

        Log::debug('GitSync: pullLatest method complete');
    }

    /**
     * Clone repository on first run.
     *
     * @throws RuntimeException
     */
    private function cloneRepository(): void
    {
        $repositoryUrl = config('git-sync.repository_url');
        $branch = config('git-sync.branch');
        $path = config('git-sync.repo_storage_path');

        if (empty($repositoryUrl)) {
            throw new RuntimeException('GIT_SYNC_REPOSITORY_URL not configured');
        }

        // Ensure parent directory exists
        $parentDir = dirname($path);
        if (! is_dir($parentDir)) {
            mkdir($parentDir, 0755, true);
        }

        // Build git clone command with escapeshellarg for security
        $process = new Process([
            'git',
            'clone',
            '--branch',
            $branch,
            '--single-branch',
            $repositoryUrl,
            $path,
        ]);

        $process->setTimeout(300); // 5 minutes timeout
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException(
                'Git clone failed: '.$process->getErrorOutput()
            );
        }

        Log::info('Repository cloned', [
            'path' => $path,
            'branch' => $branch,
            'output' => $process->getOutput(),
        ]);
    }

    /**
     * Pull updates from repository.
     *
     * @throws RuntimeException
     */
    private function pullRepository(): void
    {
        $repoPath = config('git-sync.repo_storage_path');
        $branch = config('git-sync.branch');

        // Fetch latest changes
        $fetchProcess = new Process(['git', 'fetch', 'origin'], $repoPath);
        $fetchProcess->setTimeout(300);
        $fetchProcess->run();

        if (! $fetchProcess->isSuccessful()) {
            throw new RuntimeException(
                'Git fetch failed: '.$fetchProcess->getErrorOutput()
            );
        }

        // Hard reset to origin/branch (ensures clean state even with local changes)
        $resetProcess = new Process(
            ['git', 'reset', '--hard', "origin/{$branch}"],
            $repoPath
        );
        $resetProcess->setTimeout(60);
        $resetProcess->run();

        if (! $resetProcess->isSuccessful()) {
            throw new RuntimeException(
                'Git reset failed: '.$resetProcess->getErrorOutput()
            );
        }

        $output = $resetProcess->getOutput();
        Log::info('Repository updated', [
            'branch' => $branch,
            'output_length' => strlen($output),
            'output' => substr($output, 0, 1000), // Limit output size
        ]);
    }

    /**
     * Get the full path to content files within the repository.
     */
    public function getContentPath(): string
    {
        Log::debug('GitSync: getContentPath called');
        $repoPath = config('git-sync.repo_storage_path');
        $contentPath = config('git-sync.content_path');

        Log::debug('GitSync: getContentPath config values', [
            'repo_path' => $repoPath,
            'content_path' => $contentPath,
        ]);

        if (empty($repoPath)) {
            throw new RuntimeException('git-sync.repo_storage_path config is empty');
        }

        $fullPath = $repoPath.'/'.$contentPath;
        Log::debug('GitSync: getContentPath returning', ['path' => $fullPath]);

        return $fullPath;
    }
}
