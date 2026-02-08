@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full text-center">
        {{-- Icon --}}
        <div class="mb-6 flex justify-center">
            <div class="rounded-full bg-rose-pine-muted bg-opacity-20 p-4">
                <svg class="w-16 h-16 text-rose-pine-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
        </div>

        {{-- Heading --}}
        <h1 class="text-3xl font-bold text-rose-pine-text mb-4">
            Unsubscribed
        </h1>

        {{-- Message --}}
        <p class="text-lg text-rose-pine-subtle mb-8">
            You've been unsubscribed from our newsletter. Sorry to see you go!
        </p>

        {{-- Additional Info --}}
        <div class="bg-rose-pine-overlay border border-rose-pine-muted rounded-lg p-6 mb-8">
            <p class="text-sm text-rose-pine-subtle mb-2">
                You won't receive any more emails from us.
            </p>
            <p class="text-sm text-rose-pine-muted">
                Changed your mind? You can always subscribe again from our website.
            </p>
        </div>

        {{-- Back to Home --}}
        <a href="{{ route('home') }}" class="inline-flex items-center text-rose-pine-love hover:text-rose-pine-pine transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Home
        </a>
    </div>
</div>
@endsection
