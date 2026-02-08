@extends('layouts.admin')

@section('title', 'Contact Submission')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-rose-pine-text">Contact Submission</h1>
            <p class="text-rose-pine-subtle mt-2">From {{ $contact->name }}</p>
        </div>
        <div class="flex gap-2">
            <form action="{{ route('admin.contacts.mark-as-read', $contact) }}" method="POST">
                @csrf
                <button type="submit"
                        class="px-4 py-2 {{ $contact->is_read ? 'bg-rose-pine-gold' : 'bg-rose-pine-foam' }} text-white rounded-lg hover:bg-opacity-80 transition">
                    {{ $contact->is_read ? 'Mark as Unread' : 'Mark as Read' }}
                </button>
            </form>
            <form action="{{ route('admin.contacts.destroy', $contact) }}"
                  method="POST"
                  onsubmit="return confirm('Are you sure you want to delete this contact submission?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 bg-rose-pine-love text-rose-pine-base rounded-lg hover:bg-opacity-80 transition">
                    Delete
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-rose-pine-foam bg-opacity-20 border border-rose-pine-foam rounded-lg text-rose-pine-foam">
        {{ session('success') }}
    </div>
    @endif

    <!-- Contact Information Card -->
    <div class="bg-rose-pine-surface rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-rose-pine-text mb-4">Contact Information</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-rose-pine-subtle mb-1">Name</label>
                <p class="text-rose-pine-text">{{ $contact->name }}</p>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-rose-pine-subtle mb-1">Email</label>
                <a href="mailto:{{ $contact->email }}" class="text-rose-pine-gold hover:text-rose-pine-foam">
                    {{ $contact->email }}
                </a>
            </div>

            <!-- Subject -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-rose-pine-subtle mb-1">Subject</label>
                <p class="text-rose-pine-text font-medium">{{ $contact->subject }}</p>
            </div>

            <!-- Received Date -->
            <div>
                <label class="block text-sm font-medium text-rose-pine-subtle mb-1">Received</label>
                <p class="text-rose-pine-text">
                    {{ $contact->created_at->format('F d, Y') }} at {{ $contact->created_at->format('H:i') }}
                    <span class="text-rose-pine-subtle text-sm">({{ $contact->created_at->diffForHumans() }})</span>
                </p>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-rose-pine-subtle mb-1">Status</label>
                @if($contact->is_read)
                <span class="inline-flex px-3 py-1 bg-rose-pine-foam bg-opacity-20 text-rose-pine-foam rounded text-sm">
                    ✓ Read
                </span>
                @else
                <span class="inline-flex px-3 py-1 bg-rose-pine-love bg-opacity-20 text-rose-pine-love rounded text-sm">
                    ● Unread
                </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Message Card -->
    <div class="bg-rose-pine-surface rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-rose-pine-text mb-4">Message</h2>
        <div class="prose prose-rose-pine max-w-none">
            <p class="text-rose-pine-text whitespace-pre-wrap">{{ $contact->message }}</p>
        </div>
    </div>

    <!-- Technical Details Card -->
    <div class="bg-rose-pine-surface rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-rose-pine-text mb-4">Technical Details</h2>

        <div class="space-y-4">
            <!-- IP Address -->
            <div>
                <label class="block text-sm font-medium text-rose-pine-subtle mb-1">IP Address</label>
                <p class="text-rose-pine-text font-mono text-sm">{{ $contact->ip_address ?? 'Not recorded' }}</p>
            </div>

            <!-- User Agent -->
            <div>
                <label class="block text-sm font-medium text-rose-pine-subtle mb-1">User Agent</label>
                <p class="text-rose-pine-text font-mono text-xs break-all">{{ $contact->user_agent ?? 'Not recorded' }}</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-between pt-6 border-t border-rose-pine-base/30">
        <a href="{{ route('admin.contacts.index') }}"
           class="px-6 py-2 text-rose-pine-text hover:text-rose-pine-gold transition">
            ← Back to Contacts
        </a>

        <a href="mailto:{{ $contact->email }}?subject=Re: {{ urlencode($contact->subject) }}"
           class="px-6 py-2 bg-rose-pine-gold text-rose-pine-base rounded-lg hover:bg-opacity-80 transition">
            Reply via Email
        </a>
    </div>
</div>
@endsection
