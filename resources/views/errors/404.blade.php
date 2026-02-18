{{-- 404 Error Page --}}
{{-- Plays on the "Out of Bounds" brand — a 404 is literally an out-of-bounds access --}}
@extends('layouts.app')

@section('content')
    <div class="min-h-[calc(100vh-10rem)] flex items-center justify-center px-4 py-20">
        <div class="max-w-2xl w-full text-center">

            {{-- 404 Number --}}
            <div class="relative mb-8">
                <span class="block text-[10rem] font-bold leading-none select-none"
                      style="color: var(--rose-pine-overlay);">
                    404
                </span>
                <span class="absolute inset-0 flex items-center justify-center text-[10rem] font-bold leading-none"
                      style="color: var(--rose-pine-gold); opacity: 0.15; filter: blur(32px); pointer-events: none;">
                    404
                </span>
            </div>

            {{-- Terminal-style error block --}}
            <div class="mb-10 text-left rounded-lg overflow-hidden border"
                 style="border-color: var(--rose-pine-overlay); background: var(--rose-pine-surface);">

                {{-- Terminal title bar --}}
                <div class="flex items-center gap-2 px-4 py-3 border-b"
                     style="border-color: var(--rose-pine-overlay); background: var(--rose-pine-overlay);">
                    <span class="w-3 h-3 rounded-full" style="background: #f4b8a8;"></span>
                    <span class="w-3 h-3 rounded-full" style="background: #f9c97c;"></span>
                    <span class="w-3 h-3 rounded-full" style="background: #a8d5d8;"></span>
                    <span class="ml-3 text-xs font-mono" style="color: var(--rose-pine-muted);">
                        exception.log
                    </span>
                </div>

                {{-- Terminal body --}}
                <div class="p-5 font-mono text-sm leading-relaxed space-y-1">
                    <p>
                        <span style="color: var(--rose-pine-muted);">$</span>
                        <span style="color: var(--rose-pine-foam);"> GET</span>
                        <span style="color: var(--rose-pine-text);"> {{ request()->path() }}</span>
                    </p>
                    <p class="pt-1">
                        <span style="color: var(--rose-pine-love);">IndexOutOfBoundsException:</span>
                        <span style="color: var(--rose-pine-text);"> page not found at index</span>
                        <span style="color: var(--rose-pine-gold);"> [404]</span>
                    </p>
                    <p style="color: var(--rose-pine-muted);">
                        &nbsp;&nbsp;at <span style="color: var(--rose-pine-iris);">Blog</span>::<span style="color: var(--rose-pine-foam);">find</span>(<span style="color: var(--rose-pine-gold);">"{{ request()->path() }}"</span>)
                    </p>
                    <p style="color: var(--rose-pine-muted);">
                        &nbsp;&nbsp;at <span style="color: var(--rose-pine-iris);">Router</span>::<span style="color: var(--rose-pine-foam);">dispatch</span>(<span style="color: var(--rose-pine-gold);">request</span>)
                    </p>
                    <p class="pt-2 text-xs" style="color: var(--rose-pine-muted);">
                        // This page went truly out of bounds.
                    </p>
                </div>
            </div>

            {{-- Heading + description --}}
            <h1 class="text-2xl font-bold mb-3" style="color: var(--rose-pine-text);">
                You've gone out of bounds.
            </h1>
            <p class="text-base mb-10" style="color: var(--rose-pine-subtle);">
                The page you're looking for doesn't exist, was moved, or maybe
                never made it past the idea stage.
            </p>

            {{-- Navigation links --}}
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                   style="background: var(--rose-pine-gold); color: #111827;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    Home
                </a>

                <a href="{{ route('posts.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                   style="background: var(--rose-pine-overlay); color: var(--rose-pine-text);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd" />
                        <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z" />
                    </svg>
                    Blog
                </a>

                <a href="javascript:history.back()"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                   style="background: var(--rose-pine-overlay); color: var(--rose-pine-text);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Go back
                </a>
            </div>

        </div>
    </div>
@endsection
