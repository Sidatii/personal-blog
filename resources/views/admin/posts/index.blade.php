@extends('layouts.admin')

@section('title', 'Posts')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-rose-pine-text">Posts</h1>
        <a href="{{ route('admin.posts.create') }}"
           class="px-4 py-2 bg-rose-pine-gold text-rose-pine-base rounded-lg hover:bg-opacity-80 transition">
            Create New Post
        </a>
    </div>


    <!-- Search and Filters -->
    <div class="mb-6 bg-rose-pine-surface rounded-lg p-4">
        <form method="GET" action="{{ route('admin.posts.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text"
                       name="search"
                       placeholder="Search posts..."
                       value="{{ request('search') }}"
                       class="w-full px-4 py-2 bg-rose-pine-base text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold">
            </div>
            <div class="min-w-[150px]">
                <select name="category"
                        class="w-full px-4 py-2 bg-rose-pine-base text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[150px]">
                <select name="status"
                        class="w-full px-4 py-2 bg-rose-pine-base text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold">
                    <option value="">All Status</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <button type="submit"
                    class="px-6 py-2 bg-rose-pine-gold text-rose-pine-base rounded-lg hover:bg-opacity-80 transition">
                Filter
            </button>
            @if(request()->hasAny(['search', 'category', 'status']))
            <a href="{{ route('admin.posts.index') }}"
               class="px-6 py-2 bg-rose-pine-highlight-med text-rose-pine-text rounded-lg hover:bg-opacity-80 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Posts Table -->
    <div class="bg-rose-pine-surface rounded-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-rose-pine-overlay border-b border-rose-pine-base/30">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-pine-highlight-low">
                @forelse($posts as $post)
                <tr class="hover:bg-rose-pine-overlay transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-pine-text">{{ $post->id }}</td>
                    <td class="px-6 py-4 text-sm text-rose-pine-text">
                        <div class="font-medium">{{ $post->title }}</div>
                        <div class="text-rose-pine-subtle text-xs mt-1">{{ $post->slug }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-pine-text">
                        @if($post->category)
                        <span class="px-2 py-1 bg-rose-pine-gold text-rose-pine-base font-semibold rounded text-xs">
                            {{ $post->category->name }}
                        </span>
                        @else
                        <span class="text-rose-pine-subtle">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($post->published_at)
                        <span class="px-2 py-1 bg-rose-pine-foam text-rose-pine-base font-semibold rounded text-xs">
                            Published
                        </span>
                        @else
                        <span class="px-2 py-1 bg-rose-pine-gold text-rose-pine-base font-semibold rounded text-xs">
                            Draft
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-pine-subtle">
                        {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.posts.edit', $post) }}"
                           class="text-rose-pine-gold hover:text-rose-pine-foam mr-3">Edit</a>
                        <form action="{{ route('admin.posts.destroy', $post) }}"
                              method="POST"
                              class="inline"
                              onsubmit="return confirm('Are you sure you want to delete this post?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-pine-love hover:text-rose-pine-gold">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-rose-pine-subtle">
                        No posts found.
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
