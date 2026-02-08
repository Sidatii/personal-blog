{{-- Toast Notifications Component --}}
<div
    x-data="{
        toasts: [],
        add(type, message, duration = 5000) {
            const id = Date.now();
            this.toasts.push({ id, type, message });
            if (duration > 0) {
                setTimeout(() => this.remove(id), duration);
            }
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        },
        success(message) { this.add('success', message); },
        error(message) { this.add('error', message); },
        info(message) { this.add('info', message); },
    }"
    x-on:toast.window="add($event.detail.type, $event.detail.message)"
    class="fixed bottom-4 right-4 z-50 space-y-2"
    aria-live="polite"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="min-w-72 p-4 rounded-lg shadow-lg border flex items-start gap-3"
            :class="{
                'bg-rose-pine-overlay border-rose-pine-gold text-rose-pine-text': toast.type === 'success',
                'bg-red-900/40 border-red-400/30 text-red-200': toast.type === 'error',
                'bg-rose-pine-surface border-rose-pine-muted text-rose-pine-text': toast.type === 'info'
            }"
        >
            <div class="flex-shrink-0">
                {{-- Success Icon --}}
                <svg x-show="toast.type === 'success'" class="w-5 h-5 text-rose-pine-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{-- Error Icon --}}
                <svg x-show="toast.type === 'error'" class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{-- Info Icon --}}
                <svg x-show="toast.type === 'info'" class="w-5 h-5 text-rose-pine-moon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="flex-1 text-sm" x-text="toast.message"></div>
            <button
                @click="remove(toast.id)"
                class="flex-shrink-0 opacity-60 hover:opacity-100 transition-opacity"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>
</div>
