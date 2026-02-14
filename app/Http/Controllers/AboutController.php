<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AboutController extends Controller
{
    /**
     * Display the About page with portfolio information.
     * Reads from settings table with config/portfolio.php as fallback.
     */
    public function index(Request $request): View
    {
        // Fetch all about.* settings from DB
        $raw = Setting::where('key', 'like', 'about.%')->pluck('value', 'key');

        // Parse skills from comma-separated string to array
        $skillsRaw = $raw['about.skills'] ?? null;
        if ($skillsRaw !== null && $skillsRaw !== '') {
            $skills = array_map('trim', explode(',', $skillsRaw));
        } else {
            // Fallback: flatten tech_stack from config
            $techStack = config('portfolio.tech_stack', []);
            $skills = [];
            foreach ($techStack as $category => $items) {
                foreach ((array) $items as $item) {
                    if (isset($item['name'])) {
                        $skills[] = $item['name'];
                    } elseif (is_string($item)) {
                        $skills[] = $item;
                    }
                }
            }
        }

        // Parse interests from comma-separated string to array
        $interestsRaw = $raw['about.interests'] ?? null;
        if ($interestsRaw !== null && $interestsRaw !== '') {
            $interests = array_map('trim', explode(',', $interestsRaw));
        } else {
            $interests = config('portfolio.interests', []);
        }

        // Resolve profile photo URL
        $profilePhotoPath = $raw['about.profile_photo'] ?? '';
        if ($profilePhotoPath !== '') {
            $profilePhotoUrl = Storage::disk('public')->url($profilePhotoPath);
        } else {
            $profilePhotoUrl = null;
        }

        // Build $about array with fallbacks to config/portfolio.php
        $about = [
            'name'              => $raw['about.name'] ?? config('portfolio.name', ''),
            'headline'          => $raw['about.headline'] ?? config('portfolio.title', ''),
            'bio'               => $raw['about.bio'] ?? implode("\n\n", array_filter([
                config('portfolio.bio.intro', ''),
                config('portfolio.bio.extended', ''),
            ])),
            'profile_photo_url' => $profilePhotoUrl,
            'skills'            => $skills,
            'interests'         => $interests,
            'github_url'        => $raw['about.github_url'] ?? config('portfolio.social.github', ''),
            'linkedin_url'      => $raw['about.linkedin_url'] ?? config('portfolio.social.linkedin', ''),
            'twitter_url'       => $raw['about.twitter_url'] ?? config('portfolio.social.twitter', ''),
            'location'          => $raw['about.location'] ?? '',
            'available_for'     => $raw['about.available_for'] ?? '',
        ];

        // Build SEO data
        $bioText = strip_tags($about['bio']);
        $seo = [
            'title'       => 'About',
            'description' => $bioText ? substr($bioText, 0, 160) : ('About ' . $about['name']),
            'type'        => 'website',
            'url'         => route('about'),
        ];

        return view('about.index', compact('about', 'seo'));
    }
}
