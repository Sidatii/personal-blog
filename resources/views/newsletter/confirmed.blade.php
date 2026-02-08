@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full text-center">
        {{-- Success Icon --}}
        <div class="mb-6 flex justify-center">
            <div class="rounded-full bg-rose-pine-foam bg-opacity-20 p-4">
                <svg class="w-16 h-16 text-rose-pine-foam" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        {{-- Heading --}}
        <h1 class="text-3xl font-bold text-rose-pine-text mb-4">
            Subscription Confirmed!
        </h1>

        {{-- Message --}}
        <p class="text-lg text-rose-pine-subtle mb-8">
            You're all set! You'll receive our newsletter updates straight to your inbox.
        </p>

        {{-- Additional Info --}}
        <div class="bg-rose-pine-overlay border border-rose-pine-muted rounded-lg p-6 mb-8">
            <p class="text-sm text-rose-pine-subtle">
                Thank you for subscribing. We'll keep you updated with our latest posts and announcements.
            </p>
        </div>

        {{-- Back to Home --}}
        <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-rose-pine-love hover:bg-rose-pine-pine text-white font-semibold rounded-lg transition-colors">
            Back to Home
        </a>
    </div>
</div>
@endsection
