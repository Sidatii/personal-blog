@extends('layouts.admin')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="sm:flex sm:items-center mb-8">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-rose-pine-text">Settings</h1>
            <p class="mt-2 text-sm text-rose-pine-subtle">
                Manage your site settings and preferences.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-rose-pine-foam/20 border border-rose-pine-foam/30 rounded-lg">
            <p class="text-sm text-rose-pine-foam font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-rose-pine-surface rounded-lg border border-rose-pine-highlight">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">
                {{-- Comments Section --}}
                <div>
                    <h3 class="text-lg font-medium text-rose-pine-text mb-4">Comments</h3>

                    <div class="space-y-4">
                        {{-- Auto-approve toggle --}}
                        <div class="flex items-center justify-between p-4 bg-rose-pine-base rounded-lg border border-rose-pine-highlight">
                            <div class="flex-1">
                                <label for="auto_approve_comments" class="text-sm font-medium text-rose-pine-text">
                                    Auto-approve comments
                                </label>
                                <p class="text-xs text-rose-pine-muted mt-1">
                                    When enabled, new comments appear immediately without moderation. When disabled, comments require admin approval before being visible.
                                </p>
                            </div>

                            <div class="ml-4" x-data="{ enabled: {{ $autoApprove ? 'true' : 'false' }} }">
                                <button
                                    type="button"
                                    @click="enabled = !enabled"
                                    :class="{ 'bg-rose-pine-love': enabled, 'bg-rose-pine-muted': !enabled }"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-rose-pine-love focus:ring-offset-2"
                                    role="switch"
                                    :aria-checked="enabled.toString()"
                                >
                                    <span
                                        :class="{ 'translate-x-5': enabled, 'translate-x-0': !enabled }"
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-rose-pine-base shadow ring-0 transition duration-200 ease-in-out"
                                    ></span>
                                </button>
                                <input type="hidden" name="auto_approve_comments" :value="enabled ? '1' : '0'">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-rose-pine-overlay rounded-b-lg border-t border-rose-pine-highlight flex items-center justify-end gap-3">
                <button
                    type="submit"
                    class="px-4 py-2 bg-rose-pine-love hover:bg-rose-pine-rose text-rose-pine-base font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-rose-pine-love focus:ring-offset-2"
                >
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
