{{-- Newsletter Signup Component --}}
{{-- Double-opt-in newsletter subscription form with Alpine.js loading state and toast notifications --}}
<div
    x-data="{
        loading: false,
        error: '',
        success: '',
        submitForm(event) {
            event.preventDefault();
            this.loading = true;
            this.error = '';
            this.success = '';

            const form = event.target;
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else if (data.message) {
                    this.$dispatch('toast', { type: 'error', message: data.message });
                }
            })
            .catch(e => {
                this.$dispatch('toast', { type: 'error', message: 'Network error. Please try again.' });
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
            class="w-full px-6 py-3 bg-rose-pine-love hover:bg-rose-pine-pine text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
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
