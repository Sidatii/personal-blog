{{-- Search Results Page --}}
@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Header: heading left, SearchIndex.java right --}}
    <div class="grid lg:grid-cols-2 gap-10 items-end mb-14">
        <div>
            <h1 class="text-4xl font-bold mb-3" style="color:var(--rose-pine-text)">
                @if($query)
                    Results for <span style="color:var(--rose-pine-gold)">"{{ $query }}"</span>
                @else
                    Search
                @endif
            </h1>
            @if($query && (count($posts) > 0 || count($projects) > 0))
                <p class="text-lg" style="color:var(--rose-pine-subtle)">
                    {{ count($posts) }} {{ Str::plural('post', count($posts)) }}
                    &middot; {{ count($projects) }} {{ Str::plural('project', count($projects)) }}
                </p>
            @else
                <p class="text-lg" style="color:var(--rose-pine-subtle)">Finding signal in noise.</p>
            @endif
        </div>
        <div class="rounded-xl overflow-hidden border" style="border-color:var(--rose-pine-overlay);background:var(--rose-pine-surface)">
            <div class="flex items-center gap-2 px-4 py-3 border-b" style="border-color:var(--rose-pine-overlay);background:var(--rose-pine-overlay)">
                <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-love)"></span>
                <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-gold)"></span>
                <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-foam)"></span>
                <span class="ml-3 text-xs font-mono" style="color:var(--rose-pine-muted)">SearchIndex.java</span>
            </div>
            <pre class="p-5 text-xs sm:text-sm leading-6 overflow-x-auto" style="color:var(--rose-pine-text)"><span class="text-rose-pine-pine">public</span> <span class="text-rose-pine-iris">SearchResult</span> <span class="text-rose-pine-rose">query</span><span class="text-rose-pine-subtle">(</span><span class="text-rose-pine-pine">String</span> q<span class="text-rose-pine-subtle">) {</span>
    <span class="text-rose-pine-pine">return</span> index<span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">search</span><span class="text-rose-pine-subtle">(</span>q<span class="text-rose-pine-subtle">)</span>
        <span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">across</span><span class="text-rose-pine-subtle">(</span><span class="text-rose-pine-iris">Post</span><span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-pine">class</span><span class="text-rose-pine-subtle">,</span>
                <span class="text-rose-pine-iris">Project</span><span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-pine">class</span><span class="text-rose-pine-subtle">)</span>
        <span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">ranked</span><span class="text-rose-pine-subtle">();</span>
    <span class="text-rose-pine-muted">// Finding signal in noise.</span>
<span class="text-rose-pine-subtle">}</span></pre>
        </div>
    </div>

    @if(!$query || strlen($query) < 2)
        <div class="text-center py-16">
            <p class="font-mono text-sm" style="color:var(--rose-pine-muted)">// Enter at least 2 characters to search.</p>
        </div>
    @elseif(count($posts) === 0 && count($projects) === 0)
        <div class="text-center py-16">
            <p class="font-mono text-sm mb-2" style="color:var(--rose-pine-muted)">// No results for "{{ $query }}"</p>
            <p class="text-sm" style="color:var(--rose-pine-subtle)">Try different keywords or browse all content.</p>
        </div>
    @else
        {{-- Posts --}}
        @if(count($posts) > 0)
        <section class="mb-16">
            <p class="text-xs font-mono uppercase tracking-widest mb-6" style="color:var(--rose-pine-muted)">// Posts ({{ $posts->total() }})</p>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($posts as $post)
                <a href="{{ route('posts.show', $post->slug) }}" class="group block">
                    <article class="p-5 rounded-xl border h-full transition-all group-hover:ring-1"
                             style="background:var(--rose-pine-surface);border-color:var(--rose-pine-overlay);--tw-ring-color:var(--rose-pine-overlay)">
                        <div class="flex items-center gap-3 text-xs mb-3">
                            <time class="font-mono" style="color:var(--rose-pine-muted)"
                                  datetime="{{ $post->published_at?->toIso8601String() }}">
                                {{ $post->published_at?->format('M d, Y') }}
                            </time>
                        </div>
                        <h3 class="font-bold leading-snug mb-2 transition-opacity group-hover:opacity-80"
                            style="color:var(--rose-pine-text)">
                            {{ $post->title }}
                        </h3>
                        @if($post->excerpt)
                            <p class="text-sm line-clamp-3" style="color:var(--rose-pine-subtle)">{{ $post->excerpt }}</p>
                        @endif
                    </article>
                </a>
                @endforeach
            </div>
            @if($posts->hasPages())
                <div class="mt-8">{{ $posts->appends(['q' => $query])->links() }}</div>
            @endif
        </section>
        @endif

        {{-- Projects --}}
        @if(count($projects) > 0)
        <section>
            <p class="text-xs font-mono uppercase tracking-widest mb-6" style="color:var(--rose-pine-muted)">// Projects ({{ $projects->total() }})</p>
            <div class="columns-1 sm:columns-2 lg:columns-3 gap-6 space-y-6">
                @foreach($projects as $project)
                    <x-project-card :project="$project" />
                @endforeach
            </div>
            @if($projects->hasPages())
                <div class="mt-8">{{ $projects->appends(['q' => $query])->links() }}</div>
            @endif
        </section>
        @endif
    @endif

</div>
@endsection
