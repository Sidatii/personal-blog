@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Section 1: Hero/Bio --}}
    <section class="py-12 border-b border-rose-pine-overlay">
        <div class="md:flex md:items-start md:gap-12">
            {{-- Profile Photo / Placeholder --}}
            <div class="flex-shrink-0 mb-8 md:mb-0">
                @if($about['profile_photo_url'])
                    <img
                        src="{{ $about['profile_photo_url'] }}"
                        alt="{{ $about['name'] }}"
                        class="w-48 h-48 rounded-full object-cover border-2 border-rose-pine-overlay shadow-lg"
                    >
                @else
                    <div class="w-48 h-48 rounded-full bg-rose-pine-surface border-2 border-rose-pine-overlay flex items-center justify-center">
                        {{-- User icon placeholder --}}
                        <svg class="w-24 h-24 text-rose-pine-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Bio Info --}}
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-rose-pine-text mb-2">
                    {{ $about['name'] }}
                </h1>
                @if($about['headline'])
                    <p class="text-xl text-rose-pine-gold mb-4">
                        {{ $about['headline'] }}
                    </p>
                @endif
                @if($about['location'])
                    <p class="text-sm text-rose-pine-muted mb-4">
                        {{ $about['location'] }}
                    </p>
                @endif
                @if($about['bio'])
                    <p class="text-rose-pine-subtle leading-relaxed">
                        {{ $about['bio'] }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Social Links --}}
        @if($about['github_url'] || $about['linkedin_url'] || $about['twitter_url'])
            <div class="mt-8 flex flex-wrap gap-4">
                @if($about['github_url'])
                    <a href="{{ $about['github_url'] }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="flex items-center gap-2 text-rose-pine-subtle hover:text-rose-pine-gold transition text-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/>
                        </svg>
                        GitHub
                    </a>
                @endif
                @if($about['linkedin_url'])
                    <a href="{{ $about['linkedin_url'] }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="flex items-center gap-2 text-rose-pine-subtle hover:text-rose-pine-gold transition text-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                        LinkedIn
                    </a>
                @endif
                @if($about['twitter_url'])
                    <a href="{{ $about['twitter_url'] }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="flex items-center gap-2 text-rose-pine-subtle hover:text-rose-pine-gold transition text-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                        Twitter / X
                    </a>
                @endif
            </div>
        @endif
    </section>

    {{-- Section 2: Skills --}}
    @if(!empty($about['skills']))
        <section class="py-12 border-b border-rose-pine-overlay">
            <h2 class="text-2xl font-bold text-rose-pine-text mb-8">
                Skills & Expertise
            </h2>
            <div class="flex flex-wrap gap-3">
                @foreach($about['skills'] as $skill)
                    @if($skill)
                        <span class="bg-rose-pine-surface text-rose-pine-subtle px-4 py-2 rounded-full text-sm border border-rose-pine-overlay">
                            {{ $skill }}
                        </span>
                    @endif
                @endforeach
            </div>
        </section>
    @endif

    {{-- Section 3: Interests --}}
    @if(!empty($about['interests']))
        <section class="py-12">
            <h2 class="text-2xl font-bold text-rose-pine-text mb-8">
                Interests & Hobbies
            </h2>
            <div class="flex flex-wrap gap-3">
                @foreach($about['interests'] as $interest)
                    @if($interest)
                        <span class="bg-rose-pine-surface text-rose-pine-subtle px-4 py-2 rounded-full text-sm border border-rose-pine-overlay">
                            {{ $interest }}
                        </span>
                    @endif
                @endforeach
            </div>
        </section>
    @endif

</div>
@endsection
