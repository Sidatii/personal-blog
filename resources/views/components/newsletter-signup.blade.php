{{-- Newsletter Signup Component --}}
{{-- Double-opt-in newsletter subscription form with Alpine.js loading state and toast notifications --}}
<div
    x-data="{
        loading: false,
        submitForm(event) {
            event.preventDefault();
            this.loading = true;

            const form = event.target;
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                // Follow redirects manually if needed
                if (response.redirected) {
                    window.location.href = response.url;
                    return null;
                }
                return response;
            })
            .then(response => {
                if (response === null) return;
                return response.json().catch(() => null);
            })
            .then(data => {
                if (data === null) return;
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else if (data.message) {
                    this.$dispatch('toast', { type: data.type ?? 'info', message: data.message });
                }
            })
            .catch(() => {
                // Silently ignore errors - either redirect happened or it's a real error we can't fix
            })
            .finally(() => {
                this.loading = false;
            });
        }
    }"
    class="space-y-4"
>
    {{-- Description --}}
    <p class="text-sm text-rose-pine-subtle">
        Get the latest posts and updates delivered to your inbox.
    </p>

    {{-- Subscription Form --}}
    <form
        action="{{ route('newsletter.subscribe') }}"
        method="POST"
        @submit="submitForm($event)"
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
                class="w-full px-4 py-3 bg-rose-pine-surface border border-rose-pine-muted text-rose-pine-text placeholder-rose-pine-muted rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-pine-love focus:border-transparent"
                value="{{ old('email') }}"
            />
        </div>

        {{-- Submit Button --}}
        <button
            type="submit"
            :disabled="loading"
            class="w-full px-6 py-3 bg-rose-pine-gold hover:bg-rose-pine-gold/80 text-gray-900 font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer flex items-center justify-center gap-2"
        >
            <template x-if="!loading">
                <span>Subscribe</span>
            </template>
            <template x-if="loading">
                <span class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Subscribing...
                </span>
            </template>
        </button>
    </form>

    {{-- Privacy Note --}}
    <p class="text-xs text-rose-pine-muted">
        We respect your privacy. Unsubscribe at any time.
    </p>
</div>
