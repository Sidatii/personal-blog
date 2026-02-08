@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full text-center">
        {{-- Icon --}}
        <div class="mb-6 flex justify-center">
            <svg class="w-16 h-16 text-rose-pine-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path>
            </svg>
        </div>

        {{-- Heading --}}
        <h1 class="text-3xl font-bold text-rose-pine-text mb-4">
            Check Your Email
        </h1>

        {{-- Message --}}
        <p class="text-lg text-rose-pine-subtle mb-8">
            We've sent a confirmation email to your inbox. Click the link in the email to complete your subscription.
        </p>

        {{-- Additional Info --}}
        <div class="bg-rose-pine-overlay border border-rose-pine-muted rounded-lg p-6 mb-8">
            <p class="text-sm text-rose-pine-subtle mb-2">
                Don't see the email? Check your spam folder.
            </p>
            <p class="text-sm text-rose-pine-muted">
                The confirmation link will expire in 7 days.
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
