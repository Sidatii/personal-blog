{{-- Newsletter Signup Component --}}
{{-- Double-opt-in newsletter subscription form with Alpine.js loading state and toast notifications --}}
<div
    x-data="{
        loading: false,
        error: '',
        success: '',
        async submitForm(event) {
            event.preventDefault();
            this.loading = true;
            this.error = '';
            this.success = '';

            const form = event.target;
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                } else {
                    this.error = data.message || 'Something went wrong. Please try again.';
                    this.scrollToError();
                }
            } catch (e) {
                this.error = 'Network error. Please check your connection and try again.';
                this.scrollToError();
            } finally {
                this.loading = false;
            }
        },
        scrollToError() {
            this.$nextTick(() => {
                const form = this.$el.querySelector('form');
                const errorEl = this.$el.querySelector('[x-ref=\"error\"]');
                if (errorEl) {
                    errorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        }
    }"
    x-init="
        $watch('error', (value) => {
            if (value) {
                dispatchToast('error', value);
            }
        });
        $watch('success', (value) => {
            if (value) {
                dispatchToast('success', value);
            }
        });
    "
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
        <div x-ref="error" x-show="error" x-cloak class="hidden">
            <p
                x-text="error"
                class="text-sm text-red-400 mt-2 bg-red-900/20 border border-red-400/30 rounded-lg p-3"
            ></p>
        </div>

        {{-- Success Message --}}
        <div x-show="success" x-cloak class="hidden">
            <p
                x-text="success"
                class="text-sm text-green-400 mt-2 bg-green-900/20 border border-green-400/30 rounded-lg p-3"
            ></p>
        </div>
    </form>

    {{-- Privacy Note --}}
    <p class="xs text-rose-pine-muted">
        We respect your privacy. Unsubscribe at any time.
    </p>
</div>

{{-- Toast Notification Container (should be in layout) --}}
<script>
    function dispatchToast(type, message) {
        if (typeof window.dispatchEvent === 'function') {
            window.dispatchEvent(new CustomEvent('toast', { detail: { type, message } }));
        }
    }
</script>
