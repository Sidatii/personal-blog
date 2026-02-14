@extends('layouts.admin')

@section('title', 'Certifications')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-rose-pine-text">Certifications</h1>
        <a href="{{ route('admin.certifications.create') }}"
           class="px-4 py-2 bg-rose-pine-gold text-rose-pine-base rounded-lg hover:bg-opacity-80 transition font-semibold">
            Add Certification
        </a>
    </div>

    <!-- Flash Message -->
    @if(session('success'))
    <div class="mb-6 px-4 py-3 bg-rose-pine-foam/20 border border-rose-pine-foam/40 rounded-lg text-rose-pine-foam text-sm">
        {{ session('success') }}
    </div>
    @endif

    <!-- Certifications Table -->
    <div class="bg-rose-pine-surface rounded-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-rose-pine-overlay border-b border-rose-pine-base/30">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Issuer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Issued</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Expires</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Featured</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Sort Order</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-rose-pine-subtle uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rose-pine-highlight-low">
                @forelse($certifications as $certification)
                <tr class="hover:bg-rose-pine-overlay transition">
                    <td class="px-6 py-4 text-sm text-rose-pine-text">
                        <div class="font-medium">{{ $certification->title }}</div>
                        @if($certification->credential_id)
                        <div class="text-rose-pine-subtle text-xs mt-1">{{ $certification->credential_id }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-rose-pine-text">{{ $certification->issuer }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-pine-text">
                        {{ $certification->issued_at->format('M j, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-pine-text">
                        @if($certification->expires_at)
                            <span class="{{ $certification->isExpired() ? 'text-rose-pine-love' : '' }}">
                                {{ $certification->expires_at->format('M j, Y') }}
                            </span>
                        @else
                            <span class="text-rose-pine-subtle">No expiry</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-pine-text">
                        @if($certification->is_featured)
                            <span class="text-rose-pine-gold">&#10003;</span>
                        @else
                            <span class="text-rose-pine-subtle">&mdash;</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-rose-pine-subtle">
                        {{ $certification->sort_order }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.certifications.edit', $certification->id) }}"
                           class="text-rose-pine-gold hover:text-rose-pine-foam mr-3">Edit</a>
                        <form action="{{ route('admin.certifications.destroy', $certification->id) }}"
                              method="POST"
                              class="inline"
                              onsubmit="return confirm('Are you sure you want to delete this certification?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-pine-love hover:text-rose-pine-gold">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-rose-pine-subtle">
                        No certifications found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($certifications->hasPages())
        <div class="mt-6">
            {{ $certifications->links() }}
        </div>
    @elseif($certifications->total() > 0)
        <div class="mt-6">
            <p class="text-sm text-rose-pine-subtle">
                Showing all {{ $certifications->total() }} {{ Str::plural('certification', $certifications->total()) }}
            </p>
        </div>
    @endif
</div>
@endsection
