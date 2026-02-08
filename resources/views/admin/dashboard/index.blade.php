@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
        <!-- Total Projects -->
        <div class="bg-gradient-to-br from-rose-pine-foam/90 to-rose-pine-foam rounded-lg p-6 hover:shadow-lg transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-rose-pine-base text-sm font-semibold uppercase tracking-wide">Total Projects</p>
                    <p class="text-4xl font-bold text-rose-pine-base mt-2">{{ $stats['total_projects'] }}</p>
                </div>
                <div class="w-14 h-14 bg-black/10 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-rose-pine-base" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.projects.index') }}" class="text-rose-pine-base text-sm mt-4 inline-block hover:underline font-semibold">
                Manage projects →
            </a>
        </div>

        <!-- Total Posts (git-based, read-only) -->
        <div class="bg-gradient-to-br from-rose-pine-gold/90 to-rose-pine-gold rounded-lg p-6 hover:shadow-lg transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-rose-pine-base text-sm font-semibold uppercase tracking-wide">Blog Posts</p>
                    <p class="text-4xl font-bold text-rose-pine-base mt-2">{{ $stats['total_posts'] }}</p>
                </div>
                <div class="w-14 h-14 bg-black/10 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-rose-pine-base" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <p class="text-rose-pine-base text-xs mt-4 font-semibold">Git-based publishing</p>
        </div>

        <!-- Unread Contacts -->
        <div class="bg-gradient-to-br from-rose-pine-love/90 to-rose-pine-love rounded-lg p-6 hover:shadow-lg transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-rose-pine-base text-sm font-semibold uppercase tracking-wide">Unread Contacts</p>
                    <p class="text-4xl font-bold text-rose-pine-base mt-2">{{ $stats['unread_contacts'] }}</p>
                </div>
                <div class="w-14 h-14 bg-black/10 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-rose-pine-base" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.contacts.index') }}" class="text-rose-pine-base text-sm mt-4 inline-block hover:underline font-semibold">
                View contacts →
            </a>
        </div>

        <!-- Pending Comments -->
        <div class="bg-gradient-to-br from-rose-pine-iris/90 to-rose-pine-iris rounded-lg p-6 hover:shadow-lg transition-all duration-200 {{ $stats['pending_comments'] > 0 ? 'ring-2 ring-rose-pine-gold' : '' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white text-sm font-semibold uppercase tracking-wide">Pending Comments</p>
                    <p class="text-4xl font-bold text-white mt-2">{{ $stats['pending_comments'] }}</p>
                </div>
                <div class="w-14 h-14 bg-black/10 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}" class="text-white text-sm mt-4 inline-block hover:underline font-semibold">
                Moderate comments →
            </a>
        </div>

        <!-- Analytics -->
        <div class="bg-gradient-to-br from-rose-pine-muted/90 to-rose-pine-muted rounded-lg p-6 hover:shadow-lg transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white text-sm font-semibold uppercase tracking-wide">Analytics</p>
                    <p class="text-lg font-bold text-white mt-2">
                        @if(config('services.umami.host') && config('services.umami.website_id'))
                            Active
                        @else
                            Not configured
                        @endif
                    </p>
                </div>
                <div class="w-14 h-14 bg-black/10 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
            @if(config('services.umami.host'))
                <a href="{{ config('services.umami.host') }}" target="_blank" class="text-white text-sm mt-4 inline-block hover:underline font-semibold">
                    Open dashboard ↗
                </a>
            @else
                <p class="text-white text-xs mt-4 font-semibold">Configure Umami in .env</p>
            @endif
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Contact Submissions -->
        <div class="bg-rose-pine-surface border border-rose-pine-base/20 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-rose-pine-text mb-4">Recent Contact Submissions</h3>
            @if($recent_contacts->count() > 0)
                <div class="space-y-3">
                    @foreach($recent_contacts as $contact)
                        <div class="flex items-start justify-between py-3 border-b border-rose-pine-base/20 last:border-0">
                            <div class="flex-1">
                                <a href="{{ route('admin.contacts.show', $contact) }}" class="text-rose-pine-text hover:text-rose-pine-foam transition-colors">
                                    {{ $contact->name }}
                                </a>
                                <p class="text-sm text-rose-pine-subtle mt-1">{{ $contact->subject }}</p>
                                <p class="text-sm text-rose-pine-subtle">
                                    {{ $contact->created_at->diffForHumans() }}
                                    @if(!$contact->is_read)
                                        <span class="text-rose-pine-love">• Unread</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('admin.contacts.index') }}" class="text-rose-pine-foam text-sm mt-4 inline-block hover:underline">
                    View all contacts →
                </a>
            @else
                <p class="text-rose-pine-subtle">No contact submissions yet.</p>
            @endif
        </div>

        <!-- Recent Comments -->
        <div class="bg-rose-pine-surface border border-rose-pine-base/20 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-rose-pine-text mb-4">Recent Comments</h3>
            @if($recent_comments->count() > 0)
                <div class="space-y-3">
                    @foreach($recent_comments as $comment)
                        <div class="flex items-start justify-between py-3 border-b border-rose-pine-base/20 last:border-0">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-rose-pine-text font-medium">
                                        {{ $comment->author_name ?: 'Anonymous' }}
                                    </span>
                                    <span class="text-xs px-2 py-0.5 rounded font-semibold
                                        @if($comment->status === 'pending') bg-rose-pine-gold text-rose-pine-base
                                        @elseif($comment->status === 'approved') bg-rose-pine-foam text-rose-pine-base
                                        @elseif($comment->status === 'spam') bg-rose-pine-love text-rose-pine-base
                                        @else bg-rose-pine-muted text-rose-pine-base
                                        @endif">
                                        {{ ucfirst($comment->status) }}
                                    </span>
                                </div>
                                <p class="text-sm text-rose-pine-subtle mt-1">
                                    on {{ $comment->post->title ?? 'Unknown Post' }}
                                </p>
                                <p class="text-sm text-rose-pine-subtle">
                                    {{ $comment->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}" class="text-rose-pine-foam text-sm mt-4 inline-block hover:underline">
                    View all comments →
                </a>
            @else
                <p class="text-rose-pine-subtle">No comments yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
