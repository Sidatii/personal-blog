@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Section 1: Hero/Bio --}}
    <section class="py-12 border-b border-rose-pine-overlay">
        <div class="md:flex md:items-start md:gap-12">
            {{-- Profile Photo/Placeholder --}}
            <div class="flex-shrink-0 mb-8 md:mb-0">
                @if($portfolio['photo'])
                    <img
                        src="{{ asset($portfolio['photo']) }}"
                        alt="{{ $portfolio['name'] }}"
                        class="w-48 h-48 rounded-full object-cover border-2 border-rose-pine-overlay shadow-lg"
                    >
                @else
                    <div class="w-48 h-48 rounded-full bg-rose-pine-surface border-2 border-rose-pine-overlay flex items-center justify-center">
                        {{-- User icon placeholder --}}
                        <svg class="w-24 h-24 text-rose-pine-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Bio Info --}}
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-rose-pine-text mb-2">
                    {{ $portfolio['name'] }}
                </h1>
                <p class="text-xl text-rose-pine-gold mb-4">
                    {{ $portfolio['title'] }}
                </p>
                <p class="text-rose-pine-subtle leading-relaxed">
                    {{ $portfolio['bio']['intro'] }}
                </p>
            </div>
        </div>

        {{-- Extended Bio --}}
        @if(isset($portfolio['bio']['extended']) && $portfolio['bio']['extended'])
            <div class="mt-8 max-w-3xl">
                <p class="text-rose-pine-subtle leading-relaxed">
                    {{ $portfolio['bio']['extended'] }}
                </p>
            </div>
        @endif
    </section>

    {{-- Section 2: Skills & Expertise --}}
    <section class="py-12 border-b border-rose-pine-overlay">
        <h2 class="text-2xl font-bold text-rose-pine-text mb-8">
            Skills & Expertise
        </h2>

        @include('components.tech-stack-badges', [
            'categories' => $portfolio['tech_stack'] ?? []
        ])
    </section>

    {{-- Section 3: Interests & Hobbies --}}
    <section class="py-12">
        <h2 class="text-2xl font-bold text-rose-pine-text mb-8">
            Interests & Hobbies
        </h2>

        <div class="flex flex-wrap gap-3">
            @foreach($portfolio['interests'] ?? [] as $interest)
                <span class="bg-rose-pine-surface text-rose-pine-subtle px-4 py-2 rounded-full text-sm border border-rose-pine-overlay">
                    {{ $interest }}
                </span>
            @endforeach
        </div>
    </section>

</div>
@endsection
