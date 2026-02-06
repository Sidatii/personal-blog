<?php

namespace App\Services\Content;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use RuntimeException;

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
     * @return void
     * @throws RuntimeException
     */
    public function pullLatest(): void
    {
        $lockFile = config('git-sync.lock_file');

        // Open lock file (create if not exists, don't truncate)
        $this->lockHandle = fopen($lockFile, 'c');

        if ($this->lockHandle === false) {
            throw new RuntimeException('Failed to open git sync lock file');
        }

        // Attempt non-blocking exclusive lock
        $wouldBlock = false;
        if (!flock($this->lockHandle, LOCK_EX | LOCK_NB, $wouldBlock)) {
            fclose($this->lockHandle);
            $this->lockHandle = null;

            if ($wouldBlock) {
                throw new RuntimeException('Another sync operation is in progress');
            }

            throw new RuntimeException('Failed to acquire git sync lock');
        }

        try {
            $repoPath = config('git-sync.repo_storage_path');

            // Check if repository already exists
            if (!is_dir($repoPath . '/.git')) {
                $this->cloneRepository();
            } else {
                $this->pullRepository();
            }
        } finally {
            // Always release lock and close handle
            if ($this->lockHandle) {
                flock($this->lockHandle, LOCK_UN);
                fclose($this->lockHandle);
                $this->lockHandle = null;
            }
        }
    }

    /**
     * Clone repository on first run.
     *
     * @return void
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
        if (!is_dir($parentDir)) {
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

        if (!$process->isSuccessful()) {
            throw new RuntimeException(
                'Git clone failed: ' . $process->getErrorOutput()
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
     * @return void
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

        if (!$fetchProcess->isSuccessful()) {
            throw new RuntimeException(
                'Git fetch failed: ' . $fetchProcess->getErrorOutput()
            );
        }

        // Hard reset to origin/branch (ensures clean state even with local changes)
        $resetProcess = new Process(
            ['git', 'reset', '--hard', "origin/{$branch}"],
            $repoPath
        );
        $resetProcess->setTimeout(60);
        $resetProcess->run();

        if (!$resetProcess->isSuccessful()) {
            throw new RuntimeException(
                'Git reset failed: ' . $resetProcess->getErrorOutput()
            );
        }

        Log::info('Repository updated', [
            'branch' => $branch,
            'output' => $resetProcess->getOutput(),
        ]);
    }

    /**
     * Get the full path to content files within the repository.
     *
     * @return string
     */
    public function getContentPath(): string
    {
        $repoPath = config('git-sync.repo_storage_path');
        $contentPath = config('git-sync.content_path');

        return $repoPath . '/' . $contentPath;
    }
}
