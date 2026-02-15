<?php

namespace App\Console\Commands;

use App\Services\Content\ContentIndexer;
use Illuminate\Console\Command;

class SyncContentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:sync {--force : Force re-index all files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync markdown content files to database posts';

    /**
     * Execute the console command.
     */
    public function handle(ContentIndexer $indexer): int
    {
        $this->info('Scanning content/posts/ directory...');

        // Ensure images symlink is always in place, regardless of post changes
        $this->createImagesSymlink();

        if ($this->option('force')) {
            $this->info('Force re-indexing all content...');
            $count = $indexer->rebuild();
        } else {
            $changed = $indexer->detectChanges();

            // If no posts exist yet, index all files
            if (\App\Models\Post::count() === 0) {
                $this->info('No posts found. Indexing all content...');
                $count = $indexer->indexAll();
            } elseif ($changed->isEmpty()) {
                $this->info('No changes detected.');

                return Command::SUCCESS;
            } else {
                $this->info("Found {$changed->count()} changed files...");
                $count = 0;
                foreach ($changed as $file) {
                    $indexer->indexFile($file);
                    $count++;
                    $this->line("Indexed: {$file}");
                }
            }
        }

        $this->info("Content sync complete. {$count} posts indexed.");

        return Command::SUCCESS;
    }

    /**
     * Create/update symlink: storage/app/public/content/images → git repo images.
     * Mirrors the posts symlink approach so no file copying is needed.
     */
    protected function createImagesSymlink(): void
    {
        $repoPath = config('git-sync.repo_storage_path');
        $contentPath = config('git-sync.content_path'); // e.g. 'content/posts'
        $gitImagesPath = $repoPath.'/'.dirname($contentPath).'/images';
        $symlinkPath = public_path('content/images');

        if (! is_dir($gitImagesPath)) {
            $this->warn('Images directory not found in git repo: '.$gitImagesPath);

            return;
        }

        $absoluteTarget = realpath($gitImagesPath);

        if (! $absoluteTarget) {
            $this->error('Cannot resolve images path: '.$gitImagesPath);

            return;
        }

        $parentDir = dirname($symlinkPath);
        if (! is_dir($parentDir)) {
            mkdir($parentDir, 0755, true);
        }

        clearstatcache(true, $symlinkPath);

        if (is_link($symlinkPath)) {
            if (@readlink($symlinkPath) === $absoluteTarget) {
                $this->line('Images symlink already valid.');

                return;
            }
            @unlink($symlinkPath);
        } elseif (is_dir($symlinkPath)) {
            $this->warn('Replacing real directory with symlink at: '.$symlinkPath);
            $this->removeDirectory($symlinkPath);
        }

        if (@symlink($absoluteTarget, $symlinkPath)) {
            $this->info("Images symlink created: {$symlinkPath} → {$absoluteTarget}");
        } else {
            $error = error_get_last();
            $this->error('Failed to create images symlink: '.($error['message'] ?? 'unknown error'));
        }
    }

    /**
     * Recursively remove a directory and all its contents.
     */
    protected function removeDirectory(string $path): void
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
}
