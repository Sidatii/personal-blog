@props(['comments' => [], 'depth' => 0])

@forelse($comments as $comment)
    @include('comments._comment', compact('comment', 'depth'))
    
    @if($comment->replies && $comment->replies->count() > 0 && $depth < (config('comments.max_depth', 5) - 1))
        <div class="thread-children ml-4">
            @include('comments._thread', [
                'comments' => $comment->replies,
                'depth' => $depth + 1
            ])
        </div>
    @endif
@empty
    @if($depth === 0)
        <div class="text-center py-8 text-rose-pine-500 dark:text-rose-pine-400">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <p class="text-lg font-medium mb-2">No comments yet</p>
            <p class="text-sm">Be the first to share your thoughts!</p>
        </div>
    @endif
@endforelse