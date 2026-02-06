<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the XML sitemap for the blog';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Generating sitemap...');

        $sitemap = Sitemap::create();

        // Add homepage with daily change frequency
        $sitemap->add(
            Url::create('/')
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0)
        );

        // Add blog index with weekly change frequency
        $sitemap->add(
            Url::create('/blog')
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.9)
        );

        // Add all published posts with priority based on recency
        $posts = Post::published()
            ->orderBy('published_at', 'desc')
            ->get();

        $totalPosts = $posts->count();
        $index = 0;

        foreach ($posts as $post) {
            // Calculate priority based on recency (newer posts get higher priority)
            // Priority ranges from 0.5 to 0.8
            $priority = max(0.5, 0.8 - ($index / max($totalPosts, 1)) * 0.3);

            $sitemap->add(
                Url::create(route('posts.show', $post))
                    ->setLastModificationDate($post->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority($priority)
            );

            $index++;
        }

        // Write sitemap to public directory
        $sitemapPath = public_path('sitemap.xml');
        $sitemap->writeToFile($sitemapPath);

        $this->info("Sitemap generated successfully at: {$sitemapPath}");
        $this->info('Total URLs: '.($totalPosts + 2)); // +2 for homepage and blog index

        return self::SUCCESS;
    }
}
