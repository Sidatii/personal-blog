@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto px-4 py-24 text-center">
    {{-- Checkmark Icon --}}
    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mx-auto text-rose-pine-foam" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>

    {{-- Thank You Heading --}}
    <h1 class="text-3xl font-bold text-rose-pine-text mt-6">Thank You!</h1>

    {{-- Confirmation Message --}}
    <p class="text-rose-pine-subtle mt-4">
        Your message has been sent successfully. I appreciate you reaching out and I'll get back to you as soon as possible!
    </p>

    {{-- Success Flash Message (if present) --}}
    @if(session('success'))
        <div class="mt-6 bg-rose-pine-foam/10 border border-rose-pine-foam/30 text-rose-pine-foam rounded-lg p-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Back to Home Button --}}
    <a
        href="{{ route('posts.index') }}"
        class="inline-block mt-8 bg-rose-pine-gold text-gray-900 px-6 py-3 rounded-lg font-semibold hover:bg-rose-pine-gold/90 transition"
    >
        Back to Blog
    </a>
</div>
@endsection
