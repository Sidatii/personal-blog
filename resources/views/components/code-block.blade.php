<div class="relative group my-6 rounded-lg overflow-hidden border border-rose-pine-overlay">
    {{-- Header with language and copy button --}}
    <div class="flex justify-between items-center px-4 py-2 bg-rose-pine-surface border-b border-rose-pine-overlay">
        <span class="text-xs font-mono text-rose-pine-muted">{{ $language ?? 'code' }}</span>
        
        <button onclick="copyCode(this)"
                class="text-xs text-rose-pine-subtle hover:text-rose-pine-text transition-colors flex items-center gap-1"
                data-code="{{ htmlentities($highlighted ?? $slot ?? '', ENT_QUOTES, 'UTF-8') }}">
            <span class="copy-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
            </span>
            <span class="copied-icon hidden text-rose-pine-pine">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </span>
            <span class="copy-text">Copy</span>
            <span class="copied-text hidden text-rose-pine-pine">Copied!</span>
        </button>
    </div>
    
    {{-- Code block with syntax highlighting --}}
    <pre class="rounded-t-none overflow-x-auto p-4 bg-rose-pine-overlay m-0"><code>{!! $highlighted ?? $slot !!}</code></pre>
</div>

<script>
function copyCode(button) {
    const code = button.getAttribute('data-code');
    const decodedCode = code.replace(/&quot;/g, '"')
                           .replace(/&amp;/g, '&')
                           .replace(/&lt;/g, '<')
                           .replace(/&gt;/g, '>')
                           .replace(/&#039;/g, "'");
    
    navigator.clipboard.writeText(decodedCode).then(function() {
        const copyIcon = button.querySelector('.copy-icon');
        const copiedIcon = button.querySelector('.copied-icon');
        const copyText = button.querySelector('.copy-text');
        const copiedText = button.querySelector('.copied-text');
        
        copyIcon.classList.add('hidden');
        copiedIcon.classList.remove('hidden');
        copyText.classList.add('hidden');
        copiedText.classList.remove('hidden');
        
        setTimeout(function() {
            copyIcon.classList.remove('hidden');
            copiedIcon.classList.add('hidden');
            copyText.classList.remove('hidden');
            copiedText.classList.add('hidden');
        }, 2000);
    }).catch(function(err) {
        console.error('Failed to copy: ', err);
    });
}
</script>
