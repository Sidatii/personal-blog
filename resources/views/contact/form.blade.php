@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    {{-- Page Header --}}
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-rose-pine-text">Get In Touch</h1>
        <p class="text-rose-pine-subtle mt-4">
            I'd love to hear from you. Whether you have a project in mind, a question, or just want to say hello — send me a message!
        </p>
    </div>

    {{-- General Error Display --}}
    @if($errors->any())
        <div class="mb-6 bg-rose-pine-love/10 border border-rose-pine-love/30 text-rose-pine-love rounded-lg p-4">
            <p class="font-medium">Please correct the following errors:</p>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Contact Form --}}
    <form
        action="{{ route('contact.store') }}"
        method="POST"
        x-data="{ submitting: false }"
        @submit="submitting = true"
        class="space-y-6"
    >
        @csrf

        {{-- Name Field --}}
        <div>
            <label for="name" class="block text-sm font-medium text-rose-pine-text mb-1">
                Name <span class="text-rose-pine-love">*</span>
            </label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                required
                class="w-full px-4 py-2 rounded-lg bg-rose-pine-surface border border-rose-pine-overlay text-rose-pine-text placeholder-rose-pine-muted focus:outline-none focus:ring-2 focus:ring-rose-pine-gold/50 focus:border-rose-pine-gold"
                placeholder="Your name"
            >
            @error('name')
                <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email Field --}}
        <div>
            <label for="email" class="block text-sm font-medium text-rose-pine-text mb-1">
                Email <span class="text-rose-pine-love">*</span>
            </label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
                class="w-full px-4 py-2 rounded-lg bg-rose-pine-surface border border-rose-pine-overlay text-rose-pine-text placeholder-rose-pine-muted focus:outline-none focus:ring-2 focus:ring-rose-pine-gold/50 focus:border-rose-pine-gold"
                placeholder="your.email@example.com"
            >
            @error('email')
                <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
            @enderror
        </div>

        {{-- Subject Field --}}
        <div>
            <label for="subject" class="block text-sm font-medium text-rose-pine-text mb-1">
                Subject <span class="text-rose-pine-muted">(optional)</span>
            </label>
            <input
                type="text"
                id="subject"
                name="subject"
                value="{{ old('subject') }}"
                class="w-full px-4 py-2 rounded-lg bg-rose-pine-surface border border-rose-pine-overlay text-rose-pine-text placeholder-rose-pine-muted focus:outline-none focus:ring-2 focus:ring-rose-pine-gold/50 focus:border-rose-pine-gold"
                placeholder="What's this about?"
            >
            @error('subject')
                <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
            @enderror
        </div>

        {{-- Message Field --}}
        <div>
            <label for="message" class="block text-sm font-medium text-rose-pine-text mb-1">
                Message <span class="text-rose-pine-love">*</span>
            </label>
            <textarea
                id="message"
                name="message"
                rows="6"
                required
                minlength="10"
                class="w-full px-4 py-2 rounded-lg bg-rose-pine-surface border border-rose-pine-overlay text-rose-pine-text placeholder-rose-pine-muted focus:outline-none focus:ring-2 focus:ring-rose-pine-gold/50 focus:border-rose-pine-gold resize-y"
                placeholder="Tell me about your project, question, or just say hello..."
            >{{ old('message') }}</textarea>
            @error('message')
                <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit Button --}}
        <button
            type="submit"
            :disabled="submitting"
            class="w-full bg-rose-pine-gold text-gray-900 font-semibold py-3 px-6 rounded-lg hover:bg-rose-pine-gold/90 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
        >
            <span x-show="!submitting">Send Message</span>
            <span x-show="submitting" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Sending...
            </span>
        </button>
    </form>
</div>
@endsection
