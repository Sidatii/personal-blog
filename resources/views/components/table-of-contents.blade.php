<nav class="sticky top-24 max-h-[calc(100vh-6rem)] overflow-y-auto hidden lg:block">
    <h3 class="text-xs font-bold text-rose-pine-muted uppercase tracking-wider mb-3">
        On this page
    </h3>
    
    @if(isset($headings) && count($headings) > 0)
        <ul class="space-y-2 text-sm">
            @foreach($headings as $heading)
                <li class="pl-{{ ($heading->level - 1) * 4 }}">
                    <a href="#{{ $heading->id }}"
                       class="block text-rose-pine-subtle hover:text-rose-pine-text transition-colors line-clamp-2">
                        {{ $heading->text }}
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-sm text-rose-pine-muted">No headings found</p>
    @endif
</nav>
