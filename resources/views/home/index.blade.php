{{-- Landing Page --}}
{{-- Index [0]: not out of bounds — exactly at the start. --}}
{{-- Java code panels carry the brand language across every section. --}}
@extends('layouts.app')

@section('content')

{{-- ═══════════════════════════════════════════════════════ HERO ══ --}}
<section class="relative min-h-[calc(100vh-4rem)] flex items-center px-4 py-16 sm:py-20">

    {{-- Ambient glow --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
        <div style="position:absolute;top:35%;left:50%;transform:translate(-50%,-50%);width:700px;height:400px;background:radial-gradient(ellipse,var(--rose-pine-gold) 0%,transparent 70%);opacity:.04;filter:blur(60px)"></div>
    </div>

    <div class="relative max-w-6xl mx-auto w-full">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

            {{-- Left: Brand copy --}}
            <div>
                <div class="mb-8">
                    <img src="/oob-white.svg" alt="Out of Bounds" class="h-14 w-auto dark:block hidden">
                    <img src="/oob-black.svg" alt="Out of Bounds" class="h-14 w-auto dark:hidden block">
                </div>
                <h1 class="text-4xl sm:text-5xl font-bold leading-tight mb-6" style="color:var(--rose-pine-text)">
                    Where code goes<br>
                    <span style="color:var(--rose-pine-gold)">beyond the bounds.</span>
                </h1>
                <p class="text-lg leading-relaxed mb-10" style="color:var(--rose-pine-subtle)">
                    A software engineering space for deep-dives, real projects,
                    and ideas that don't fit neatly inside the array.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('posts.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold text-sm transition-opacity hover:opacity-85"
                       style="background:var(--rose-pine-gold);color:#111827">
                        Read the Blog
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                    <a href="{{ route('projects.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold text-sm transition-colors"
                       style="background:var(--rose-pine-overlay);color:var(--rose-pine-text)">
                        See Projects
                    </a>
                </div>
            </div>

            {{-- Right: OutOfBounds.java — shown on mobile AND desktop --}}
            <div class="mt-10 lg:mt-0">
                <div class="rounded-xl overflow-hidden border" style="border-color:var(--rose-pine-overlay);background:var(--rose-pine-surface)">
                    <div class="flex items-center gap-2 px-4 py-3 border-b" style="border-color:var(--rose-pine-overlay);background:var(--rose-pine-overlay)">
                        <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-love)"></span>
                        <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-gold)"></span>
                        <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-foam)"></span>
                        <span class="ml-3 text-xs font-mono" style="color:var(--rose-pine-muted)">OutOfBounds.java</span>
                    </div>
                    <pre class="p-5 text-xs sm:text-sm leading-6 overflow-x-auto" style="color:var(--rose-pine-text)"><span class="text-rose-pine-pine">package</span> com.oob<span class="text-rose-pine-subtle">;</span>

<span class="text-rose-pine-pine">public</span> <span class="text-rose-pine-pine">class</span> <span class="text-rose-pine-iris">OutOfBounds</span> <span class="text-rose-pine-subtle">{</span>

    <span class="text-rose-pine-pine">public</span> <span class="text-rose-pine-pine">static</span> <span class="text-rose-pine-pine">void</span> <span class="text-rose-pine-rose">main</span><span class="text-rose-pine-subtle">(</span><span class="text-rose-pine-pine">String</span><span class="text-rose-pine-subtle">[]</span> args<span class="text-rose-pine-subtle">) {</span>
        <span class="text-rose-pine-iris">Router</span><span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">get</span><span class="text-rose-pine-subtle">(</span><span class="text-rose-pine-gold">"/"</span><span class="text-rose-pine-subtle">,</span> ctx <span class="text-rose-pine-subtle">-></span> <span class="text-rose-pine-subtle">{</span>
            <span class="text-rose-pine-pine">return</span> <span class="text-rose-pine-iris">Platform</span>
                <span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">init</span><span class="text-rose-pine-subtle">()</span>   <span class="text-rose-pine-muted">// index [0]</span>
                <span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">load</span><span class="text-rose-pine-subtle">(ctx);</span>
        <span class="text-rose-pine-subtle">});</span>
        <span class="text-rose-pine-muted">// Not out of bounds.</span>
        <span class="text-rose-pine-muted">// Exactly at the start.</span>
    <span class="text-rose-pine-subtle">}</span>
