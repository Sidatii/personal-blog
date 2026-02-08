{{-- Admin Toast Notification Component --}}
<div
    x-data="{
        show: false,
        message: '',
        type: 'success',
        showToast(msg, notifType = 'success') {
            this.message = msg;
            this.type = notifType;
            this.show = true;
            setTimeout(() => this.show = false, 4000);
        }
    }"
    @admin-toast.window="showToast($event.detail.message, $event.detail.type || 'success')"
    x-init="
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif
        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif
        @if(session('warning'))
            showToast('{{ session('warning') }}', 'warning');
        @endif
        @if(session('info'))
            showToast('{{ session('info') }}', 'info');
        @endif
    "
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-2"
    class="fixed bottom-6 right-6 z-50 max-w-sm"
    style="display: none;"
>
    <div class="flex items-center gap-3 px-4 py-3 rounded-lg shadow-xl border-2"
         :class="{
             'bg-rose-pine-overlay border-rose-pine-foam': type === 'success',
             'bg-rose-pine-overlay border-rose-pine-love': type === 'error',
             'bg-rose-pine-overlay border-rose-pine-gold': type === 'warning',
             'bg-rose-pine-overlay border-rose-pine-iris': type === 'info'
         }">
        {{-- Icon --}}
        <svg class="w-5 h-5 flex-shrink-0"
             :class="{
                 'text-rose-pine-foam': type === 'success',
                 'text-rose-pine-love': type === 'error',
                 'text-rose-pine-gold': type === 'warning',
                 'text-rose-pine-iris': type === 'info'
             }"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {{-- Success checkmark --}}
            <path x-show="type === 'success'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            {{-- Error X --}}
            <path x-show="type === 'error'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            {{-- Warning exclamation --}}
            <path x-show="type === 'warning'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            {{-- Info circle --}}
            <path x-show="type === 'info'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>

        {{-- Message --}}
        <p class="text-sm font-medium text-rose-pine-text" x-text="message"></p>

        {{-- Close button --}}
        <button @click="show = false" class="ml-auto text-rose-pine-subtle hover:text-rose-pine-text transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>
