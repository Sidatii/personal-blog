@extends('layouts.admin')

@section('title', 'Activity Log')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-rose-pine-text">Activity Log</h1>
        <p class="text-rose-pine-subtle mt-2">Audit trail of all admin actions</p>
    </div>

    <!-- Filters -->
    <div class="mb-6 bg-rose-pine-surface rounded-lg p-4">
        <form method="GET" action="{{ route('admin.activity.index') }}" class="flex flex-wrap gap-4">
            <div class="min-w-[150px]">
                <select name="action"
                        class="w-full px-4 py-2 bg-rose-pine-base text-rose-pine-text border border-rose-pine-highlight-med rounded-lg focus:outline-none focus:border-rose-pine-gold">
                    <option value="">All Actions</option>
                    <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                    <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                    <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                    <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                </select>
            </div>
            <div class="min-w-[150px]">
                <select name="model_type"
                        class="w-full px-4 py-2 bg-rose-pine-base text-rose-pine-text border border-rose-pine-highlight-med rounded-lg focus:outline-none focus:border-rose-pine-gold">
                    <option value="">All Models</option>
                    <option value="App\Models\Post" {{ request('model_type') == 'App\Models\Post' ? 'selected' : '' }}>Posts</option>
                    <option value="App\Models\Category" {{ request('model_type') == 'App\Models\Category' ? 'selected' : '' }}>Categories</option>
                    <option value="App\Models\Tag" {{ request('model_type') == 'App\Models\Tag' ? 'selected' : '' }}>Tags</option>
                    <option value="App\Models\Project" {{ request('model_type') == 'App\Models\Project' ? 'selected' : '' }}>Projects</option>
                    <option value="App\Models\ContactSubmission" {{ request('model_type') == 'App\Models\ContactSubmission' ? 'selected' : '' }}>Contacts</option>
                </select>
            </div>
            <div class="min-w-[150px]">
                <select name="admin_id"
                        class="w-full px-4 py-2 bg-rose-pine-base text-rose-pine-text border border-rose-pine-highlight-med rounded-lg focus:outline-none focus:border-rose-pine-gold">
                    <option value="">All Admins</option>
                    @foreach($admins as $admin)
                    <option value="{{ $admin->id }}" {{ request('admin_id') == $admin->id ? 'selected' : '' }}>
                        {{ $admin->email }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[150px]">
                <input type="date"
                       name="date_from"
                       value="{{ request('date_from') }}"
                       placeholder="From date"
                       class="w-full px-4 py-2 bg-rose-pine-base text-rose-pine-text border border-rose-pine-highlight-med rounded-lg focus:outline-none focus:border-rose-pine-gold">
            </div>
            <div class="min-w-[150px]">
                <input type="date"
                       name="date_to"
                       value="{{ request('date_to') }}"
                       placeholder="To date"
                       class="w-full px-4 py-2 bg-rose-pine-base text-rose-pine-text border border-rose-pine-highlight-med rounded-lg focus:outline-none focus:border-rose-pine-gold">
            </div>
            <button type="submit"
                    class="px-6 py-2 bg-rose-pine-gold text-white rounded-lg hover:bg-opacity-80 transition">
                Filter
            </button>
            @if(request()->hasAny(['action', 'model_type', 'admin_id', 'date_from', 'date_to']))
            <a href="{{ route('admin.activity.index') }}"
               class="px-6 py-2 bg-rose-pine-highlight-med text-rose-pine-text rounded-lg hover:bg-opacity-80 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Activity Table -->
    <div class="bg-rose-pine-surface rounded-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-rose-pine-overlay border-b border-rose-pine-highlight-med">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Admin</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Model</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-pine-highlight-low">
                @forelse($activities as $activity)
                <tr class="hover:bg-rose-pine-overlay transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-pine-text">
                        <div>{{ $activity->created_at->format('M d, Y') }}</div>
                        <div class="text-rose-pine-subtle text-xs">{{ $activity->created_at->format('H:i:s') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-pine-text">
                        {{ $activity->admin->email }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($activity->action === 'created')
                        <span class="px-2 py-1 bg-rose-pine-foam bg-opacity-20 text-rose-pine-foam rounded text-xs">
                            Created
                        </span>
                        @elseif($activity->action === 'updated')
                        <span class="px-2 py-1 bg-rose-pine-gold bg-opacity-20 text-rose-pine-gold rounded text-xs">
                            Updated
                        </span>
                        @elseif($activity->action === 'deleted')
                        <span class="px-2 py-1 bg-rose-pine-love bg-opacity-20 text-rose-pine-love rounded text-xs">
                            Deleted
                        </span>
                        @elseif($activity->action === 'login')
                        <span class="px-2 py-1 bg-rose-pine-gold bg-opacity-20 text-rose-pine-gold rounded text-xs">
                            Login
                        </span>
                        @elseif($activity->action === 'logout')
                        <span class="px-2 py-1 bg-rose-pine-subtle bg-opacity-20 text-rose-pine-subtle rounded text-xs">
                            Logout
                        </span>
                        @else
                        <span class="px-2 py-1 bg-rose-pine-highlight-med text-rose-pine-text rounded text-xs">
                            {{ ucfirst($activity->action) }}
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-pine-text">
                        @if($activity->model_type)
                        <span class="text-rose-pine-subtle text-xs">
                            {{ class_basename($activity->model_type) }}
                            @if($activity->model_id)
                            #{{ $activity->model_id }}
                            @endif
                        </span>
                        @else
                        <span class="text-rose-pine-subtle">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-rose-pine-text">
                        {{ $activity->description }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-pine-subtle">
                        {{ $activity->ip_address ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-rose-pine-subtle">
                        No activity logs found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($activities->hasPages())
    <div class="mt-6">
        {{ $activities->links() }}
    </div>
    @endif
</div>
@endsection
