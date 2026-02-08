@props(['comment' => null, 'depth' => 0])

<div
    class="comment-item border-l-2 {{ $depth > 0 ? 'border-rose-pine-highlight' : 'border-transparent' }} {{ $depth > 0 ? 'pl-4' : '' }} mb-3"
    @if($depth > 0) style="margin-left: {{ $depth * 1.5 }}rem;" @endif
    data-comment-id="{{ $comment->id }}"
>
    <div class="bg-rose-pine-surface rounded-lg p-3 shadow-sm border border-rose-pine-highlight">
        {{-- Author information --}}
        <div class="flex items-start space-x-2 mb-2">
            {{-- Avatar placeholder --}}
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-rose-pine-overlay flex items-center justify-center">
                <span class="text-rose-pine-subtle text-xs font-medium">
                    {{ strtoupper(substr($comment->author_name ?: 'A', 0, 1)) }}
                </span>
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex items-center space-x-1.5">
                    <p class="text-sm font-semibold text-rose-pine-text truncate">
                        {{ $comment->author_name ?: 'Anonymous' }}
                    </p>

                    <span class="text-xs text-rose-pine-muted">
                        • {{ $comment->created_at->diffForHumans() }}
                    </span>

                    @if($comment->status === 'pending')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-rose-pine-gold/20 text-rose-pine-gold border border-rose-pine-gold/30">
                            Awaiting moderation
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Comment content --}}
        <div class="ml-10">
            <p class="text-sm text-rose-pine-text whitespace-pre-wrap leading-relaxed">
                {{ $comment->content }}
            </p>
        </div>

        {{-- Comment actions and reply form wrapper --}}
        <div x-data="{ showReplyForm: false }" class="ml-10">
            {{-- Comment actions --}}
            <div class="mt-2 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    {{-- Reply button --}}
                    <button
                        @click="showReplyForm = !showReplyForm"
                        class="text-xs text-rose-pine-love hover:text-rose-pine-rose transition-colors duration-200 font-medium"
                    >
                        <span x-show="!showReplyForm">Reply</span>
                        <span x-show="showReplyForm">Cancel</span>
                    </button>
                </div>

                {{-- Moderation info for admins ONLY --}}
                @if(auth()->guard('admin')->check())
                    <div class="text-xs text-rose-pine-muted">
                        <span class="font-mono">{{ $comment->ip_address }}</span>
                    </div>
                @endif
            </div>

            {{-- Reply form (shown when Reply is clicked) --}}
            <div
                x-show="showReplyForm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2"
            class="mt-4 border-t border-rose-pine-highlight pt-4"
            x-init="$watch('showReplyForm', value => value && $nextTick(() => $el.querySelector('textarea').focus()))"
        >
            <div class="text-sm font-medium text-rose-pine-subtle mb-2">
                Replying to {{ $comment->author_name ?: 'Anonymous' }}
            </div>


                @include('comments._form', ['post' => $comment->post, 'parentId' => $comment->id])
            </div>
        </div>
    </div>
</div>