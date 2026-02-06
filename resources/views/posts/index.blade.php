{{-- Blog Index Page --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <header class="mb-12">
            <h1 class="text-4xl font-bold text-rose-pine-text mb-4">Blog</h1>
            <p class="text-lg text-rose-pine-subtle">Latest articles and insights.</p>
        </header>

        @if($posts->count() > 0)
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach($posts as $post)
                    <article class="group">
                        <a href="{{ route('posts.show', $post->slug) }}" class="block">
                            <div class="p-6 rounded-lg bg-rose-pine-surface group-hover:bg-rose-pine-overlay transition-colors">
                                <h2 class="text-xl font-bold text-rose-pine-text group-hover:text-rose-pine-rose mb-2">
                                    {{ $post->title }}
                                </h2>

                                <div class="flex items-center gap-4 text-sm text-rose-pine-muted mb-3">
                                    <time datetime="{{ $post->published_at?->toIso8601String() }}">
                                        {{ $post->published_at?->format('M d, Y') }}
                                    </time>

                                    @if($post->tags->count())
                                        <div class="flex gap-2">
                                            @foreach($post->tags as $tag)
                                                <span class="px-2 py-1 rounded bg-rose-pine-overlay text-rose-pine-subtle text-xs">
                                                    {{ $tag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                @if($post->excerpt)
                                    <p class="text-rose-pine-subtle text-sm line-clamp-3">
                                        {{ $post->excerpt }}
                                    </p>
                                @endif
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <p class="text-rose-pine-muted">No posts yet. Check back soon!</p>
            </div>
        @endif
    </div>
@endsection
