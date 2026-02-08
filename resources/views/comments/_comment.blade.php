@props(['comment' => null, 'depth' => 0])

<div
    class="comment-item border-l-2 {{ $depth > 0 ? 'border-rose-pine-highlight' : 'border-transparent' }} {{ $depth > 0 ? 'pl-6' : '' }} mb-4"
    @if($depth > 0) style="margin-left: {{ $depth * 2 }}rem;" @endif
    data-comment-id="{{ $comment->id }}"
>
    <div class="bg-rose-pine-surface rounded-lg p-4 shadow-sm border border-rose-pine-highlight">
        {{-- Author information --}}
        <div class="flex items-start space-x-3 mb-3">
            {{-- Avatar placeholder --}}
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-rose-pine-overlay flex items-center justify-center">
                <span class="text-rose-pine-subtle font-medium">
                    {{ strtoupper(substr($comment->author_name ?: 'A', 0, 1)) }}
                </span>
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex items-center space-x-2">
                    <p class="text-sm font-medium text-rose-pine-text truncate">
                        {{ $comment->author_name ?: 'Anonymous' }}
                    </p>

                    <span class="text-xs text-rose-pine-muted">
                        {{ $comment->created_at->diffForHumans() }}
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
        <div class="prose prose-sm max-w-none prose-rose-pine">
            <p class="text-rose-pine-text whitespace-pre-wrap">
                {{ $comment->content }}
            </p>
        </div>

        {{-- Comment actions --}}
        <div class="mt-3 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                {{-- Reply button --}}
                <button
                    x-data="{ showReplyForm: false }"
                    @click="showReplyForm = !showReplyForm"
                    class="text-sm text-rose-pine-love hover:text-rose-pine-rose transition-colors duration-200 font-medium"
                >
                    <span x-show="!showReplyForm">Reply</span>
                    <span x-show="showReplyForm">Cancel</span>
                </button>

                {{-- Reactions (if enabled) --}}
                @if($comment->reaction_counts && count($comment->reaction_counts) > 0)
                    <x-reaction-bar :reactable="$comment" size="sm" />
                @endif
            </div>

            {{-- Moderation info for admins --}}
            @if(auth()->guard('admin')->check())
                <div class="text-xs text-rose-pine-muted">
                    <span class="font-mono">{{ $comment->ip_address }}</span>
                </div>
            @endif
        </div>

        {{-- Reply form (shown when Reply is clicked) --}}
        <div
            x-data="{ showReplyForm: false }"
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