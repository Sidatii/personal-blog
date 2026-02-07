@extends('layouts.app')

@section('title', $post->title . ' - ' . config('app.name'))

@section('content')
<div class="max-w-6xl mx-auto px-4 py-12">
    <div class="lg:grid lg:grid-cols-4 lg:gap-12">
        {{-- Table of Contents - Sticky sidebar (hidden on mobile) --}}
        <aside class="hidden lg:block lg:col-span-1">
            @if(isset($headings) && count($headings) > 0)
                @include('components.table-of-contents', ['headings' => $headings])
            @endif
        </aside>

        {{-- Main Article Content --}}
        <article class="lg:col-span-3 min-w-0">
            {{-- Reading Progress Bar --}}
            @include('components.reading-progress')
            
            {{-- Post Header --}}
            <header class="mb-8">
                {{-- Post Title --}}
                <h1 class="text-3xl sm:text-4xl font-bold text-rose-pine-text mb-4 leading-tight">
                    {{ $post->title }}
                </h1>
                
                {{-- Post Meta --}}
                <div class="flex flex-wrap items-center gap-4 text-sm text-rose-pine-muted mb-4">
                    {{-- Published Date --}}
                    @if($post->published_at)
                        <time datetime="{{ $post->published_at->toISOString() }}" class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            {{ $post->published_at->format('F j, Y') }}
                        </time>
                    @endif
                    
                    {{-- Category --}}
                    @if($post->category)
                        <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                            {{ $post->category->name }}
                        </span>
                    @endif
                    
                    {{-- Reading Time Estimate --}}
                    @if(isset($readingTime))
                        <span class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            {{ $readingTime }} min read
                        </span>
                    @endif
                </div>
                
                {{-- Tags --}}
                @if($post->tags && $post->tags->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($post->tags as $tag)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-pine-overlay text-rose-pine-subtle">
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
            <div class="prose prose-invert prose-rose-pine max-w-none">
                {!! $content !!}
            </div>
            
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

{{-- Add custom CSS for prose styling --}}
@push('head')
<style>
    /* Custom prose styles for Rose Pine theme */
    .prose-rose-pine {
        --tw-prose-body: var(--rp-text);
        --tw-prose-headings: var(--rp-text);
        --tw-prose-lead: var(--rp-subtle);
        --tw-prose-links: var(--rp-iris);
        --tw-prose-bold: var(--rp-text);
        --tw-prose-counters: var(--rp-muted);
        --tw-prose-bullets: var(--rp-muted);
        --tw-prose-hr: var(--rp-highlight-med);
        --tw-prose-quotes: var(--rp-subtle);
        --tw-prose-quote-borders: var(--rp-highlight-med);
        --tw-prose-captions: var(--rp-muted);
        --tw-prose-code: var(--rp-text);
        --tw-prose-pre-code: var(--rp-text);
        --tw-prose-pre-bg: var(--rp-overlay);
        --tw-prose-th-borders: var(--rp-highlight-med);
        --tw-prose-td-borders: var(--rp-highlight-low);
    }

    /* Distinct header typography for visual hierarchy */
    .prose h1 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--rp-text);
        margin-top: 2em;
        margin-bottom: 1em;
        letter-spacing: -0.025em;
    }

    .prose h2 {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--rp-text);
        margin-top: 1.5em;
        margin-bottom: 0.75em;
        border-bottom: 1px solid var(--rp-highlight-med);
        padding-bottom: 0.25em;
    }

    .prose h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--rp-subtle);
        margin-top: 1.25em;
        margin-bottom: 0.5em;
    }

    .prose h4 {
        font-size: 1rem;
        font-weight: 500;
        color: var(--rp-muted);
        margin-top: 1em;
        margin-bottom: 0.25em;
    }

    .prose h5 {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--rp-muted);
        margin-top: 0.75em;
        margin-bottom: 0.25em;
    }

    .prose h6 {
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--rp-muted);
        margin-top: 0.5em;
        margin-bottom: 0.25em;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Smooth scroll for anchor links */
    html {
        scroll-behavior: smooth;
    }

    /* Offset for fixed header when scrolling to anchors */
    [id] {
        scroll-margin-top: 6rem;
    }

    /* Code block line numbers and transparent background */
    .shiki {
        counter-reset: line;
        background-color: transparent !important;
        background: transparent !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .shiki .line {
        display: inline-block;
        width: 100%;
    }

    .shiki .line::before {
        counter-increment: line;
        content: counter(line);
        display: inline-block;
        width: 2.5em;
        margin-right: 1em;
        text-align: right;
        color: var(--rp-muted);
        user-select: none;
        font-size: 0.85em;
        opacity: 0.6;
    }

    .shiki code {
        display: block;
        background: transparent !important;
    }
</style>
@endpush
