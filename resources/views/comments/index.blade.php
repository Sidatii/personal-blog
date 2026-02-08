@props(['post' => null, 'comments' => collect()])

<section id="comments-section" class="mt-12 border-t border-rose-pine-highlight pt-8">
    <header class="mb-6">
        <h2 class="text-2xl font-bold text-rose-pine-text">
            <span class="mr-2">💬</span>
            {{ $comments->count() }} {{ $comments->count() === 1 ? 'Comment' : 'Comments' }}
        </h2>

        @if($comments->count() > 0)
            <p class="text-rose-pine-subtle">
                Join the discussion - share your thoughts and perspectives.
            </p>
        @endif
    </header>

    {{-- New comment form --}}
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-rose-pine-text mb-4">
            {{ $comments->count() > 0 ? 'Leave a Comment' : 'Start the Discussion' }}
        </h3>
        
        @include('comments._form', ['post' => $post])
    </div>

    {{-- Success message area for HTMX responses --}}
    <div
        id="comment-success-message"
        class="hidden mb-4 p-4 bg-rose-pine-foam/20 border border-rose-pine-foam/30 rounded-lg"
        x-data="{ show: false }"
        x-show="show"
        x-init="document.addEventListener('comment-posted', () => { show = true; setTimeout(() => show = false, 5000); })"
    >
        <p class="text-rose-pine-foam font-medium">
            Your comment has been {{ $post->comments()->where('status', 'approved')->exists() ? 'posted' : 'submitted for moderation' }}!
        </p>
    </div>

    {{-- Comments list --}}
    <div id="comments-list" class="space-y-4">
        @include('comments._thread', ['comments' => $comments])
    </div>

    @if($comments->count() === 0)
        <div class="text-center py-8 text-rose-pine-muted">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <p class="text-lg font-medium mb-2 text-rose-pine-text">No comments yet</p>
            <p class="text-sm">Be the first to share your thoughts!</p>
        </div>
    @endif
</section>