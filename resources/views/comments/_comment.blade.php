@props(['comment' => null, 'depth' => 0])

<div
    class="comment-item {{ $depth > 0 ? 'ml-6 border-l-2 border-rose-pine-highlight pl-3' : '' }} mb-2"
    data-comment-id="{{ $comment->id }}"
>
    <div class="group py-2">
        {{-- Header: Avatar + Name + Time --}}
        <div class="flex items-center gap-2 mb-1.5">
            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-rose-pine-overlay flex items-center justify-center">
                <span class="text-rose-pine-subtle text-xs font-bold">
                    {{ strtoupper(substr($comment->author_name ?: 'A', 0, 1)) }}
                </span>
            </div>

            <span class="text-sm font-semibold text-rose-pine-text">
                {{ $comment->author_name ?: 'Anonymous' }}
            </span>

            <span class="text-xs text-rose-pine-muted">
                {{ $comment->created_at->diffForHumans() }}
            </span>

            @if($comment->status === 'pending')
                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-rose-pine-gold/20 text-rose-pine-gold">
                    Pending
                </span>
            @endif

            {{-- Admin IP info --}}
            @if(auth()->guard('admin')->check())
                <span class="text-xs text-rose-pine-muted font-mono ml-auto">
                    {{ $comment->ip_address }}
                </span>
            @endif
        </div>

        {{-- Content --}}
        <p class="text-sm text-rose-pine-text whitespace-pre-wrap leading-relaxed mb-1.5">
            {{ $comment->content }}
        </p>

        {{-- Actions --}}
        <div x-data="{ showReplyForm: false }">
            <div class="flex items-center gap-3">
                <button
                    @click="showReplyForm = !showReplyForm"
                    class="text-xs text-rose-pine-muted hover:text-rose-pine-love transition-colors duration-200 font-medium"
                >
                    <span x-show="!showReplyForm">Reply</span>
                    <span x-show="showReplyForm">Cancel</span>
                </button>
            </div>

            {{-- Reply form (inline when active) --}}
            <div
                x-show="showReplyForm"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform translate-y-2"
                class="mt-3 pl-8 border-l-2 border-rose-pine-love"
                x-init="$watch('showReplyForm', value => value && $nextTick(() => $el.querySelector('textarea').focus()))"
            >
                <div class="text-xs text-rose-pine-subtle mb-2">
                    Replying to <span class="font-semibold">{{ $comment->author_name ?: 'Anonymous' }}</span>
                </div>
                @include('comments._form', ['post' => $comment->post, 'parentId' => $comment->id])
            </div>
        </div>
    </div>
</div>