@props(['post' => null, 'comments' => collect(), 'totalRootComments' => 0])

<section
    id="comments-section"
    class="mt-12 border-t border-rose-pine-highlight pt-8"
    x-data="{
        offset: 5,
        totalRoot: {{ $totalRootComments }},
        hasMore: {{ $totalRootComments > 5 ? 'true' : 'false' }},
        isLoading: false,

        async loadMore() {
            if (this.isLoading || !this.hasMore) return;

            this.isLoading = true;

            try {
                const response = await fetch('{{ route('comments.index', $post) }}?offset=' + this.offset + '&per_page=5', {
                    headers: {
                        'Accept': 'text/html',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const html = await response.text();
                document.getElementById('comments-list').insertAdjacentHTML('beforeend', html);

                this.offset += 5;
                this.hasMore = this.offset < this.totalRoot;
            } catch (error) {
                console.error('Failed to load more comments:', error);
            } finally {
                this.isLoading = false;
            }
        }
    }"
>
    <header class="mb-6">
        <h2 class="text-2xl font-bold text-rose-pine-text">
            <span class="mr-2">💬</span>
            <span x-text="totalRoot"></span> {{ $totalRootComments === 1 ? 'Comment' : 'Comments' }}
        </h2>

        @if($totalRootComments > 0)
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
    <div id="comments-list" class="space-y-1">
        @include('comments._thread', ['comments' => $comments, 'depth' => 0])
    </div>

    {{-- Load more button --}}
    <div x-show="hasMore" class="mt-6 text-center">
        <button
            @click="loadMore()"
            :disabled="isLoading"
            class="px-6 py-2.5 bg-rose-pine-surface hover:bg-rose-pine-overlay border-2 border-rose-pine-highlight hover:border-rose-pine-subtle text-rose-pine-text font-medium rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <span x-show="!isLoading">Show more comments</span>
            <span x-show="isLoading" class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Loading...
            </span>
        </button>
    </div>

    @if($totalRootComments === 0)
        <div class="text-center py-8 text-rose-pine-muted">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <p class="text-lg font-medium mb-2 text-rose-pine-text">No comments yet</p>
            <p class="text-sm">Be the first to share your thoughts!</p>
        </div>
    @endif
</section>