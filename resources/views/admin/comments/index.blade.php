@extends('layouts.admin')

@section('title', 'Comment Moderation')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-rose-pine-text">
            Comment Moderation
            <span class="text-lg font-normal text-rose-pine-subtle ml-2">
                ({{ $comments->total() }} {{ $status !== 'all' ? $status : 'total' }})
            </span>
        </h1>
    </div>


    <!-- Filter Tabs -->
    <div class="mb-6 bg-rose-pine-surface rounded-lg p-4">
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}"
               class="px-4 py-2 rounded-lg transition {{ $status === 'pending' ? 'bg-rose-pine-gold text-rose-pine-base' : 'bg-rose-pine-base text-rose-pine-text hover:bg-rose-pine-overlay' }}">
                Pending
                <span class="ml-1 text-sm opacity-75">({{ $counts['pending'] }})</span>
            </a>
            <a href="{{ route('admin.comments.index', ['status' => 'approved']) }}"
               class="px-4 py-2 rounded-lg transition {{ $status === 'approved' ? 'bg-rose-pine-foam text-rose-pine-base' : 'bg-rose-pine-base text-rose-pine-text hover:bg-rose-pine-overlay' }}">
                Approved
                <span class="ml-1 text-sm opacity-75">({{ $counts['approved'] }})</span>
            </a>
            <a href="{{ route('admin.comments.index', ['status' => 'spam']) }}"
               class="px-4 py-2 rounded-lg transition {{ $status === 'spam' ? 'bg-rose-pine-love text-rose-pine-base' : 'bg-rose-pine-base text-rose-pine-text hover:bg-rose-pine-overlay' }}">
                Spam
                <span class="ml-1 text-sm opacity-75">({{ $counts['spam'] }})</span>
            </a>
            <a href="{{ route('admin.comments.index', ['status' => 'rejected']) }}"
               class="px-4 py-2 rounded-lg transition {{ $status === 'rejected' ? 'bg-rose-pine-muted text-rose-pine-base' : 'bg-rose-pine-base text-rose-pine-text hover:bg-rose-pine-overlay' }}">
                Rejected
                <span class="ml-1 text-sm opacity-75">({{ $counts['rejected'] }})</span>
            </a>
        </div>
    </div>

    <!-- Comments List -->
    <div class="space-y-4">
        @forelse($comments as $comment)
            @include('admin.comments._comment-card', ['comment' => $comment])
        @empty
            <div class="bg-rose-pine-surface border border-rose-pine-base/20 rounded-lg p-8 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-rose-pine-muted mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <h3 class="text-lg font-medium text-rose-pine-text mb-2">No {{ $status }} comments</h3>
                <p class="text-rose-pine-subtle">There are no comments with this status.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($comments->hasPages())
        <div class="mt-6">
            {{ $comments->appends(['status' => $status])->links() }}
        </div>
    @elseif($comments->total() > 0)
        <div class="mt-6">
            <p class="text-sm text-rose-pine-subtle">
                Showing all {{ $comments->total() }} {{ Str::plural('comment', $comments->total()) }}
            </p>
        </div>
    @endif
</div>
@endsection