<span class="text-rose-pine-subtle">}</span></pre>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ════════════════════════════════════════════ WHAT'S HERE ══ --}}
<section class="py-20 border-t" style="border-color:var(--rose-pine-overlay)">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-10">
            <h2 class="text-2xl font-bold mb-2" style="color:var(--rose-pine-text)">What's here</h2>
            <p style="color:var(--rose-pine-subtle)">Three sections. All signal, no noise.</p>
        </div>

        <div class="grid lg:grid-cols-2 gap-10 items-start">

            {{-- Left: 3 section cards --}}
            <div class="grid grid-cols-1 gap-4">

                {{-- Blog --}}
                <a href="{{ route('posts.index') }}"
                   class="group flex gap-4 p-5 rounded-xl border transition-all hover:ring-1"
                   style="background:var(--rose-pine-surface);border-color:var(--rose-pine-overlay);--tw-ring-color:var(--rose-pine-gold)">
                    <div class="w-10 h-10 flex-shrink-0 rounded-lg flex items-center justify-center"
                         style="background:color-mix(in srgb,var(--rose-pine-gold) 12%,transparent)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--rose-pine-gold)">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold mb-1 transition-opacity group-hover:opacity-75" style="color:var(--rose-pine-text)">Blog</h3>
                        <p class="text-sm leading-relaxed" style="color:var(--rose-pine-subtle)">Deep-dives into backend engineering, security research, system design, and the craft of software.</p>
                    </div>
                </a>

                {{-- Projects --}}
                <a href="{{ route('projects.index') }}"
                   class="group flex gap-4 p-5 rounded-xl border transition-all hover:ring-1"
                   style="background:var(--rose-pine-surface);border-color:var(--rose-pine-overlay);--tw-ring-color:var(--rose-pine-iris)">
                    <div class="w-10 h-10 flex-shrink-0 rounded-lg flex items-center justify-center"
                         style="background:color-mix(in srgb,var(--rose-pine-iris) 12%,transparent)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--rose-pine-iris)">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold mb-1 transition-opacity group-hover:opacity-75" style="color:var(--rose-pine-text)">Projects</h3>
                        <p class="text-sm leading-relaxed" style="color:var(--rose-pine-subtle)">Open-source tools and experiments built to solve real problems.</p>
                    </div>
                </a>

                {{-- Certifications --}}
                <a href="{{ route('certifications.index') }}"
                   class="group flex gap-4 p-5 rounded-xl border transition-all hover:ring-1"
                   style="background:var(--rose-pine-surface);border-color:var(--rose-pine-overlay);--tw-ring-color:var(--rose-pine-pine)">
                    <div class="w-10 h-10 flex-shrink-0 rounded-lg flex items-center justify-center"
                         style="background:color-mix(in srgb,var(--rose-pine-pine) 12%,transparent)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--rose-pine-pine)">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold mb-1 transition-opacity group-hover:opacity-75" style="color:var(--rose-pine-text)">Certifications</h3>
                        <p class="text-sm leading-relaxed" style="color:var(--rose-pine-subtle)">Validated credentials and professional certifications from the journey so far.</p>
                    </div>
                </a>

            </div>

            {{-- Right: Platform.java — the interface contract --}}
            <div class="mt-6 lg:mt-0">
                <div class="rounded-xl overflow-hidden border" style="border-color:var(--rose-pine-overlay);background:var(--rose-pine-surface)">
                    <div class="flex items-center gap-2 px-4 py-3 border-b" style="border-color:var(--rose-pine-overlay);background:var(--rose-pine-overlay)">
                        <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-love)"></span>
                        <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-gold)"></span>
                        <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-foam)"></span>
                        <span class="ml-3 text-xs font-mono" style="color:var(--rose-pine-muted)">Platform.java</span>
                    </div>
                    <pre class="p-5 text-xs sm:text-sm leading-6 overflow-x-auto" style="color:var(--rose-pine-text)"><span class="text-rose-pine-muted">/** The platform contract. */</span>
