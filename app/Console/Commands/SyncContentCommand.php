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

        // Sync images
        $this->syncImages();

        $this->info("Content sync complete. {$count} posts indexed.");

        return Command::SUCCESS;
    }

    /**
     * Sync images from content/images to storage.
     */
    protected function syncImages(): void
    {
        $sourceDir = base_path('content/images');
        $targetDir = storage_path('app/public/content/images');

        if (! is_dir($sourceDir)) {
            $this->warn('No content/images directory found.');

            return;
        }

        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $synced = 0;
        foreach (glob("$sourceDir/*") as $file) {
            if (is_file($file)) {
                $filename = basename($file);
                copy($file, "$targetDir/$filename");
                $synced++;
            }
        }

        $this->info("Synced {$synced} images to public storage.");
    }
}
