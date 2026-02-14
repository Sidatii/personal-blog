@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="mb-10">
        <h1 class="text-3xl font-bold text-rose-pine-text">Certifications</h1>
        <p class="mt-2 text-rose-pine-subtle">Professional credentials and completed courses.</p>
    </div>

    @if($featured->isNotEmpty())
    <section class="mb-10">
        <h2 class="text-sm font-semibold uppercase tracking-widest text-rose-pine-gold mb-4">Highlighted</h2>
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
        <h2 class="text-sm font-semibold uppercase tracking-widest text-rose-pine-muted mb-4">All Certifications</h2>
        @endif
        <div class="space-y-4">
            @foreach($standard as $cert)
                <x-certification-card :certification="$cert" />
            @endforeach
        </div>
    </section>
    @endif

    @if($featured->isEmpty() && $standard->isEmpty())
    <div class="text-center py-20 text-rose-pine-muted">
        <div class="text-5xl mb-4">🎓</div>
        <p class="text-lg">No certifications added yet.</p>
    </div>
    @endif
</div>
@endsection
