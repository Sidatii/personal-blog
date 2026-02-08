@extends('layouts.admin')

@section('title', 'Contact Submissions')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-rose-pine-text">Contact Submissions</h1>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-rose-pine-foam border border-rose-pine-foam rounded-lg text-rose-pine-base">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filter Buttons -->
    <div class="mb-6 bg-rose-pine-surface rounded-lg p-4">
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('admin.contacts.index') }}"
               class="px-4 py-2 rounded-lg transition {{ !request('filter') ? 'bg-rose-pine-gold text-rose-pine-base' : 'bg-rose-pine-base text-rose-pine-text hover:bg-rose-pine-overlay' }}">
                All
                <span class="ml-1 text-sm opacity-75">({{ App\Models\ContactSubmission::count() }})</span>
            </a>
            <a href="{{ route('admin.contacts.index', ['filter' => 'unread']) }}"
               class="px-4 py-2 rounded-lg transition {{ request('filter') === 'unread' ? 'bg-rose-pine-gold text-rose-pine-base' : 'bg-rose-pine-base text-rose-pine-text hover:bg-rose-pine-overlay' }}">
                Unread
                <span class="ml-1 text-sm opacity-75">({{ App\Models\ContactSubmission::unread()->count() }})</span>
            </a>
            <a href="{{ route('admin.contacts.index', ['filter' => 'read']) }}"
               class="px-4 py-2 rounded-lg transition {{ request('filter') === 'read' ? 'bg-rose-pine-gold text-rose-pine-base' : 'bg-rose-pine-base text-rose-pine-text hover:bg-rose-pine-overlay' }}">
                Read
                <span class="ml-1 text-sm opacity-75">({{ App\Models\ContactSubmission::where('is_read', true)->count() }})</span>
            </a>
        </div>
    </div>

    <!-- Contacts Table -->
    <div class="bg-rose-pine-surface rounded-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-rose-pine-overlay border-b border-rose-pine-base/30">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-pine-highlight-low">
                @forelse($contacts as $contact)
                <tr class="hover:bg-rose-pine-overlay transition {{ !$contact->is_read ? 'bg-rose-pine-love bg-opacity-5' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-pine-text">
                        <div class="font-medium">{{ $contact->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-pine-text">
                        <a href="mailto:{{ $contact->email }}" class="text-rose-pine-gold hover:text-rose-pine-foam">
                            {{ $contact->email }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-sm text-rose-pine-text">
                        <div class="max-w-xs truncate">{{ $contact->subject }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-pine-subtle">
                        {{ $contact->created_at->format('M d, Y') }}
                        <div class="text-xs">{{ $contact->created_at->format('H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($contact->is_read)
                        <span class="px-2 py-1 bg-rose-pine-foam text-rose-pine-base rounded text-xs font-semibold">
                            Read
                        </span>
                        @else
                        <span class="px-2 py-1 bg-rose-pine-love text-rose-pine-base rounded text-xs font-semibold">
                            Unread
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.contacts.show', $contact) }}"
                           class="text-rose-pine-gold hover:text-rose-pine-foam mr-3">View</a>
                        <form action="{{ route('admin.contacts.mark-as-read', $contact) }}"
                              method="POST"
                              class="inline">
                            @csrf
                            <button type="submit" class="text-rose-pine-gold hover:text-rose-pine-gold mr-3">
                                {{ $contact->is_read ? 'Mark Unread' : 'Mark Read' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.contacts.destroy', $contact) }}"
                              method="POST"
                              class="inline"
                              onsubmit="return confirm('Are you sure you want to delete this contact submission?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-pine-love hover:text-rose-pine-gold">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-rose-pine-subtle">
                        No contact submissions found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($contacts->hasPages())
        <div class="mt-6">
            {{ $contacts->appends(request()->query())->links() }}
        </div>
    @elseif($contacts->total() > 0)
        <div class="mt-6">
            <p class="text-sm text-rose-pine-subtle">
                Showing all {{ $contacts->total() }} {{ Str::plural('contact', $contacts->total()) }}
            </p>
        </div>
    @endif
</div>
@endsection
