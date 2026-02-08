{{-- Newsletter Signup Component --}}
{{-- Double-opt-in newsletter subscription form with Alpine.js loading state --}}
<div x-data="{ loading: false, message: '' }" class="space-y-4">
    {{-- Description --}}
    <p class="text-sm text-rose-pine-subtle">
        Get the latest posts and updates delivered to your inbox.
    </p>

    {{-- Subscription Form --}}
    <form
        action="{{ route('newsletter.subscribe') }}"
        method="POST"
        @submit="loading = true"
        class="space-y-3"
    >
        @csrf

        {{-- Email Input --}}
        <div>
            <label for="newsletter-email" class="sr-only">Email address</label>
            <input
                type="email"
                id="newsletter-email"
                name="email"
                required
                placeholder="your@email.com"
                class="w-full px-4 py-2 bg-rose-pine-surface border border-rose-pine-muted text-rose-pine-text placeholder-rose-pine-muted rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-pine-love focus:border-transparent"
                value="{{ old('email') }}"
            />
        </div>

        {{-- Optional Name Input (hidden by default, can be revealed) --}}
        <div class="hidden">
            <label for="newsletter-name" class="sr-only">Your name (optional)</label>
            <input
                type="text"
                id="newsletter-name"
                name="name"
                placeholder="Your name (optional)"
                class="w-full px-4 py-2 bg-rose-pine-surface border border-rose-pine-muted text-rose-pine-text placeholder-rose-pine-muted rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-pine-love focus:border-transparent"
                value="{{ old('name') }}"
            />
        </div>

        {{-- Submit Button --}}
        <button
            type="submit"
            :disabled="loading"
            class="w-full px-6 py-2 bg-rose-pine-love hover:bg-rose-pine-pine text-white font-semibold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <span x-show="!loading">Subscribe</span>
            <span x-show="loading" class="flex items-center justify-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Subscribing...
            </span>
        </button>

        {{-- Error Messages --}}
        @if ($errors->has('email'))
            <p class="text-sm text-red-400 mt-2">
                {{ $errors->first('email') }}
            </p>
        @endif

        {{-- Success Message --}}
        @if (session('message'))
            <p class="text-sm text-green-400 mt-2">
                {{ session('message') }}
            </p>
        @endif
    </form>

    {{-- Privacy Note --}}
    <p class="text-xs text-rose-pine-muted">
        We respect your privacy. Unsubscribe at any time.
    </p>
</div>
