@props(['certification'])

<div class="relative flex gap-4 p-5 rounded-xl border
            {{ $certification->is_featured ? 'border-rose-pine-gold bg-rose-pine-overlay shadow-md' : 'border-rose-pine-muted bg-rose-pine-surface' }}
            {{ $certification->isExpired() ? 'opacity-60' : '' }}
            transition hover:border-rose-pine-subtle">

    <div class="flex-shrink-0">
        @if($certification->badge_url)
            <img src="{{ $certification->badge_url }}" alt="{{ $certification->issuer }} badge" class="w-14 h-14 object-contain rounded-lg">
        @else
            <div class="w-14 h-14 rounded-lg bg-rose-pine-overlay border border-rose-pine-muted flex items-center justify-center text-2xl">🎓</div>
        @endif
    </div>

    <div class="flex-1 min-w-0">
        <div class="flex items-start justify-between gap-2">
            <div>
                <h3 class="font-semibold text-rose-pine-text leading-snug">{{ $certification->title }}</h3>
                <p class="text-sm text-rose-pine-subtle mt-0.5">{{ $certification->issuer }}</p>
            </div>
            @if($certification->is_featured)
            <span class="flex-shrink-0 text-xs px-2 py-0.5 rounded-full bg-rose-pine-gold/20 text-rose-pine-gold border border-rose-pine-gold/30">Featured</span>
            @endif
        </div>

        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-rose-pine-muted">
            <span>Issued {{ $certification->issued_at->format('M Y') }}</span>
            @if($certification->expires_at)
                @if($certification->isExpired())
                    <span class="text-red-400">Expired {{ $certification->expires_at->format('M Y') }}</span>
                @else
                    <span>Expires {{ $certification->expires_at->format('M Y') }}</span>
                @endif
            @else
                <span>No expiry</span>
            @endif
            @if($certification->credential_id)
                <span>ID: {{ $certification->credential_id }}</span>
            @endif
        </div>

        @if($certification->credential_url)
        <a href="{{ $certification->credential_url }}" target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center gap-1 mt-2 text-xs text-rose-pine-foam hover:text-rose-pine-iris transition">
            Verify credential →
        </a>
        @endif
    </div>
</div>
