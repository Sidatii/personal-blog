@extends('layouts.admin')

@section('title', 'Add Certification')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-rose-pine-text">Add Certification</h1>
        <p class="text-rose-pine-subtle mt-2">Fill in the details below to add a new certification.</p>
    </div>

    <form action="{{ route('admin.certifications.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Title -->
        <div>
            <label for="title" class="block text-sm font-medium text-rose-pine-text mb-2">
                Title <span class="text-rose-pine-love">*</span>
            </label>
            <input type="text"
                   name="title"
                   id="title"
                   value="{{ old('title') }}"
                   required
                   class="w-full px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('title') border-rose-pine-love @enderror">
            @error('title')
            <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
            @enderror
        </div>

        <!-- Issuer -->
        <div>
            <label for="issuer" class="block text-sm font-medium text-rose-pine-text mb-2">
                Issuer <span class="text-rose-pine-love">*</span>
            </label>
            <input type="text"
                   name="issuer"
                   id="issuer"
                   value="{{ old('issuer') }}"
                   required
                   class="w-full px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('issuer') border-rose-pine-love @enderror">
            @error('issuer')
            <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
            @enderror
        </div>

        <!-- Credential ID and URL -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Credential ID -->
            <div>
                <label for="credential_id" class="block text-sm font-medium text-rose-pine-text mb-2">
                    Credential ID
                </label>
                <input type="text"
                       name="credential_id"
                       id="credential_id"
                       value="{{ old('credential_id') }}"
                       placeholder="e.g. ABC-123456"
                       class="w-full px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('credential_id') border-rose-pine-love @enderror">
                @error('credential_id')
                <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
                @enderror
            </div>

            <!-- Credential URL -->
            <div>
                <label for="credential_url" class="block text-sm font-medium text-rose-pine-text mb-2">
                    Credential URL
                </label>
                <input type="url"
                       name="credential_url"
                       id="credential_url"
                       value="{{ old('credential_url') }}"
                       placeholder="https://example.com/verify"
                       class="w-full px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('credential_url') border-rose-pine-love @enderror">
                <p class="mt-1 text-sm text-rose-pine-subtle">Link to verify the credential.</p>
                @error('credential_url')
                <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Badge Image -->
        <div>
            <x-admin-image-upload
                name="badge_image"
                directory="uploads/certifications"
                label="Badge Image"
            />
            @error('badge_image')
            <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
            @enderror
        </div>

        <!-- Issued At and Expires At -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Issued At -->
            <div>
                <label for="issued_at" class="block text-sm font-medium text-rose-pine-text mb-2">
                    Issued At <span class="text-rose-pine-love">*</span>
                </label>
                <input type="date"
                       name="issued_at"
                       id="issued_at"
                       value="{{ old('issued_at') }}"
                       required
                       class="w-full px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('issued_at') border-rose-pine-love @enderror">
                @error('issued_at')
                <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
                @enderror
            </div>

            <!-- Expires At -->
            <div>
                <label for="expires_at" class="block text-sm font-medium text-rose-pine-text mb-2">
                    Expires At
                </label>
                <input type="date"
                       name="expires_at"
                       id="expires_at"
                       value="{{ old('expires_at') }}"
                       class="w-full px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('expires_at') border-rose-pine-love @enderror">
                <p class="mt-1 text-sm text-rose-pine-subtle">Leave blank if no expiry.</p>
                @error('expires_at')
                <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Featured and Sort Order -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Featured -->
            <div class="flex items-center">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox"
                           name="is_featured"
                           id="is_featured"
                           value="1"
                           {{ old('is_featured') ? 'checked' : '' }}
                           class="rounded border-rose-pine-base/30 text-rose-pine-gold focus:ring-rose-pine-gold focus:ring-offset-rose-pine-surface">
                    <span class="text-sm font-medium text-rose-pine-text">Mark as Featured</span>
                </label>
            </div>

            <!-- Sort Order -->
            <div>
                <label for="sort_order" class="block text-sm font-medium text-rose-pine-text mb-2">
                    Sort Order
                </label>
                <input type="number"
                       name="sort_order"
                       id="sort_order"
                       value="{{ old('sort_order', 0) }}"
                       min="0"
                       class="w-full px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('sort_order') border-rose-pine-love @enderror">
                @error('sort_order')
                <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-rose-pine-base/30">
            <a href="{{ route('admin.certifications.index') }}"
               class="px-6 py-2 text-rose-pine-text hover:text-rose-pine-gold transition">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-rose-pine-gold text-rose-pine-base rounded-lg hover:bg-opacity-80 transition">
                Create Certification
            </button>
        </div>
    </form>
</div>
@endsection
