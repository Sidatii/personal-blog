{{-- Search Box Component with Autocomplete --}}
<div
    x-data="{
        open: false,
        query: '',
        results: { posts: [], projects: [] },
        loading: false,
        async search() {
            if (this.query.length < 2) {
                this.results = { posts: [], projects: [] };
                this.open = false;
                return;
            }

            this.loading = true;

            try {
                const response = await fetch(`{{ route('search.index') }}?q=${encodeURIComponent(this.query)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    this.results = await response.json();
                    this.open = this.results.posts.length > 0 || this.results.projects.length > 0;
                }
            } catch (error) {
                console.error('Search error:', error);
            } finally {
                this.loading = false;
            }
        }
    }"
    @keydown.escape.window="open = false"
    class="relative"
>
    {{-- Search Input --}}
    <div class="relative">
        <input
            type="text"
            x-model="query"
            @input.debounce.300ms="search()"
            @focus="if (query.length >= 2 && (results.posts.length > 0 || results.projects.length > 0)) { open = true }"
            placeholder="Search posts and projects..."
            class="w-full md:w-64 px-4 py-2 pl-10 bg-rose-pine-surface border border-rose-pine-muted rounded-lg
                   text-rose-pine-text placeholder-rose-pine-muted
                   focus:outline-none focus:ring-2 focus:ring-rose-pine-gold focus:border-transparent
                   transition-all"
        />

        {{-- Search Icon --}}
        <svg
            class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-rose-pine-muted"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>

        {{-- Loading Spinner --}}
        <svg
            x-show="loading"
            x-cloak
            class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-rose-pine-gold animate-spin"
            fill="none"
            viewBox="0 0 24 24"
        >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    {{-- Autocomplete Dropdown --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        @click.away="open = false"
        class="absolute z-50 mt-2 w-full md:w-96 bg-rose-pine-surface border border-rose-pine-muted rounded-lg shadow-xl overflow-hidden"
    >
        {{-- Posts Results --}}
        <template x-if="results.posts && results.posts.length > 0">
            <div class="p-2">
                <div class="text-xs uppercase font-semibold text-rose-pine-muted px-3 py-2">
                    Posts
                </div>
                <template x-for="post in results.posts" :key="post.id">
                    <a
                        :href="post.url"
                        class="block px-3 py-2 rounded hover:bg-rose-pine-overlay transition-colors group"
                    >
                        <div class="font-medium text-rose-pine-text group-hover:text-rose-pine-rose" x-text="post.title"></div>
                        <div class="text-xs text-rose-pine-subtle line-clamp-1" x-text="post.excerpt"></div>
                    </a>
                </template>
            </div>
        </template>

        {{-- Projects Results --}}
        <template x-if="results.projects && results.projects.length > 0">
            <div class="p-2" :class="{ 'border-t border-rose-pine-muted': results.posts && results.posts.length > 0 }">
                <div class="text-xs uppercase font-semibold text-rose-pine-muted px-3 py-2">
                    Projects
                </div>
                <template x-for="project in results.projects" :key="project.id">
                    <a
                        :href="project.url"
                        class="block px-3 py-2 rounded hover:bg-rose-pine-overlay transition-colors group"
                    >
                        <div class="font-medium text-rose-pine-text group-hover:text-rose-pine-rose" x-text="project.title"></div>
                        <div class="text-xs text-rose-pine-subtle line-clamp-1" x-text="project.description"></div>
                    </a>
                </template>
            </div>
        </template>

        {{-- View All Results Link --}}
        <div class="border-t border-rose-pine-muted p-2">
            <a
                :href="`{{ route('search.index') }}?q=${encodeURIComponent(query)}`"
                class="block px-3 py-2 text-center text-sm font-medium text-rose-pine-gold hover:text-rose-pine-rose transition-colors"
            >
                View all results →
            </a>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
