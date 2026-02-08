@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Posts -->
        <div class="bg-rose-pine-surface border border-rose-pine-highlight-low rounded-lg p-6 hover:border-rose-pine-foam transition-colors duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-rose-pine-subtle text-sm font-medium">Total Posts</p>
                    <p class="text-3xl font-bold text-rose-pine-text mt-2">{{ $stats['total_posts'] }}</p>
                </div>
                <div class="w-12 h-12 bg-rose-pine-foam/10 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-rose-pine-foam" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.posts.index') }}" class="text-rose-pine-foam text-sm mt-4 inline-block hover:underline">
                View all posts →
            </a>
        </div>

        <!-- Published Posts -->
        <div class="bg-rose-pine-surface border border-rose-pine-highlight-low rounded-lg p-6 hover:border-rose-pine-iris transition-colors duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-rose-pine-subtle text-sm font-medium">Published</p>
                    <p class="text-3xl font-bold text-rose-pine-text mt-2">{{ $stats['published_posts'] }}</p>
                </div>
                <div class="w-12 h-12 bg-rose-pine-iris/10 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-rose-pine-iris" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Draft Posts -->
        <div class="bg-rose-pine-surface border border-rose-pine-highlight-low rounded-lg p-6 hover:border-rose-pine-gold transition-colors duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-rose-pine-subtle text-sm font-medium">Drafts</p>
                    <p class="text-3xl font-bold text-rose-pine-text mt-2">{{ $stats['draft_posts'] }}</p>
                </div>
                <div class="w-12 h-12 bg-rose-pine-gold/10 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-rose-pine-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Unread Contacts -->
        <div class="bg-rose-pine-surface border border-rose-pine-highlight-low rounded-lg p-6 hover:border-rose-pine-love transition-colors duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-rose-pine-subtle text-sm font-medium">Unread Contacts</p>
                    <p class="text-3xl font-bold text-rose-pine-text mt-2">{{ $stats['unread_contacts'] }}</p>
                </div>
                <div class="w-12 h-12 bg-rose-pine-love/10 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-rose-pine-love" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.contacts.index') }}" class="text-rose-pine-love text-sm mt-4 inline-block hover:underline">
                View contacts →
            </a>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Categories -->
        <div class="bg-rose-pine-surface border border-rose-pine-highlight-low rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-rose-pine-subtle text-sm font-medium">Categories</p>
                    <p class="text-2xl font-bold text-rose-pine-text mt-2">{{ $stats['total_categories'] }}</p>
                </div>
                <a href="{{ route('admin.categories.index') }}" class="text-rose-pine-foam text-sm hover:underline">
                    Manage →
                </a>
            </div>
        </div>

        <!-- Tags -->
        <div class="bg-rose-pine-surface border border-rose-pine-highlight-low rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-rose-pine-subtle text-sm font-medium">Tags</p>
                    <p class="text-2xl font-bold text-rose-pine-text mt-2">{{ $stats['total_tags'] }}</p>
                </div>
                <a href="{{ route('admin.tags.index') }}" class="text-rose-pine-foam text-sm hover:underline">
                    Manage →
                </a>
            </div>
        </div>

        <!-- Projects -->
        <div class="bg-rose-pine-surface border border-rose-pine-highlight-low rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-rose-pine-subtle text-sm font-medium">Projects</p>
                    <p class="text-2xl font-bold text-rose-pine-text mt-2">{{ $stats['total_projects'] }}</p>
                </div>
                <a href="{{ route('admin.projects.index') }}" class="text-rose-pine-foam text-sm hover:underline">
                    Manage →
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Posts -->
        <div class="bg-rose-pine-surface border border-rose-pine-highlight-low rounded-lg p-6">
            <h3 class="text-lg font-semibold text-rose-pine-text mb-4">Recent Posts</h3>
            @if($recent_posts->count() > 0)
                <div class="space-y-3">
                    @foreach($recent_posts as $post)
                        <div class="flex items-start justify-between py-3 border-b border-rose-pine-highlight-low last:border-0">
                            <div class="flex-1">
                                <a href="{{ route('admin.posts.edit', $post) }}" class="text-rose-pine-text hover:text-rose-pine-foam transition-colors">
                                    {{ $post->title }}
                                </a>
                                <p class="text-sm text-rose-pine-subtle mt-1">
                                    {{ $post->created_at->diffForHumans() }}
                                    @if($post->published_at)
                                        <span class="text-rose-pine-iris">• Published</span>
                                    @else
                                        <span class="text-rose-pine-gold">• Draft</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('admin.posts.index') }}" class="text-rose-pine-foam text-sm mt-4 inline-block hover:underline">
                    View all posts →
                </a>
            @else
                <p class="text-rose-pine-subtle">No posts yet.</p>
            @endif
        </div>

        <!-- Recent Contact Submissions -->
        <div class="bg-rose-pine-surface border border-rose-pine-highlight-low rounded-lg p-6">
            <h3 class="text-lg font-semibold text-rose-pine-text mb-4">Recent Contact Submissions</h3>
            @if($recent_contacts->count() > 0)
                <div class="space-y-3">
                    @foreach($recent_contacts as $contact)
                        <div class="flex items-start justify-between py-3 border-b border-rose-pine-highlight-low last:border-0">
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
    </div>
</div>
@endsection
