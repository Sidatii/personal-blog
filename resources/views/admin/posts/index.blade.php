@extends('layouts.admin')

@section('title', 'Blog Posts')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-rose-pine-text">Blog Posts</h1>
    </div>

    <!-- Info Note -->
    <div class="mb-6 p-4 bg-rose-pine-surface rounded-lg border border-rose-pine-highlight/30 flex items-start gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-rose-pine-pine flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-sm text-rose-pine-subtle">
            Posts are managed via git. Use the image manager to upload images for use in your markdown files.
        </p>
    </div>

    <!-- Posts Table -->
    <div class="bg-rose-pine-surface rounded-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-rose-pine-overlay border-b border-rose-pine-base/30">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Published At</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-pine-highlight-low">
                @forelse($posts as $post)
                <tr class="hover:bg-rose-pine-overlay transition">
                    <td class="px-6 py-4 text-sm text-rose-pine-text">
                        <div class="font-medium">{{ $post->title }}</div>
                        <div class="text-rose-pine-subtle text-xs mt-1">{{ $post->slug }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-pine-subtle">
                        {{ $post->category?->name ?? '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($post->published_at && $post->published_at->isPast())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-pine-foam/20 text-rose-pine-foam rounded-full text-xs font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-pine-foam inline-block"></span>
                                Published
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-pine-overlay text-rose-pine-muted rounded-full text-xs font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-pine-muted inline-block"></span>
                                Draft
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-pine-subtle">
                        {{ $post->published_at?->format('M j, Y') ?? '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.posts.show', $post->id) }}"
                           class="px-3 py-1.5 bg-rose-pine-gold text-rose-pine-base rounded-lg hover:bg-opacity-80 transition text-xs font-semibold">
                            Manage Images
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-rose-pine-subtle">
                        No posts found. Posts are synced from your git repository.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($posts->hasPages())
        <div class="mt-6">
            {{ $posts->links() }}
        </div>
    @elseif($posts->total() > 0)
        <div class="mt-6">
            <p class="text-sm text-rose-pine-subtle">
                Showing all {{ $posts->total() }} {{ Str::plural('post', $posts->total()) }}
            </p>
        </div>
    @endif
</div>
@endsection
