@extends('layouts.app')

@section('title', $post->title . ' - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="lg:grid lg:grid-cols-4 lg:gap-10 min-w-0">
        {{-- Table of Contents - Sticky sidebar (hidden on mobile) --}}
        <aside class="hidden lg:block lg:col-span-1 min-w-0">
            @if(isset($headings) && count($headings) > 0)
                @include('components.table-of-contents', ['headings' => $headings])
            @endif
        </aside>

        {{-- Main Article Content --}}
        <article class="lg:col-span-3 min-w-0">
            {{-- Reading Progress Bar --}}
            @include('components.reading-progress')
            
            {{-- Post Header --}}
            <header class="mb-8 min-w-0">
                {{-- Post Title --}}
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-rose-pine-text mb-4 leading-tight break-words">
                    {{ $post->title }}
                </h1>
                
                {{-- Post Meta --}}
                <div class="flex flex-wrap items-center gap-2 sm:gap-4 text-xs sm:text-sm text-rose-pine-muted mb-4">
                    {{-- Published Date --}}
                    @if($post->published_at)
                        <time datetime="{{ $post->published_at->toISOString() }}" class="flex items-center gap-1 whitespace-nowrap">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            {{ $post->published_at->format('M j, Y') }}
                        </time>
                    @endif
                    
                    {{-- Category --}}
                    @if($post->category)
                        <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                            <span class="truncate max-w-[120px] sm:max-w-none">{{ $post->category->name }}</span>
                        </span>
                    @endif
                    
                    {{-- Reading Time Estimate --}}
                    @if(isset($readingTime))
                        <span class="flex items-center gap-1 whitespace-nowrap">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            {{ $readingTime }} min
                        </span>
                    @endif
                </div>
                
                {{-- Tags --}}
                @if($post->tags && $post->tags->count() > 0)
                    <div class="flex flex-wrap gap-1.5 sm:gap-2">
                        @foreach($post->tags as $tag)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-rose-pine-overlay text-rose-pine-subtle">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </header>
            
            {{-- Post Excerpt (if present) --}}
            @if($post->excerpt)
                <div class="mb-8 p-4 bg-rose-pine-overlay/50 border-l-4 border-rose-pine-pine rounded-r-lg">
                    <p class="text-rose-pine-subtle italic">{{ $post->excerpt }}</p>
                </div>
            @endif
            
             {{-- Post Content --}}
             <div class="prose prose-invert prose-rose-pine max-w-none post-content min-w-0">
                 {!! $content !!}
             </div>

            {{-- Reaction Bar --}}
            <x-reaction-bar :reactable="$post" />

            {{-- Comments Section --}}
            @if(isset($comments))
                @include('comments.index', compact('post', 'comments'))
            @endif

            {{-- Post Footer --}}
            <footer class="mt-12 pt-8 border-t border-rose-pine-overlay">
                <div class="flex items-center justify-between">
                    {{-- Back to posts link --}}
                    <a href="{{ url('/') }}" class="text-rose-pine-subtle hover:text-rose-pine-text transition-colors flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                        Back to posts
                    </a>
                    
                    {{-- Share buttons could go here --}}
                </div>
            </footer>
        </article>
    </div>
</div>
@endsection

@push('head')
<style>
    /* ── Rose Pine theme tokens for @tailwindcss/typography ─────────────────── */
    .prose-rose-pine {
        --tw-prose-body:        var(--rose-pine-text);
        --tw-prose-headings:    var(--rose-pine-text);
        --tw-prose-lead:        var(--rose-pine-subtle);
        --tw-prose-links:       var(--rose-pine-iris);
        --tw-prose-bold:        var(--rose-pine-text);
        --tw-prose-counters:    var(--rose-pine-muted);
        --tw-prose-bullets:     var(--rose-pine-muted);
        --tw-prose-hr:          var(--rose-pine-highlight);
        --tw-prose-quotes:      var(--rose-pine-subtle);
        --tw-prose-quote-borders: var(--rose-pine-highlight);
        --tw-prose-captions:    var(--rose-pine-muted);
        --tw-prose-code:        var(--rose-pine-foam);
        --tw-prose-pre-code:    var(--rose-pine-text);
        --tw-prose-pre-bg:      var(--rose-pine-overlay);
        --tw-prose-th-borders:  var(--rose-pine-highlight);
        --tw-prose-td-borders:  var(--rose-pine-highlight);
    }

    /* ── Links ───────────────────────────────────────────────────────────────── */
    .prose a {
        color: var(--rose-pine-iris);
        text-decoration: underline;
        text-underline-offset: 3px;
        text-decoration-color: color-mix(in srgb, var(--rose-pine-iris) 50%, transparent);
        transition: color 0.15s, text-decoration-color 0.15s;
    }
    .prose a:hover {
        color: var(--rose-pine-foam);
        text-decoration-color: var(--rose-pine-foam);
    }

    /* ── Headings ────────────────────────────────────────────────────────────── */
    .prose h1 {
        font-size: clamp(1.5rem, 5vw, 2rem); font-weight: 700;
        color: var(--rose-pine-text);
        margin-top: 2em; margin-bottom: 0.75em;
        letter-spacing: -0.025em; line-height: 1.2;
    }
    .prose h2 {
        font-size: clamp(1.25rem, 4vw, 1.5rem); font-weight: 600;
        color: var(--rose-pine-text);
        margin-top: 2em; margin-bottom: 0.75em;
        border-bottom: 1px solid var(--rose-pine-highlight);
        padding-bottom: 0.3em; line-height: 1.3;
    }
    .prose h3 {
        font-size: clamp(1.1rem, 3vw, 1.25rem); font-weight: 600;
        color: var(--rose-pine-subtle);
        margin-top: 1.5em; margin-bottom: 0.5em;
    }
    .prose h4 {
        font-size: clamp(1rem, 2.5vw, 1rem); font-weight: 600;
        color: var(--rose-pine-muted);
        margin-top: 1.25em; margin-bottom: 0.4em;
    }
    .prose h5 {
        font-size: 0.875rem; font-weight: 500;
        color: var(--rose-pine-muted);
        margin-top: 1em; margin-bottom: 0.25em;
    }
    .prose h6 {
        font-size: 0.8rem; font-weight: 500;
        color: var(--rose-pine-muted);
        margin-top: 0.75em; margin-bottom: 0.25em;
        text-transform: uppercase; letter-spacing: 0.05em;
    }

    /* ── Body text & spacing ─────────────────────────────────────────────────── */
    .prose p  { margin-top: 0; margin-bottom: 1.25em; line-height: 1.8; }
    .prose li { margin-bottom: 0.4em; line-height: 1.75; }
    .prose ul { margin-bottom: 1.25em; }
    .prose ol { margin-bottom: 1.25em; }

    /* Mobile text wrapping fixes */
    .prose {
        overflow-wrap: break-word;
        word-wrap: break-word;
        word-break: break-word;
        hyphens: auto;
    }
    .prose img {
        max-width: 100%;
        height: auto;
    }

    /* Code block containers must not overflow */
    .prose .code-block {
        max-width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .prose .code-block {
        width: 100%;
        max-width: calc(100vw - 2rem);
        margin-left: calc(-50vw + 50% + 1rem);
        margin-right: calc(-50vw + 50% + 1rem);
    }

    @media (min-width: 640px) {
        .prose .code-block {
            max-width: 100%;
            margin-left: 0;
            margin-right: 0;
        }
    }

    /* ── Inline code ─────────────────────────────────────────────────────────── */
    .prose code:not([class]) {
        background-color: var(--rose-pine-overlay);
        color: var(--rose-pine-foam);
        padding: 0.15em 0.4em;
        border-radius: 0.3em;
        font-size: 0.875em;
        white-space: nowrap;
        word-wrap: normal;
    }
    .prose code:not([class])::before,
    .prose code:not([class])::after { content: none; }

    /* ── Blockquotes ─────────────────────────────────────────────────────────── */
    .prose blockquote {
        border-left: 4px solid var(--rose-pine-highlight);
        padding: 0.25em 1em;
        color: var(--rose-pine-subtle);
        font-style: italic;
        margin: 1.5em 0;
        background-color: transparent;
    }
    .prose blockquote p { margin-bottom: 0.5em; }
    .prose blockquote p:last-child { margin-bottom: 0; }

    /* ── GitHub-style callouts ───────────────────────────────────────────────── */
    .callout {
        border-left: 4px solid;
        border-radius: 0 0.5rem 0.5rem 0;
        padding: 0.75rem 1rem;
        margin: 1.25rem 0;
        background-color: var(--rose-pine-surface);
        overflow-wrap: break-word;
    }
    .callout-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin: 0 0 0.5em;
    }
    .callout-body > p:last-child { margin-bottom: 0; }
    .callout-body p { font-size: 0.9rem; }

    .callout-note      { border-color: var(--rose-pine-iris); }
    .callout-note      .callout-title { color: var(--rose-pine-iris); }
    .callout-tip       { border-color: var(--rose-pine-foam); }
    .callout-tip       .callout-title { color: var(--rose-pine-foam); }
    .callout-important { border-color: var(--rose-pine-gold); }
    .callout-important .callout-title { color: var(--rose-pine-gold); }
    .callout-warning   { border-color: var(--rose-pine-love); }
    .callout-warning   .callout-title { color: var(--rose-pine-love); }
    .callout-caution   { border-color: var(--rose-pine-pine); }
    .callout-caution   .callout-title { color: var(--rose-pine-pine); }

    /* ── Horizontal rules ────────────────────────────────────────────────────── */
    .prose hr {
        border-color: var(--rose-pine-highlight);
        margin: 2.5em 0;
    }

    /* ── Smooth scroll + anchor offset ──────────────────────────────────────── */
    html { scroll-behavior: smooth; }
    [id]  { scroll-margin-top: 6rem; }

    /* ── Shiki code blocks ───────────────────────────────────────────────────── */
    /* Reset prose pre defaults */
    .prose pre { margin: 0; padding: 0; background: transparent; border-radius: 0; font-size: 1rem; }

    /* Unified container styling for all shiki variants */
    .prose .code-block {
        border-radius: 0.5rem;
        overflow: hidden;
        margin: 1.5em 0;
        max-width: 100%;
        width: 100%;
        background: var(--rose-pine-overlay);
    }

    /* Wrapper divs inside code-block */
    .shiki-dark,
    .shiki-dawn {
        background: transparent !important;
    }

    /* Shiki output - scrollable container */
    .shiki {
        counter-reset: line;
        background: transparent !important;
        margin: 0 !important;
        font-size: 0.8rem;
        line-height: 1.5;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .shiki code {
        display: block;
        background: transparent !important;
    }

    /* Line numbers - inline, scrolls naturally with content */
    .shiki .line {
        display: block;
        padding: 0 0.5rem 0 0;
    }

    .shiki .line::before {
        counter-increment: line;
        content: counter(line);
        display: inline-block;
        width: 2em;
        margin-right: 0.5em;
        text-align: right;
        color: var(--rose-pine-muted);
        user-select: none;
        opacity: 0.5;
        font-size: 0.75rem;
    }

    /* Mobile adjustments */
    @media (max-width: 640px) {
        .shiki { font-size: 0.7rem; }
        .shiki .line::before { width: 1.75em; font-size: 0.65rem; }
    }

    /* ── Tables ──────────────────────────────────────────────────────────────── */
    .prose table {
        width: 100%; border-collapse: collapse;
        margin: 1.5em 0; font-size: 0.875rem;
        display: block; overflow-x: auto;
    }
    .prose thead { background-color: var(--rose-pine-surface); }
    .prose th {
        padding: 0.625rem 1rem; text-align: left;
        font-weight: 600; color: var(--rose-pine-text);
        border: 1px solid var(--rose-pine-highlight); white-space: nowrap;
    }
    .prose td {
        padding: 0.625rem 1rem; color: var(--rose-pine-subtle);
        border: 1px solid var(--rose-pine-highlight); vertical-align: top;
    }
    .prose tbody tr:nth-child(even) { background-color: var(--rose-pine-overlay); }
    .prose tbody tr:hover            { background-color: var(--rose-pine-highlight); }

    /* ── KaTeX math ──────────────────────────────────────────────────────────── */
    .prose .katex-display {
        margin: 1.5em 0; overflow-x: auto; overflow-y: hidden;
    }
</style>
@endpush

