{{-- Projects Index Page --}}
@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Header: heading + filters left, Portfolio.java right --}}
    <div class="grid lg:grid-cols-2 gap-10 items-start mb-14">
        <div>
            <h1 class="text-4xl font-bold mb-3" style="color:var(--rose-pine-text)">Projects</h1>
            <p class="text-lg mb-8" style="color:var(--rose-pine-subtle)">Code that shipped.</p>

            {{-- Status filters --}}
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('projects.index') }}"
                   class="px-4 py-1.5 rounded-lg text-sm font-medium transition-colors"
                   style="{{ is_null($currentStatus) ? 'background:var(--rose-pine-gold);color:#111827' : 'background:var(--rose-pine-surface);color:var(--rose-pine-subtle)' }}">
                    All
                </a>
                @foreach(['active' => 'Active', 'in-progress' => 'In Progress', 'completed' => 'Completed', 'archived' => 'Archived'] as $key => $label)
                <a href="{{ route('projects.index', ['status' => $key]) }}"
                   class="px-4 py-1.5 rounded-lg text-sm font-medium transition-colors"
                   style="{{ $currentStatus === $key ? 'background:var(--rose-pine-gold);color:#111827' : 'background:var(--rose-pine-surface);color:var(--rose-pine-subtle)' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>
        </div>

        <div class="rounded-xl overflow-hidden border" style="border-color:var(--rose-pine-overlay);background:var(--rose-pine-surface)">
            <div class="flex items-center gap-2 px-4 py-3 border-b" style="border-color:var(--rose-pine-overlay);background:var(--rose-pine-overlay)">
                <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-love)"></span>
                <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-gold)"></span>
                <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-foam)"></span>
                <span class="ml-3 text-xs font-mono" style="color:var(--rose-pine-muted)">Portfolio.java</span>
            </div>
            <pre class="p-5 text-xs sm:text-sm leading-6 overflow-x-auto" style="color:var(--rose-pine-text)"><span class="text-rose-pine-pine">public</span> <span class="text-rose-pine-iris">List</span>&lt;<span class="text-rose-pine-iris">Project</span>&gt; <span class="text-rose-pine-rose">all</span><span class="text-rose-pine-subtle">(</span><span class="text-rose-pine-iris">@Nullable</span> <span class="text-rose-pine-pine">String</span> status<span class="text-rose-pine-subtle">) {</span>
    <span class="text-rose-pine-pine">return</span> projects<span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">stream</span><span class="text-rose-pine-subtle">()</span>
        <span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">filter</span><span class="text-rose-pine-subtle">(</span>p <span class="text-rose-pine-subtle">-></span> status <span class="text-rose-pine-subtle">==</span> <span class="text-rose-pine-pine">null</span>
            <span class="text-rose-pine-subtle">||</span> p<span class="text-rose-pine-subtle">.</span>status<span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">equals</span><span class="text-rose-pine-subtle">(</span>status<span class="text-rose-pine-subtle">))</span>
        <span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">toList</span><span class="text-rose-pine-subtle">();</span>
    <span class="text-rose-pine-muted">// Code that shipped.</span>
<span class="text-rose-pine-subtle">}</span></pre>
        </div>
    </div>

    @if($projects->count() > 0)
        <div class="columns-1 sm:columns-2 lg:columns-3 gap-6 space-y-6">
            @foreach($projects as $project)
                <x-project-card :project="$project" />
            @endforeach
        </div>
    @else
        <div class="text-center py-20">
            <p class="font-mono text-sm" style="color:var(--rose-pine-muted)">// No projects to display.</p>
            @if($currentStatus)
                <p class="text-sm mt-3" style="color:var(--rose-pine-subtle)">
                    Try <a href="{{ route('projects.index') }}" style="color:var(--rose-pine-gold)">viewing all projects</a>.
                </p>
            @endif
        </div>
    @endif

</div>
@endsection
