{{-- Projects Index Page --}}
{{-- Masonry grid layout with status filter --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        {{-- Page Header --}}
        <header class="mb-10">
            <h1 class="text-4xl font-bold text-rose-pine-text mb-4">Projects</h1>
            <p class="text-lg text-rose-pine-subtle">Browse my portfolio of projects showcasing web development, applications, and technical work.</p>
        </header>

        {{-- Status Filter Bar --}}
        <div class="mb-10">
            <div class="flex flex-wrap gap-2" x-data="{ currentStatus: '{{ $currentStatus ?? 'all' }}' }">
                {{-- All Filter --}}
                <a href="{{ route('projects.index') }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                   {{ is_null($currentStatus) ? 'bg-rose-pine-iris text-rose-pine-base' : 'bg-rose-pine-surface text-rose-pine-subtle hover:text-rose-pine-text' }}">
                    All
                </a>
                
                {{-- Active Filter --}}
                <a href="{{ route('projects.index', ['status' => 'active']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                   {{ $currentStatus === 'active' ? 'bg-rose-pine-iris text-rose-pine-base' : 'bg-rose-pine-surface text-rose-pine-subtle hover:text-rose-pine-text' }}">
                    Active
                </a>
                
                {{-- Completed Filter --}}
                <a href="{{ route('projects.index', ['status' => 'completed']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                   {{ $currentStatus === 'completed' ? 'bg-rose-pine-iris text-rose-pine-base' : 'bg-rose-pine-surface text-rose-pine-subtle hover:text-rose-pine-text' }}">
                    Completed
                </a>
                
                {{-- In Progress Filter --}}
                <a href="{{ route('projects.index', ['status' => 'in-progress']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                   {{ $currentStatus === 'in-progress' ? 'bg-rose-pine-iris text-rose-pine-base' : 'bg-rose-pine-surface text-rose-pine-subtle hover:text-rose-pine-text' }}">
                    In Progress
                </a>
                
                {{-- Archived Filter --}}
                <a href="{{ route('projects.index', ['status' => 'archived']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                   {{ $currentStatus === 'archived' ? 'bg-rose-pine-iris text-rose-pine-base' : 'bg-rose-pine-surface text-rose-pine-subtle hover:text-rose-pine-text' }}">
                    Archived
                </a>
            </div>
        </div>

        {{-- Projects Masonry Grid --}}
        @if($projects->count() > 0)
            <div class="columns-1 sm:columns-2 lg:columns-3 gap-6 space-y-6">
                @foreach($projects as $project)
                    <x-project-card :project="$project" />
                @endforeach
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-16">
                <p class="text-rose-pine-muted text-lg">No projects to display.</p>
                @if($currentStatus)
                    <p class="text-rose-pine-subtle text-sm mt-2">
                        Try <a href="{{ route('projects.index') }}" class="text-rose-pine-iris hover:underline">viewing all projects</a>.
                    </p>
                @endif
            </div>
        @endif
    </div>
@endsection
