@extends('layouts.admin')

@section('title', 'Projects')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-rose-pine-text">Projects</h1>
        <a href="{{ route('admin.projects.create') }}"
           class="px-4 py-2 bg-rose-pine-gold text-rose-pine-base rounded-lg hover:bg-opacity-80 transition font-semibold">
            Create New Project
        </a>
    </div>


    <!-- Status Filter -->
    <div class="mb-6 bg-rose-pine-surface rounded-lg p-4">
        <form method="GET" action="{{ route('admin.projects.index') }}" class="flex flex-wrap gap-4">
            <div class="min-w-[150px]">
                <select name="status"
                        class="w-full px-4 py-2 bg-rose-pine-base text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="in-progress" {{ request('status') == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
            <button type="submit"
                    class="px-6 py-2 bg-rose-pine-gold text-rose-pine-base rounded-lg hover:bg-opacity-80 transition">
                Filter
            </button>
            @if(request('status'))
            <a href="{{ route('admin.projects.index') }}"
               class="px-6 py-2 bg-rose-pine-subtle text-rose-pine-base rounded-lg hover:bg-opacity-80 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Projects Table -->
    <div class="bg-rose-pine-surface rounded-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-rose-pine-overlay border-b border-rose-pine-base/30">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Thumbnail</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Featured</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-pine-highlight-low">
                @forelse($projects as $project)
                <tr class="hover:bg-rose-pine-overlay transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($project->thumbnail)
                        <img src="{{ $project->thumbnail }}" alt="{{ $project->title }}" class="w-16 h-16 object-cover rounded">
                        @else
                        <div class="w-16 h-16 bg-rose-pine-overlay rounded flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-rose-pine-subtle" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-rose-pine-text">
                        <div class="font-medium">{{ $project->title }}</div>
                        <div class="text-rose-pine-subtle text-xs mt-1">{{ $project->slug }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($project->status === 'active')
                        <span class="px-3 py-1 bg-rose-pine-foam text-rose-pine-base rounded-full text-xs font-semibold">
                            Active
                        </span>
                        @elseif($project->status === 'completed')
                        <span class="px-3 py-1 bg-rose-pine-love text-rose-pine-base rounded-full text-xs font-semibold">
                            Completed
                        </span>
                        @elseif($project->status === 'in-progress')
                        <span class="px-3 py-1 bg-rose-pine-gold text-rose-pine-base rounded-full text-xs font-semibold">
                            In Progress
                        </span>
                        @else
                        <span class="px-3 py-1 bg-rose-pine-muted text-rose-pine-base rounded-full text-xs font-semibold">
                            Archived
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-pine-text">
                        @if($project->is_featured)
                        <span class="text-rose-pine-love">★ Featured</span>
                        @else
                        <span class="text-rose-pine-subtle">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.projects.edit', $project) }}"
                           class="text-rose-pine-gold hover:text-rose-pine-foam mr-3">Edit</a>
                        <form action="{{ route('admin.projects.destroy', $project) }}"
                              method="POST"
                              class="inline"
                              onsubmit="return confirm('Are you sure you want to delete this project?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-pine-love hover:text-rose-pine-gold">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-rose-pine-subtle">
                        No projects found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($projects->hasPages())
        <div class="mt-6">
            {{ $projects->appends(request()->query())->links() }}
        </div>
    @elseif($projects->total() > 0)
        <div class="mt-6">
            <p class="text-sm text-rose-pine-subtle">
                Showing all {{ $projects->total() }} {{ Str::plural('project', $projects->total()) }}
            </p>
        </div>
    @endif
</div>
@endsection
