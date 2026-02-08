<div class="bg-rose-pine-surface border border-rose-pine-base/20 rounded-lg p-5 hover:border-rose-pine-highlight-med transition-colors {{ $comment->is_highlighted ? 'ring-2 ring-rose-pine-gold' : '' }}">
    <!-- Header Row -->
    <div class="flex flex-wrap items-start justify-between gap-4 mb-3">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-bold text-rose-pine-text">
                    {{ $comment->author_name ?: 'Anonymous' }}
                </span>
                @if($comment->is_highlighted)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-rose-pine-gold text-rose-pine-base font-semibold" title="Highlighted comment">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        Highlighted
                    </span>
                @endif
                @if($comment->author_email)
                    <span class="text-sm text-rose-pine-subtle">&lt;{{ $comment->author_email }}&gt;</span>
                @endif
            </div>
            <div class="text-sm text-rose-pine-subtle mt-1">
                on
                <a href="{{ route('posts.show', $comment->post->slug ?? $comment->post_id) }}" 
                   class="text-rose-pine-gold hover:text-rose-pine-foam transition-colors" 
                   target="_blank">
                    {{ $comment->post->title ?? 'Unknown Post' }}
                </a>
                <span class="mx-2">•</span>
                <span title="{{ $comment->created_at->format('F j, Y g:i A') }}">
                    {{ $comment->created_at->diffForHumans() }}
                </span>
            </div>
        </div>

        <!-- Status Badge -->
        <div>
            @if($comment->status === 'pending')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-rose-pine-gold bg-opacity-20 text-rose-pine-gold border border-rose-pine-gold border-opacity-30">
                    Pending
                </span>
            @elseif($comment->status === 'approved')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-rose-pine-foam bg-opacity-20 text-rose-pine-foam border border-rose-pine-foam border-opacity-30">
                    Approved
                </span>
            @elseif($comment->status === 'spam')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-rose-pine-love bg-opacity-20 text-rose-pine-love border border-rose-pine-love border-opacity-30">
                    Spam
                </span>
            @elseif($comment->status === 'rejected')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-rose-pine-muted bg-opacity-20 text-rose-pine-muted border border-rose-pine-muted border-opacity-30">
                    Rejected
                </span>
            @endif
        </div>
    </div>

    <!-- Content -->
    <div class="bg-rose-pine-base bg-opacity-50 rounded-lg p-4 mb-4">
        <p class="text-rose-pine-text whitespace-pre-wrap">{{ $comment->content }}</p>
    </div>

    <!-- Metadata Row -->
    <div class="flex flex-wrap items-center gap-4 text-xs text-rose-pine-muted mb-4">
        <span class="flex items-center gap-1" title="IP Address">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            {{ $comment->ip_address ?: 'Unknown IP' }}
        </span>
        <span class="flex items-center gap-1" title="User Agent">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
            </svg>
            {{ Str::limit($comment->user_agent, 50) ?: 'Unknown browser' }}
        </span>
        <span class="flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
            </svg>
            ID: {{ $comment->id }}
        </span>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-wrap items-center gap-2">
        @if($comment->status !== 'approved')
            <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-rose-pine-foam text-rose-pine-base rounded-lg hover:bg-opacity-80 transition-colors text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Approve
                </button>
            </form>
        @endif

        @if($comment->status !== 'spam')
            <form action="{{ route('admin.comments.spam', $comment) }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-rose-pine-gold text-rose-pine-base rounded-lg hover:bg-opacity-80 transition-colors text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Mark as Spam
                </button>
            </form>
        @endif

        <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="inline"
              onsubmit="return confirm('Are you sure you want to permanently delete this comment? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-rose-pine-love text-rose-pine-base rounded-lg hover:bg-opacity-80 transition-colors text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete
            </button>
        </form>
    </div>
</div>
