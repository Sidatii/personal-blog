<nav
    x-data="{
        active: '',
        init() {
            const headings = Array.from(document.querySelectorAll('.post-content h1[id], .post-content h2[id]'));
            if (!headings.length) return;
            this.active = headings[0]?.id ?? '';

            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) this.active = entry.target.id;
                });
            }, { rootMargin: '-80px 0px -60% 0px' });

            headings.forEach(h => observer.observe(h));
        }
    }"
    class="sticky top-24 max-h-[calc(100vh-7rem)] overflow-y-auto"
>
    <div class="bg-rose-pine-surface border border-rose-pine-overlay rounded-xl p-4">
        <p class="text-[10px] font-semibold uppercase tracking-widest text-rose-pine-muted mb-3 pb-2.5 border-b border-rose-pine-overlay">
            On this page
        </p>

        @if(isset($headings) && count($headings) > 0)
            <ul class="space-y-0.5 text-sm">
                @foreach($headings as $heading)
                    <li>
                        <a
                            href="#{{ $heading->id }}"
                            :class="active === '{{ $heading->id }}'
                                ? 'text-rose-pine-iris border-rose-pine-iris bg-rose-pine-overlay/60 font-medium'
                                : 'text-rose-pine-subtle border-transparent hover:text-rose-pine-text hover:border-rose-pine-muted hover:bg-rose-pine-overlay/30'"
                            class="block border-l-2 transition-all duration-150 line-clamp-2 {{ $heading->level === 1 ? 'px-3 py-1.5 text-sm' : 'pl-5 pr-3 py-1 text-xs' }}"
                        >{{ $heading->text }}</a>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-xs text-rose-pine-muted">No headings found</p>
        @endif
    </div>
</nav>
