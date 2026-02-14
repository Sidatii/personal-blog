<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function __construct(private ImageUploadService $imageService) {}

    public function index()
    {
        $settings = Setting::where('key', 'like', 'about.%')->pluck('value', 'key');

        return view('admin.about.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'about_name'          => 'required|string|max:255',
            'about_headline'      => 'nullable|string|max:255',
            'about_bio'           => 'nullable|string|max:5000',
            'about_profile_photo' => 'nullable|string',
            'about_skills'        => 'nullable|string',
            'about_interests'     => 'nullable|string',
            'about_github_url'    => 'nullable|url|max:255',
            'about_linkedin_url'  => 'nullable|url|max:255',
            'about_twitter_url'   => 'nullable|url|max:255',
            'about_location'      => 'nullable|string|max:255',
            'about_available_for' => 'nullable|string|max:255',
        ]);

        $map = [
            'about_name'          => 'about.name',
            'about_headline'      => 'about.headline',
            'about_bio'           => 'about.bio',
            'about_profile_photo' => 'about.profile_photo',
            'about_skills'        => 'about.skills',
            'about_interests'     => 'about.interests',
            'about_github_url'    => 'about.github_url',
            'about_linkedin_url'  => 'about.linkedin_url',
            'about_twitter_url'   => 'about.twitter_url',
            'about_location'      => 'about.location',
            'about_available_for' => 'about.available_for',
        ];

        foreach ($map as $inputKey => $settingKey) {
            $value = $request->input($inputKey, '');

            // Handle profile photo: preserve existing if no new upload was made
            if ($inputKey === 'about_profile_photo') {
                if (empty($value)) {
                    // No new upload — keep the existing stored path
                    $value = $request->input('about_profile_photo_existing', '');
                } else {
                    // New upload — delete the old file if path changed
                    $current = Setting::where('key', 'about.profile_photo')->value('value');
                    if ($current && $current !== $value) {
                        $this->imageService->delete($current);
                    }
                }
            }

            Setting::updateOrCreate(['key' => $settingKey], ['value' => $value ?? '']);
        }

        // Log activity if ActivityLogger exists
        try {
            \App\Services\ActivityLogger::log('Updated about page content');
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.about.index')->with('success', 'About page updated successfully.');
    }
}
