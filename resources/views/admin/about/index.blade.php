@extends('layouts.admin')

@section('title', 'About Page Editor')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-rose-pine-text">About Page</h1>
        <p class="text-rose-pine-subtle mt-2">Edit the content displayed on your public about page.</p>
    </div>

    <!-- Success Flash -->
    @if(session('success'))
        <div class="mb-6 px-4 py-3 bg-rose-pine-pine/20 border border-rose-pine-pine/40 text-rose-pine-foam rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.about.update') }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- Section: Identity --}}
        <div class="bg-rose-pine-surface rounded-lg p-6 space-y-6">
            <h2 class="text-lg font-semibold text-rose-pine-text border-b border-rose-pine-base/30 pb-3">Identity</h2>

            <!-- Name -->
            <div>
                <label for="about_name" class="block text-sm font-medium text-rose-pine-text mb-2">
                    Name <span class="text-rose-pine-love">*</span>
                </label>
                <input type="text"
                       name="about_name"
                       id="about_name"
                       value="{{ old('about_name', $settings['about.name'] ?? '') }}"
                       required
                       class="w-full px-4 py-2 bg-rose-pine-base text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('about_name') border-rose-pine-love @enderror">
                @error('about_name')
                    <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
                @enderror
            </div>

            <!-- Headline -->
            <div>
                <label for="about_headline" class="block text-sm font-medium text-rose-pine-text mb-2">
                    Headline / Role
                </label>
                <input type="text"
                       name="about_headline"
                       id="about_headline"
                       value="{{ old('about_headline', $settings['about.headline'] ?? '') }}"
                       placeholder="e.g. Software Developer"
                       class="w-full px-4 py-2 bg-rose-pine-base text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('about_headline') border-rose-pine-love @enderror">
                @error('about_headline')
                    <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location -->
            <div>
                <label for="about_location" class="block text-sm font-medium text-rose-pine-text mb-2">
                    Location
                </label>
                <input type="text"
                       name="about_location"
                       id="about_location"
                       value="{{ old('about_location', $settings['about.location'] ?? '') }}"
                       placeholder="e.g. London, UK"
                       class="w-full px-4 py-2 bg-rose-pine-base text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('about_location') border-rose-pine-love @enderror">
                @error('about_location')
                    <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
                @enderror
            </div>

            <!-- Available For -->
            <div>
                <label for="about_available_for" class="block text-sm font-medium text-rose-pine-text mb-2">
                    Available For
                </label>
                <input type="text"
                       name="about_available_for"
                       id="about_available_for"
                       value="{{ old('about_available_for', $settings['about.available_for'] ?? '') }}"
                       placeholder="e.g. Freelance, Full-time roles"
                       class="w-full px-4 py-2 bg-rose-pine-base text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('about_available_for') border-rose-pine-love @enderror">
                @error('about_available_for')
                    <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Section: Bio --}}
        <div class="bg-rose-pine-surface rounded-lg p-6 space-y-6">
            <h2 class="text-lg font-semibold text-rose-pine-text border-b border-rose-pine-base/30 pb-3">Bio</h2>

            <div>
                <label for="about_bio" class="block text-sm font-medium text-rose-pine-text mb-2">
                    Biography
                </label>
                <textarea name="about_bio"
                          id="about_bio"
                          rows="6"
                          class="w-full px-4 py-2 bg-rose-pine-base text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('about_bio') border-rose-pine-love @enderror">{{ old('about_bio', $settings['about.bio'] ?? '') }}</textarea>
                @error('about_bio')
                    <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-rose-pine-subtle">Supports basic markdown. Maximum 5000 characters.</p>
            </div>
        </div>

        {{-- Section: Profile Photo --}}
        <div class="bg-rose-pine-surface rounded-lg p-6 space-y-6">
            <h2 class="text-lg font-semibold text-rose-pine-text border-b border-rose-pine-base/30 pb-3">Profile Photo</h2>

            @php
                $currentPhotoPath = $settings['about.profile_photo'] ?? '';
                $currentPhotoUrl = $currentPhotoPath ? asset('storage/' . $currentPhotoPath) : null;
            @endphp

            {{-- Preserve existing photo path when no new upload is made --}}
            <input type="hidden" name="about_profile_photo_existing" value="{{ $currentPhotoPath }}">

            <x-admin-image-upload
                name="about_profile_photo"
                directory="uploads/about"
                :current="$currentPhotoUrl"
                label="Profile Photo"
            />
            <p class="text-sm text-rose-pine-subtle">Upload a square photo for best results. Displayed as a circle on the about page.</p>
        </div>

        {{-- Section: Skills & Interests --}}
        <div class="bg-rose-pine-surface rounded-lg p-6 space-y-6">
            <h2 class="text-lg font-semibold text-rose-pine-text border-b border-rose-pine-base/30 pb-3">Skills & Interests</h2>

            <!-- Skills -->
            <div>
                <label for="about_skills" class="block text-sm font-medium text-rose-pine-text mb-2">
                    Skills
                </label>
                <input type="text"
                       name="about_skills"
                       id="about_skills"
                       value="{{ old('about_skills', $settings['about.skills'] ?? '') }}"
                       placeholder="PHP, Laravel, Vue.js, PostgreSQL"
                       class="w-full px-4 py-2 bg-rose-pine-base text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('about_skills') border-rose-pine-love @enderror">
                @error('about_skills')
                    <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-rose-pine-subtle">Comma-separated: PHP, Laravel, Vue.js</p>
            </div>

            <!-- Interests -->
            <div>
                <label for="about_interests" class="block text-sm font-medium text-rose-pine-text mb-2">
                    Interests
                </label>
                <input type="text"
                       name="about_interests"
                       id="about_interests"
                       value="{{ old('about_interests', $settings['about.interests'] ?? '') }}"
                       placeholder="Open Source, Photography, Technical Writing"
                       class="w-full px-4 py-2 bg-rose-pine-base text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('about_interests') border-rose-pine-love @enderror">
                @error('about_interests')
                    <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-rose-pine-subtle">Comma-separated: Open Source, Photography</p>
            </div>
        </div>

        {{-- Section: Social Links --}}
        <div class="bg-rose-pine-surface rounded-lg p-6 space-y-6">
            <h2 class="text-lg font-semibold text-rose-pine-text border-b border-rose-pine-base/30 pb-3">Social Links</h2>

            <!-- GitHub -->
            <div>
                <label for="about_github_url" class="block text-sm font-medium text-rose-pine-text mb-2">
                    GitHub URL
                </label>
                <input type="url"
                       name="about_github_url"
                       id="about_github_url"
                       value="{{ old('about_github_url', $settings['about.github_url'] ?? '') }}"
                       placeholder="https://github.com/yourusername"
                       class="w-full px-4 py-2 bg-rose-pine-base text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('about_github_url') border-rose-pine-love @enderror">
                @error('about_github_url')
                    <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
                @enderror
            </div>

            <!-- LinkedIn -->
            <div>
                <label for="about_linkedin_url" class="block text-sm font-medium text-rose-pine-text mb-2">
                    LinkedIn URL
                </label>
                <input type="url"
                       name="about_linkedin_url"
                       id="about_linkedin_url"
                       value="{{ old('about_linkedin_url', $settings['about.linkedin_url'] ?? '') }}"
                       placeholder="https://linkedin.com/in/yourusername"
                       class="w-full px-4 py-2 bg-rose-pine-base text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('about_linkedin_url') border-rose-pine-love @enderror">
                @error('about_linkedin_url')
                    <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
                @enderror
            </div>

            <!-- Twitter -->
            <div>
                <label for="about_twitter_url" class="block text-sm font-medium text-rose-pine-text mb-2">
                    Twitter / X URL <span class="text-rose-pine-muted text-xs">(optional)</span>
                </label>
                <input type="url"
                       name="about_twitter_url"
                       id="about_twitter_url"
                       value="{{ old('about_twitter_url', $settings['about.twitter_url'] ?? '') }}"
                       placeholder="https://twitter.com/yourusername"
                       class="w-full px-4 py-2 bg-rose-pine-base text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('about_twitter_url') border-rose-pine-love @enderror">
                @error('about_twitter_url')
                    <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-4 pt-2">
            <a href="{{ route('admin.dashboard') }}"
               class="px-6 py-2 text-rose-pine-text hover:text-rose-pine-gold transition">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-rose-pine-gold text-rose-pine-base rounded-lg hover:bg-opacity-80 transition">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
