{{-- Blog Index Page --}}
@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Header: heading left, BlogService.java right --}}
    <div class="grid lg:grid-cols-2 gap-10 items-end mb-14">
        <div>
            <h1 class="text-4xl font-bold mb-3" style="color:var(--rose-pine-text)">Blog</h1>
            <p class="text-lg" style="color:var(--rose-pine-subtle)">Engineering, written down.</p>
        </div>
        <div class="rounded-xl overflow-hidden border" style="border-color:var(--rose-pine-overlay);background:var(--rose-pine-surface)">
            <div class="flex items-center gap-2 px-4 py-3 border-b" style="border-color:var(--rose-pine-overlay);background:var(--rose-pine-overlay)">
                <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-love)"></span>
                <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-gold)"></span>
                <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-foam)"></span>
                <span class="ml-3 text-xs font-mono" style="color:var(--rose-pine-muted)">BlogService.java</span>
            </div>
            <pre class="p-5 text-xs sm:text-sm leading-6 overflow-x-auto" style="color:var(--rose-pine-text)"><span class="text-rose-pine-pine">public</span> <span class="text-rose-pine-iris">List</span>&lt;<span class="text-rose-pine-iris">Article</span>&gt; <span class="text-rose-pine-rose">findAll</span><span class="text-rose-pine-subtle">() {</span>
    <span class="text-rose-pine-pine">return</span> repo<span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">findAllPublished</span><span class="text-rose-pine-subtle">()</span>
        <span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">sorted</span><span class="text-rose-pine-subtle">(</span><span class="text-rose-pine-iris">Comparator</span><span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">reverseOrder</span><span class="text-rose-pine-subtle">())</span>
        <span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">toList</span><span class="text-rose-pine-subtle">();</span>
    <span class="text-rose-pine-muted">// Engineering, written down.</span>
<span class="text-rose-pine-subtle">}</span></pre>
        </div>
    </div>

    @if($posts->count() > 0)
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($posts as $post)
            <a href="{{ route('posts.show', $post->slug) }}" class="group block">
                <article class="p-6 rounded-xl border h-full transition-all group-hover:ring-1"
                         style="background:var(--rose-pine-surface);border-color:var(--rose-pine-overlay);--tw-ring-color:var(--rose-pine-overlay)">
                    <div class="flex flex-wrap items-center gap-2 text-xs mb-3">
                        <time class="font-mono" style="color:var(--rose-pine-muted)"
                              datetime="{{ $post->published_at?->toIso8601String() }}">
                            {{ $post->published_at?->format('M d, Y') }}
                        </time>
                        @if($post->tags->count())
                            @foreach($post->tags->take(2) as $tag)
                                <span class="px-2 py-0.5 rounded-full"
                                      style="background:var(--rose-pine-overlay);color:var(--rose-pine-iris)">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        @endif
                    </div>
                    <h2 class="text-lg font-bold mb-2 leading-snug transition-opacity group-hover:opacity-80"
                        style="color:var(--rose-pine-text)">
                        {{ $post->title }}
                    </h2>
                    @if($post->excerpt)
                        <p class="text-sm leading-relaxed line-clamp-3" style="color:var(--rose-pine-subtle)">
                            {{ $post->excerpt }}
                        </p>
                    @endif
                </article>
            </a>
            @endforeach
        </div>

        <div class="mt-12">
            {{ $posts->links() }}
        </div>
    @else
        <div class="text-center py-20">
            <p class="font-mono text-sm" style="color:var(--rose-pine-muted)">// No posts yet. Check back soon.</p>
        </div>
    @endif

</div>
@endsection
