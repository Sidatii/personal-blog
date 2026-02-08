{{-- Search Results Page --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <header class="mb-12">
            <h1 class="text-4xl font-bold text-rose-pine-text mb-4">
                Search Results
                @if($query)
                    <span class="text-rose-pine-subtle">for "{{ $query }}"</span>
                @endif
            </h1>
            @if($query && (count($posts) > 0 || count($projects) > 0))
                <p class="text-lg text-rose-pine-subtle">
                    Found {{ count($posts) }} {{ Str::plural('post', count($posts)) }}
                    and {{ count($projects) }} {{ Str::plural('project', count($projects)) }}
                </p>
            @endif
        </header>

        @if(!$query || strlen($query) < 2)
            {{-- Empty Query State --}}
            <div class="text-center py-16">
                <p class="text-rose-pine-muted text-lg">Please enter at least 2 characters to search.</p>
            </div>
        @elseif(count($posts) === 0 && count($projects) === 0)
            {{-- No Results State --}}
            <div class="text-center py-16">
                <p class="text-rose-pine-muted text-lg">No results found for "{{ $query }}"</p>
                <p class="text-rose-pine-subtle text-sm mt-2">Try different keywords or browse all content.</p>
            </div>
        @else
            {{-- Posts Section --}}
            @if(count($posts) > 0)
                <section class="mb-16">
                    <h2 class="text-2xl font-bold text-rose-pine-text mb-6">
                        Posts ({{ $posts->total() }})
                    </h2>

                    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($posts as $post)
                            <article class="group">
                                <a href="{{ route('posts.show', $post->slug) }}" class="block">
                                    <div class="p-6 rounded-lg bg-rose-pine-surface group-hover:bg-rose-pine-overlay transition-colors">
                                        <h3 class="text-xl font-bold text-rose-pine-text group-hover:text-rose-pine-rose mb-2">
                                            {{ $post->title }}
                                        </h3>

                                        <div class="flex items-center gap-4 text-sm text-rose-pine-muted mb-3">
                                            <time datetime="{{ $post->published_at?->toIso8601String() }}">
                                                {{ $post->published_at?->format('M d, Y') }}
                                            </time>

                                            @if($post->category)
                                                <span class="px-2 py-1 rounded bg-rose-pine-overlay text-rose-pine-foam text-xs">
                                                    {{ $post->category->name }}
                                                </span>
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

                    @if($posts->hasPages())
                        <div class="mt-8">
                            {{ $posts->appends(['q' => $query])->links() }}
                        </div>
                    @endif
                </section>
            @endif

            {{-- Projects Section --}}
            @if(count($projects) > 0)
                <section>
                    <h2 class="text-2xl font-bold text-rose-pine-text mb-6">
                        Projects ({{ $projects->total() }})
                    </h2>

                    <div class="columns-1 sm:columns-2 lg:columns-3 gap-6 space-y-6">
                        @foreach($projects as $project)
                            <x-project-card :project="$project" />
                        @endforeach
                    </div>

                    @if($projects->hasPages())
                        <div class="mt-8">
                            {{ $projects->appends(['q' => $query])->links() }}
                        </div>
                    @endif
                </section>
            @endif
        @endif
    </div>
@endsection
