@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Header: heading left, Credentials.java right --}}
    <div class="grid lg:grid-cols-2 gap-10 items-end mb-14">
        <div>
            <h1 class="text-4xl font-bold mb-3" style="color:var(--rose-pine-text)">Certifications</h1>
            <p class="text-lg" style="color:var(--rose-pine-subtle)">Skills that compound.</p>
        </div>
        <div class="rounded-xl overflow-hidden border" style="border-color:var(--rose-pine-overlay);background:var(--rose-pine-surface)">
            <div class="flex items-center gap-2 px-4 py-3 border-b" style="border-color:var(--rose-pine-overlay);background:var(--rose-pine-overlay)">
                <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-love)"></span>
                <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-gold)"></span>
                <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-foam)"></span>
                <span class="ml-3 text-xs font-mono" style="color:var(--rose-pine-muted)">Credentials.java</span>
            </div>
            <pre class="p-5 text-xs sm:text-sm leading-6 overflow-x-auto" style="color:var(--rose-pine-text)"><span class="text-rose-pine-pine">public</span> <span class="text-rose-pine-iris">Stream</span>&lt;<span class="text-rose-pine-iris">Certificate</span>&gt; <span class="text-rose-pine-rose">verified</span><span class="text-rose-pine-subtle">() {</span>
    <span class="text-rose-pine-pine">return</span> certs<span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">stream</span><span class="text-rose-pine-subtle">()</span>
        <span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">filter</span><span class="text-rose-pine-subtle">(</span><span class="text-rose-pine-iris">Certificate</span><span class="text-rose-pine-subtle">::</span>isValid<span class="text-rose-pine-subtle">)</span>
        <span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">sorted</span><span class="text-rose-pine-subtle">(</span><span class="text-rose-pine-rose">by</span><span class="text-rose-pine-subtle">(</span><span class="text-rose-pine-iris">Certificate</span><span class="text-rose-pine-subtle">::</span>issuedAt<span class="text-rose-pine-subtle">)</span>
                <span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">reversed</span><span class="text-rose-pine-subtle">());</span>
    <span class="text-rose-pine-muted">// Skills that compound.</span>
<span class="text-rose-pine-subtle">}</span></pre>
        </div>
    </div>

    @if($featured->isNotEmpty())
    <section class="mb-10">
        <p class="text-xs font-mono uppercase tracking-widest mb-5" style="color:var(--rose-pine-gold)">// Highlighted</p>
        <div class="space-y-4">
            @foreach($featured as $cert)
                <x-certification-card :certification="$cert" />
            @endforeach
        </div>
    </section>
    @endif

    @if($standard->isNotEmpty())
    <section>
        @if($featured->isNotEmpty())
        <p class="text-xs font-mono uppercase tracking-widest mb-5" style="color:var(--rose-pine-muted)">// All Certifications</p>
        @endif
        <div class="space-y-4">
            @foreach($standard as $cert)
                <x-certification-card :certification="$cert" />
            @endforeach
        </div>
    </section>
    @endif

    @if($featured->isEmpty() && $standard->isEmpty())
    <div class="text-center py-20">
        <p class="font-mono text-sm" style="color:var(--rose-pine-muted)">// No certifications added yet.</p>
    </div>
    @endif

</div>
@endsection
