@props(['comments' => [], 'depth' => 0])

@foreach($comments as $comment)
    <div x-data="{ showReplies: false }">
        @include('comments._comment', compact('comment', 'depth'))

        @if($comment->replies && $comment->replies->count() > 0 && $depth < (config('comments.max_depth', 5) - 1))
            {{-- Toggle button for replies --}}
            <div class="ml-4 mb-2">
                <button
                    @click="showReplies = !showReplies"
                    class="text-xs text-rose-pine-love hover:text-rose-pine-rose transition-colors duration-200 font-medium flex items-center space-x-1"
                >
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': showReplies }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span x-show="!showReplies">Show {{ $comment->replies->count() }} {{ $comment->replies->count() === 1 ? 'reply' : 'replies' }}</span>
                    <span x-show="showReplies">Hide replies</span>
                </button>
            </div>

            {{-- Replies (hidden by default) --}}
            <div
                x-show="showReplies"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2"
                class="thread-children ml-4"
            >
                @include('comments._thread', [
                    'comments' => $comment->replies,
                    'depth' => $depth + 1
                ])
            </div>
        @endif
    </div>
@endforeach