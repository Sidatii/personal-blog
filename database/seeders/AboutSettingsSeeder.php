<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class AboutSettingsSeeder extends Seeder
{
    /**
     * Seed about.* settings from config/portfolio.php.
     * Uses firstOrCreate so existing admin-entered values are preserved.
     */
    public function run(): void
    {
        $portfolio = config('portfolio');

        // Name
        Setting::firstOrCreate(
            ['key' => 'about.name'],
            ['value' => $portfolio['name'] ?? '']
        );

        // Headline — config uses 'title'
        Setting::firstOrCreate(
            ['key' => 'about.headline'],
            ['value' => $portfolio['title'] ?? '']
        );

        // Bio — config uses bio.intro + bio.extended
        $bio = '';
        if (isset($portfolio['bio'])) {
            if (is_array($portfolio['bio'])) {
                $parts = array_filter([
                    $portfolio['bio']['intro'] ?? '',
                    $portfolio['bio']['extended'] ?? '',
                ]);
                $bio = implode("\n\n", $parts);
            } else {
                $bio = (string) $portfolio['bio'];
            }
        }
        Setting::firstOrCreate(
            ['key' => 'about.bio'],
            ['value' => $bio]
        );

        // Profile photo — config uses 'photo'
        Setting::firstOrCreate(
            ['key' => 'about.profile_photo'],
            ['value' => $portfolio['photo'] ?? '']
        );

        // Skills — config uses 'tech_stack' (nested categories with name/note)
        // Flatten to a simple comma-separated list of skill names
        $skills = [];
        if (isset($portfolio['tech_stack']) && is_array($portfolio['tech_stack'])) {
            foreach ($portfolio['tech_stack'] as $category => $items) {
                if (is_array($items)) {
                    foreach ($items as $item) {
                        if (isset($item['name'])) {
                            $skills[] = $item['name'];
                        } elseif (is_string($item)) {
                            $skills[] = $item;
                        }
                    }
                }
            }
        }
        Setting::firstOrCreate(
            ['key' => 'about.skills'],
            ['value' => implode(', ', $skills)]
        );

        // Interests — config uses 'interests' (simple string array)
        $interests = $portfolio['interests'] ?? [];
        Setting::firstOrCreate(
            ['key' => 'about.interests'],
            ['value' => is_array($interests) ? implode(', ', $interests) : (string) $interests]
        );

        // GitHub URL — config uses social.github
        Setting::firstOrCreate(
            ['key' => 'about.github_url'],
            ['value' => $portfolio['social']['github'] ?? '']
        );

        // LinkedIn URL — config uses social.linkedin
        Setting::firstOrCreate(
            ['key' => 'about.linkedin_url'],
            ['value' => $portfolio['social']['linkedin'] ?? '']
        );

        // Twitter URL — config uses social.twitter
        Setting::firstOrCreate(
            ['key' => 'about.twitter_url'],
            ['value' => $portfolio['social']['twitter'] ?? '']
        );

        // Location — not in config, default empty
        Setting::firstOrCreate(
            ['key' => 'about.location'],
            ['value' => $portfolio['location'] ?? '']
        );

        // Available for — not in config, default empty
        Setting::firstOrCreate(
            ['key' => 'about.available_for'],
            ['value' => $portfolio['available_for'] ?? '']
        );

        $this->command->info('About settings seeded successfully.');
    }
}