<span class="text-rose-pine-pine">public</span> <span class="text-rose-pine-pine">interface</span> <span class="text-rose-pine-iris">Platform</span> <span class="text-rose-pine-subtle">{</span>

    <span class="text-rose-pine-muted">/**</span>
<span class="text-rose-pine-muted">     * Writes that go deep.</span>
<span class="text-rose-pine-muted">     * @return articles on engineering</span>
<span class="text-rose-pine-muted">     */</span>
    <span class="text-rose-pine-iris">List</span><span class="text-rose-pine-subtle">&lt;</span><span class="text-rose-pine-iris">Article</span><span class="text-rose-pine-subtle">&gt;</span> <span class="text-rose-pine-rose">blog</span><span class="text-rose-pine-subtle">();</span>

    <span class="text-rose-pine-muted">/**</span>
<span class="text-rose-pine-muted">     * Code that ships.</span>
<span class="text-rose-pine-muted">     * @return real, built things</span>
<span class="text-rose-pine-muted">     */</span>
    <span class="text-rose-pine-iris">List</span><span class="text-rose-pine-subtle">&lt;</span><span class="text-rose-pine-iris">Project</span><span class="text-rose-pine-subtle">&gt;</span> <span class="text-rose-pine-rose">projects</span><span class="text-rose-pine-subtle">();</span>

    <span class="text-rose-pine-muted">/**</span>
<span class="text-rose-pine-muted">     * Skills that compound.</span>
<span class="text-rose-pine-muted">     * @return validated expertise</span>
<span class="text-rose-pine-muted">     */</span>
    <span class="text-rose-pine-iris">List</span><span class="text-rose-pine-subtle">&lt;</span><span class="text-rose-pine-iris">Certificate</span><span class="text-rose-pine-subtle">&gt;</span> <span class="text-rose-pine-rose">certifications</span><span class="text-rose-pine-subtle">();</span>
<span class="text-rose-pine-subtle">}</span></pre>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ════════════════════════════════════ LATEST POSTS ══ --}}
@if($recentPosts->count())
<section class="py-20 border-t" style="border-color:var(--rose-pine-overlay)">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section header — text left, Feed.java right --}}
        <div class="grid lg:grid-cols-2 gap-10 items-start mb-12">

            <div class="flex flex-col justify-start">
                <h2 class="text-2xl font-bold mb-2" style="color:var(--rose-pine-text)">Latest posts</h2>
                <p class="mb-6" style="color:var(--rose-pine-subtle)">Fresh off the stack.</p>
                <a href="{{ route('posts.index') }}"
                   class="inline-flex items-center gap-2 text-sm font-semibold w-fit transition-opacity hover:opacity-70"
                   style="color:var(--rose-pine-pine)">
                    View all posts →
                </a>
            </div>

            {{-- Feed.java --}}
            <div>
                <div class="rounded-xl overflow-hidden border" style="border-color:var(--rose-pine-overlay);background:var(--rose-pine-surface)">
                    <div class="flex items-center gap-2 px-4 py-3 border-b" style="border-color:var(--rose-pine-overlay);background:var(--rose-pine-overlay)">
                        <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-love)"></span>
                        <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-gold)"></span>
                        <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-foam)"></span>
                        <span class="ml-3 text-xs font-mono" style="color:var(--rose-pine-muted)">Feed.java</span>
                    </div>
                    <pre class="p-5 text-xs sm:text-sm leading-6 overflow-x-auto" style="color:var(--rose-pine-text)"><span class="text-rose-pine-pine">public</span> <span class="text-rose-pine-iris">List</span><span class="text-rose-pine-subtle">&lt;</span><span class="text-rose-pine-iris">Post</span><span class="text-rose-pine-subtle">&gt;</span> <span class="text-rose-pine-rose">latest</span><span class="text-rose-pine-subtle">(</span><span class="text-rose-pine-pine">int</span> limit<span class="text-rose-pine-subtle">) {</span>
    <span class="text-rose-pine-pine">return</span> postRepo<span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">stream</span><span class="text-rose-pine-subtle">()</span>
        <span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">filter</span><span class="text-rose-pine-subtle">(</span><span class="text-rose-pine-iris">Post</span><span class="text-rose-pine-subtle">::</span>isPublished<span class="text-rose-pine-subtle">)</span>
        <span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">sorted</span><span class="text-rose-pine-subtle">(</span><span class="text-rose-pine-iris">Comparator</span><span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">reverseOrder</span><span class="text-rose-pine-subtle">())</span>
        <span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">limit</span><span class="text-rose-pine-subtle">(</span>limit<span class="text-rose-pine-subtle">)</span>
        <span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">toList</span><span class="text-rose-pine-subtle">();</span>
    <span class="text-rose-pine-muted">// Fresh off the stack.</span>
<span class="text-rose-pine-subtle">}</span></pre>
                </div>
            </div>

        </div>

        {{-- Post cards --}}
        <div class="grid gap-5 md:grid-cols-3">
            @foreach($recentPosts as $post)
            <a href="{{ route('posts.show', $post->slug) }}" class="group block">
                <article class="p-5 rounded-xl border h-full transition-all group-hover:ring-1"
                         style="background:var(--rose-pine-surface);border-color:var(--rose-pine-overlay);--tw-ring-color:var(--rose-pine-overlay)">
                    <div class="flex items-center gap-3 mb-3">
                        <time class="text-xs font-mono" style="color:var(--rose-pine-muted)"
                              datetime="{{ $post->published_at?->toIso8601String() }}">
                            {{ $post->published_at?->format('M d, Y') }}
                        </time>
                        @if($post->tags->count())
                            <span class="text-xs px-2 py-0.5 rounded-full"
                                  style="background:var(--rose-pine-overlay);color:var(--rose-pine-iris)">
                                {{ $post->tags->first()->name }}
                            </span>
                        @endif
                    </div>
                    <h3 class="font-bold leading-snug mb-2 transition-opacity group-hover:opacity-80"
                        style="color:var(--rose-pine-text)">
                        {{ $post->title }}
                    </h3>
                    @if($post->excerpt)
                        <p class="text-sm leading-relaxed line-clamp-3" style="color:var(--rose-pine-subtle)">
                            {{ $post->excerpt }}
                        </p>
                    @endif
                </article>
            </a>
            @endforeach
        </div>

    </div>
</section>
@endif


@endsection
