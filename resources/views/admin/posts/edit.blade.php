@extends('layouts.admin')

@section('title', 'Edit Post')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-rose-pine-text">Edit Post</h1>
        <p class="text-rose-pine-subtle mt-2">Update the post details below.</p>
        <div class="mt-2 text-xs text-rose-pine-muted">
            <span>Created: {{ $post->created_at->format('M d, Y g:i A') }}</span>
            <span class="mx-2">•</span>
            <span>Updated: {{ $post->updated_at->format('M d, Y g:i A') }}</span>
        </div>
    </div>

    <form action="{{ route('admin.posts.update', $post) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Title -->
        <div>
            <label for="title" class="block text-sm font-medium text-rose-pine-text mb-2">
                Title <span class="text-rose-pine-love">*</span>
            </label>
            <input type="text"
                   name="title"
                   id="title"
                   value="{{ old('title', $post->title) }}"
                   required
                   class="w-full px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('title') border-rose-pine-love @enderror">
            @error('title')
            <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
            @enderror
        </div>

        <!-- Slug -->
        <div>
            <label for="slug" class="block text-sm font-medium text-rose-pine-text mb-2">
                Slug <span class="text-rose-pine-love">*</span>
            </label>
            <input type="text"
                   name="slug"
                   id="slug"
                   value="{{ old('slug', $post->slug) }}"
                   required
                   class="w-full px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('slug') border-rose-pine-love @enderror">
            @error('slug')
            <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
            @enderror
        </div>

        <!-- Excerpt -->
        <div>
            <label for="excerpt" class="block text-sm font-medium text-rose-pine-text mb-2">
                Excerpt
            </label>
            <textarea name="excerpt"
                      id="excerpt"
                      rows="3"
                      class="w-full px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('excerpt') border-rose-pine-love @enderror">{{ old('excerpt', $post->excerpt) }}</textarea>
            @error('excerpt')
            <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-rose-pine-subtle">A short summary of the post for previews and SEO.</p>
        </div>

        <!-- Filepath (Read-only if exists) -->
        @if($post->filepath)
        <div>
            <label class="block text-sm font-medium text-rose-pine-text mb-2">
                File Path
            </label>
            <input type="text"
                   value="{{ $post->filepath }}"
                   disabled
                   class="w-full px-4 py-2 bg-rose-pine-overlay text-rose-pine-subtle border border-rose-pine-base/20 rounded-lg cursor-not-allowed">
            <p class="mt-1 text-sm text-rose-pine-subtle">This post is synced from Git. Filepath cannot be changed.</p>
        </div>
        @endif

        <!-- Content -->
        <div>
            <label for="content" class="block text-sm font-medium text-rose-pine-text mb-2">
                Content (Markdown) <span class="text-rose-pine-love">*</span>
            </label>
            <textarea name="content"
                      id="content"
                      rows="20"
                      required
                      class="w-full px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold font-mono text-sm @error('content') border-rose-pine-love @enderror">{{ old('content', $post->content ?? '') }}</textarea>
            @error('content')
            <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-rose-pine-subtle">Write your post content in Markdown format.</p>
        </div>

        <!-- Category and Tags Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Category -->
            <div>
                <label for="category_id" class="block text-sm font-medium text-rose-pine-text mb-2">
                    Category
                </label>
                <select name="category_id"
                        id="category_id"
                        class="w-full px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('category_id') border-rose-pine-love @enderror">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
                @error('category_id')
                <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
                @enderror
            </div>

            <!-- Published At -->
            <div>
                <label for="published_at" class="block text-sm font-medium text-rose-pine-text mb-2">
                    Publish Date
                </label>
                <input type="datetime-local"
                       name="published_at"
                       id="published_at"
                       value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}"
                       class="w-full px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('published_at') border-rose-pine-love @enderror">
                @error('published_at')
                <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-rose-pine-subtle">Leave empty to save as draft.</p>
            </div>
        </div>

        <!-- Tags -->
        <div>
            <label class="block text-sm font-medium text-rose-pine-text mb-2">
                Tags
            </label>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 p-4 bg-rose-pine-surface border border-rose-pine-base/30 rounded-lg max-h-64 overflow-y-auto">
                @foreach($tags as $tag)
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox"
                           name="tags[]"
                           value="{{ $tag->id }}"
                           {{ in_array($tag->id, old('tags', $post->tags->pluck('id')->toArray())) ? 'checked' : '' }}
                           class="rounded border-rose-pine-base/30 text-rose-pine-gold focus:ring-rose-pine-gold focus:ring-offset-rose-pine-surface">
                    <span class="text-sm text-rose-pine-text">{{ $tag->name }}</span>
                </label>
                @endforeach
            </div>
            @error('tags')
            <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
            @enderror
        </div>

        <!-- Featured -->
        <div>
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox"
                       name="is_featured"
                       id="is_featured"
                       value="1"
                       {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}
                       class="rounded border-rose-pine-base/30 text-rose-pine-gold focus:ring-rose-pine-gold focus:ring-offset-rose-pine-surface">
                <span class="text-sm font-medium text-rose-pine-text">Mark as Featured Post</span>
            </label>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-rose-pine-base/30">
            <a href="{{ route('admin.posts.index') }}"
               class="px-6 py-2 text-rose-pine-text hover:text-rose-pine-gold transition">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-rose-pine-gold text-rose-pine-base rounded-lg hover:bg-opacity-80 transition">
                Update Post
            </button>
        </div>
    </form>
</div>
@endsection
