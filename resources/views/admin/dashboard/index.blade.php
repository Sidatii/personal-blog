@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Projects -->
        <div class="bg-gradient-to-br from-rose-pine-foam/90 to-rose-pine-foam rounded-lg p-6 hover:shadow-lg transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-800 text-sm font-semibold uppercase tracking-wide">Total Projects</p>
                    <p class="text-4xl font-bold text-gray-900 mt-2">{{ $stats['total_projects'] }}</p>
                </div>
                <div class="w-14 h-14 bg-black/10 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.projects.index') }}" class="text-gray-900 text-sm mt-4 inline-block hover:underline font-semibold">
                Manage projects →
            </a>
        </div>

        <!-- Total Posts (git-based, read-only) -->
        <div class="bg-gradient-to-br from-rose-pine-gold/90 to-rose-pine-gold rounded-lg p-6 hover:shadow-lg transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-800 text-sm font-semibold uppercase tracking-wide">Blog Posts</p>
                    <p class="text-4xl font-bold text-gray-900 mt-2">{{ $stats['total_posts'] }}</p>
                </div>
                <div class="w-14 h-14 bg-black/10 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <p class="text-gray-800 text-xs mt-4 font-semibold">Git-based publishing</p>
        </div>

        <!-- Unread Contacts -->
        <div class="bg-gradient-to-br from-rose-pine-love/90 to-rose-pine-love rounded-lg p-6 hover:shadow-lg transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-800 text-sm font-semibold uppercase tracking-wide">Unread Contacts</p>
                    <p class="text-4xl font-bold text-gray-900 mt-2">{{ $stats['unread_contacts'] }}</p>
                </div>
                <div class="w-14 h-14 bg-black/10 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.contacts.index') }}" class="text-gray-900 text-sm mt-4 inline-block hover:underline font-semibold">
                View contacts →
            </a>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="grid grid-cols-1 gap-6">
        <!-- Recent Contact Submissions -->
        <div class="bg-rose-pine-surface border border-rose-pine-base/20 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-rose-pine-text mb-4">Recent Contact Submissions</h3>
            @if($recent_contacts->count() > 0)
                <div class="space-y-3">
                    @foreach($recent_contacts as $contact)
                        <div class="flex items-start justify-between py-3 border-b border-rose-pine-base/20 last:border-0">
                            <div class="flex-1">
                                <a href="{{ route('admin.contacts.show', $contact) }}" class="text-rose-pine-text hover:text-rose-pine-foam transition-colors">
                                    {{ $contact->name }}
                                </a>
                                <p class="text-sm text-rose-pine-subtle mt-1">{{ $contact->subject }}</p>
                                <p class="text-sm text-rose-pine-subtle">
                                    {{ $contact->created_at->diffForHumans() }}
                                    @if(!$contact->is_read)
                                        <span class="text-rose-pine-love">• Unread</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('admin.contacts.index') }}" class="text-rose-pine-foam text-sm mt-4 inline-block hover:underline">
                    View all contacts →
                </a>
            @else
                <p class="text-rose-pine-subtle">No contact submissions yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
