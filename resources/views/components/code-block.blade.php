<div x-ref="container" class="relative group my-6 rounded-lg overflow-hidden border border-rose-pine-overlay">
    {{-- Header with language and copy button --}}
    <div class="flex justify-between items-center px-4 py-2 bg-rose-pine-surface border-b border-rose-pine-overlay">
        <span class="text-xs font-mono text-rose-pine-muted">{{ $language ?? 'code' }}</span>
        
        <button x-data="{ copied: false }"
                @click="navigator.clipboard.writeText($refs.container.querySelector('.overflow-x-auto').textContent); copied = true; setTimeout(() => copied = false, 2000)"
                class="text-xs text-rose-pine-subtle hover:text-rose-pine-text transition-colors flex items-center gap-1">
            <span x-show="!copied">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
            </span>
            <span x-show="copied" class="text-rose-pine-pine flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Copied!
            </span>
            <span x-show="!copied">Copy</span>
        </button>
    </div>
    
    {{-- Code block with syntax highlighting (Shiki keeps pre/code wrapper) --}}
    <div class="rounded-t-none overflow-x-auto p-4 bg-rose-pine-overlay m-0">{!! $slot !!}</div>
</div>

{{-- Line numbers CSS --}}
@push('head')
<style>
    /* Line numbers for code blocks */
    .shiki { counter-reset: line; }
    .shiki .line::before { 
        counter-increment: line; 
        content: counter(line); 
        display: inline-block;
        width: 2em;
        margin-right: 1em;
        text-align: right;
        color: var(--rp-muted);
        user-select: none;
    }
</style>
@endpush
