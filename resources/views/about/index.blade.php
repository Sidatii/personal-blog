@extends('layouts.app')

@section('content')
@php
    $name      = $about['name'] ?: 'Developer';
    $nameClass = implode('', array_map('ucfirst', preg_split('/\s+/', trim($name))));

    // Bio → Javadoc lines (word-wrapped at 62 chars per line)
    $bio      = trim(strip_tags($about['bio'] ?? ''));
    $bioLines = [];
    if ($bio) {
        foreach (preg_split('/\n{2,}/', $bio) as $i => $para) {
            if ($i > 0) $bioLines[] = ' *';
            $para = preg_replace('/\s+/', ' ', trim($para));
            foreach (explode("\n", wordwrap($para, 62, "\n", true)) as $wl) {
                $bioLines[] = ' * ' . $wl;
            }
        }
    }

    $skills    = array_values(array_filter($about['skills']    ?? []));
    $interests = array_values(array_filter($about['interests'] ?? []));

    // Color token shorthand
    $mn = '<span class="text-rose-pine-muted">';
    $pi = '<span class="text-rose-pine-pine">';
    $ir = '<span class="text-rose-pine-iris">';
    $ro = '<span class="text-rose-pine-rose">';
    $go = '<span class="text-rose-pine-gold">';
    $su = '<span class="text-rose-pine-subtle">';
    $cl = '</span>';

    // Skills: 3 per row
    $skillLines = [];
    foreach (array_chunk($skills, 3) as $chunk) {
        $parts = array_map(fn($s) => $go . '"' . e($s) . '"' . $cl, $chunk);
        $skillLines[] = '            ' . implode($su . ', ' . $cl, $parts);
    }
    for ($i = 0; $i < count($skillLines) - 1; $i++) {
        $skillLines[$i] .= $su . ',' . $cl;
    }

    // Interests: 4 per row
    $intLines = [];
    foreach (array_chunk($interests, 4) as $chunk) {
        $parts = array_map(fn($s) => $go . '"' . e($s) . '"' . $cl, $chunk);
        $intLines[] = '            ' . implode($su . ', ' . $cl, $parts);
    }
    for ($i = 0; $i < count($intLines) - 1; $i++) {
        $intLines[$i] .= $su . ',' . $cl;
    }

    $location  = e($about['location']      ?? '');
    $available = e($about['available_for'] ?? '');
    $ghDisplay = e(preg_replace('#^https?://#', '', $about['github_url']   ?? ''));
    $liDisplay = e(preg_replace('#^https?://#', '', $about['linkedin_url'] ?? ''));
    $twDisplay = e(preg_replace('#^https?://#', '', $about['twitter_url']  ?? ''));

    // ── Assemble Developer.java class string ─────────────────────
    $jc  = $pi . 'package' . $cl . ' ' . $su . 'com.oob.about;' . $cl . "\n\n";

    $jc .= $mn . '/**' . $cl . "\n";
    foreach ($bioLines as $l) {
        $jc .= $mn . e($l) . $cl . "\n";
    }
    if ($bioLines) $jc .= $mn . ' *' . $cl . "\n";
    $jc .= $mn . ' * @author ' . e($name) . $cl . "\n";
    $jc .= $mn . ' */' . $cl . "\n\n";

    if ($location || $available) {
        $jc .= $ir . '@Profile' . $cl . $su . '(' . $cl . "\n";
        if ($location) {
            $jc .= '    location  ' . $su . '=' . $cl . ' ' . $go . '"' . $location . '"' . $cl;
            $jc .= $available ? ($su . ',' . $cl . "\n") : "\n";
        }
        if ($available) {
            $jc .= '    available ' . $su . '=' . $cl . ' ' . $go . '"' . $available . '"' . $cl . "\n";
        }
        $jc .= $su . ')' . $cl . "\n";
    }

    $jc .= $pi . 'public class' . $cl . ' ' . $ir . e($nameClass) . $cl;
    $jc .= ' ' . $pi . 'implements' . $cl . ' ' . $ir . 'Engineer' . $cl . ' ' . $su . '{' . $cl . "\n\n";

    if ($skillLines) {
        $jc .= '    ' . $pi . 'public' . $cl . ' ' . $ir . 'List' . $cl;
        $jc .= $su . '&lt;' . $cl . $ir . 'String' . $cl . $su . '&gt;' . $cl;
        $jc .= ' ' . $ro . 'skills' . $cl . $su . '() {' . $cl . "\n";
        $jc .= '        ' . $pi . 'return' . $cl . ' ' . $ir . 'List' . $cl . $su . '.' . $cl . $ro . 'of' . $cl . $su . '(' . $cl . "\n";
        foreach ($skillLines as $l) { $jc .= $l . "\n"; }
        $jc .= '        ' . $su . ');' . $cl . "\n";
        $jc .= '    ' . $su . '}' . $cl . "\n\n";
    }

    if ($intLines) {
        $jc .= '    ' . $pi . 'public' . $cl . ' ' . $ir . 'List' . $cl;
        $jc .= $su . '&lt;' . $cl . $ir . 'String' . $cl . $su . '&gt;' . $cl;
        $jc .= ' ' . $ro . 'interests' . $cl . $su . '() {' . $cl . "\n";
        $jc .= '        ' . $pi . 'return' . $cl . ' ' . $ir . 'List' . $cl . $su . '.' . $cl . $ro . 'of' . $cl . $su . '(' . $cl . "\n";
        foreach ($intLines as $l) { $jc .= $l . "\n"; }
        $jc .= '        ' . $su . ');' . $cl . "\n";
        $jc .= '    ' . $su . '}' . $cl . "\n\n";
    }

    if ($ghDisplay || $liDisplay || $twDisplay) {
        if ($ghDisplay)
            $jc .= '    ' . $pi . 'private final' . $cl . ' ' . $ir . 'String' . $cl . ' github   ' . $su . '=' . $cl . ' ' . $go . '"' . $ghDisplay . '"' . $cl . $su . ';' . $cl . "\n";
        if ($liDisplay)
            $jc .= '    ' . $pi . 'private final' . $cl . ' ' . $ir . 'String' . $cl . ' linkedin ' . $su . '=' . $cl . ' ' . $go . '"' . $liDisplay . '"' . $cl . $su . ';' . $cl . "\n";
        if ($twDisplay)
            $jc .= '    ' . $pi . 'private final' . $cl . ' ' . $ir . 'String' . $cl . ' twitter  ' . $su . '=' . $cl . ' ' . $go . '"' . $twDisplay . '"' . $cl . $su . ';' . $cl . "\n";
        $jc .= "\n";
    }

    $jc .= $su . '}' . $cl;
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid lg:grid-cols-4 gap-10 items-start">

        {{-- ── CODE PANEL (desktop: left 3/4 · mobile: second) ────── --}}
        <div class="order-2 lg:order-1 lg:col-span-3 min-w-0">
            <div class="rounded-xl overflow-hidden border" style="border-color:var(--rose-pine-overlay);background:var(--rose-pine-surface)">
                <div class="flex items-center gap-2 px-4 py-3 border-b" style="border-color:var(--rose-pine-overlay);background:var(--rose-pine-overlay)">
                    <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-love)"></span>
                    <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-gold)"></span>
                    <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-foam)"></span>
                    <span class="ml-3 text-xs font-mono" style="color:var(--rose-pine-muted)">{{ $nameClass }}.java</span>
                </div>
                <pre class="p-5 text-xs sm:text-sm leading-6 overflow-x-auto" style="color:var(--rose-pine-text)">{!! $jc !!}</pre>
            </div>
        </div>

        {{-- ── PROFILE CARD (desktop: right 1/4 · mobile: first) ──── --}}
        <div class="order-1 lg:order-2">
            <div class="rounded-xl border p-6 lg:sticky lg:top-6" style="background:var(--rose-pine-surface);border-color:var(--rose-pine-overlay)">

                {{-- Photo --}}
                <div class="flex justify-center mb-5">
                    @if($about['profile_photo_url'])
                        <img src="{{ $about['profile_photo_url'] }}"
                             alt="{{ $about['name'] }}"
                             class="w-28 h-28 rounded-full object-cover border-2"
                             style="border-color:var(--rose-pine-overlay)">
                    @else
                        <div class="w-28 h-28 rounded-full flex items-center justify-center border-2"
                             style="background:var(--rose-pine-overlay);border-color:var(--rose-pine-highlight)">
                            <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--rose-pine-muted)">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Name & Headline --}}
                <div class="text-center mb-5">
                    <h1 class="text-xl font-bold mb-1" style="color:var(--rose-pine-text)">{{ $about['name'] }}</h1>
                    @if($about['headline'])
                        <p class="text-sm leading-snug" style="color:var(--rose-pine-gold)">{{ $about['headline'] }}</p>
                    @endif
                </div>

                {{-- Location + Available --}}
                <div class="flex flex-col items-center gap-2 mb-6">
                    @if($about['location'])
                        <div class="flex items-center gap-1.5 text-xs" style="color:var(--rose-pine-subtle)">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $about['location'] }}
                        </div>
                    @endif
                    @if($about['available_for'])
                        <div class="flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium"
                             style="background:color-mix(in srgb,var(--rose-pine-pine) 12%,transparent);color:var(--rose-pine-pine)">
                            <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background:var(--rose-pine-pine)"></span>
                            {{ $about['available_for'] }}
                        </div>
                    @endif
                </div>

                {{-- Social links --}}
                @if($about['github_url'] || $about['linkedin_url'] || $about['twitter_url'])
                    <div class="flex flex-col gap-2">
                        @if($about['github_url'])
                            <a href="{{ $about['github_url'] }}" target="_blank" rel="noopener noreferrer"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-opacity hover:opacity-75"
                               style="background:var(--rose-pine-overlay);color:var(--rose-pine-subtle)">
                                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/>
                                </svg>
                                <span class="font-mono text-xs truncate">{{ preg_replace('#^https?://#', '', $about['github_url']) }}</span>
                            </a>
                        @endif
                        @if($about['linkedin_url'])
                            <a href="{{ $about['linkedin_url'] }}" target="_blank" rel="noopener noreferrer"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-opacity hover:opacity-75"
                               style="background:var(--rose-pine-overlay);color:var(--rose-pine-subtle)">
                                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                                <span class="font-mono text-xs truncate">{{ preg_replace('#^https?://#', '', $about['linkedin_url']) }}</span>
                            </a>
                        @endif
                        @if($about['twitter_url'])
                            <a href="{{ $about['twitter_url'] }}" target="_blank" rel="noopener noreferrer"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-opacity hover:opacity-75"
                               style="background:var(--rose-pine-overlay);color:var(--rose-pine-subtle)">
                                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                                <span class="font-mono text-xs truncate">{{ preg_replace('#^https?://#', '', $about['twitter_url']) }}</span>
                            </a>
                        @endif
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection
