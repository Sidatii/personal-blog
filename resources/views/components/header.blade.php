{{-- Header Component --}}
{{-- Rose Pine theme with logo, navigation, and dark mode toggle --}}
<header class="sticky top-0 z-50 bg-rose-pine-base/80 backdrop-blur-md border-b border-rose-pine-overlay" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">
            {{-- Logo/Brand (left) --}}
            <a href="/" class="flex-shrink-0 h-full flex items-center py-2">
                <img src="/oob-white.png" alt="{{ config('app.name') }}" class="h-full w-auto dark:block hidden">
                <img src="/oob-black.png" alt="{{ config('app.name') }}" class="h-full w-auto dark:hidden block">
            </a>

            {{-- Desktop Navigation (center) --}}
            <nav class="hidden md:flex items-center space-x-6 flex-shrink-0">
                <a href="/blog" class="text-rose-pine-subtle hover:text-rose-pine-text transition-colors">
                    Blog
                </a>
                <a href="/projects" class="text-rose-pine-subtle hover:text-rose-pine-text transition-colors">
                    Projects
                </a>
                <a href="/about" class="text-rose-pine-subtle hover:text-rose-pine-text transition-colors">
                    About
                </a>
                <a href="/contact" class="text-rose-pine-subtle hover:text-rose-pine-text transition-colors">
                    Contact
                </a>
                @auth('admin')
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-rose-pine-gold text-gray-900 rounded-lg font-semibold hover:bg-rose-pine-gold/90 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Admin
                </a>
                @endauth
                @include('components.dark-mode-toggle')
            </nav>

            {{-- Search Box (desktop) --}}
            <div class="hidden md:block flex-grow max-w-md">
                @include('components.search-box')
            </div>

            {{-- Mobile Hamburger Button --}}
            <button
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="md:hidden p-2 text-rose-pine-subtle hover:text-rose-pine-text transition-colors"
                aria-label="Toggle mobile menu"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    {{-- Hamburger icon (when menu closed) --}}
                    <path
                        x-show="!mobileMenuOpen"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                    {{-- Close icon (when menu open) --}}
                    <path
                        x-show="mobileMenuOpen"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>
        </div>

        {{-- Mobile Menu Dropdown --}}
        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            @click.away="mobileMenuOpen = false"
            class="md:hidden pb-4 space-y-4"
            style="display: none;"
        >
            {{-- Mobile Search --}}
            <div class="mb-4">
                <form action="{{ route('search.index') }}" method="GET">
                    <input
                        type="text"
                        name="q"
                        placeholder="Search..."
                        class="w-full px-4 py-2 bg-rose-pine-surface border border-rose-pine-muted rounded-lg
                               text-rose-pine-text placeholder-rose-pine-muted
                               focus:outline-none focus:ring-2 focus:ring-rose-pine-gold focus:border-transparent"
                    />
                </form>
            </div>

            <a href="/blog" class="block text-rose-pine-subtle hover:text-rose-pine-text transition-colors">
                Blog
            </a>
            <a href="/projects" class="block text-rose-pine-subtle hover:text-rose-pine-text transition-colors">
                Projects
            </a>
            <a href="/about" class="block text-rose-pine-subtle hover:text-rose-pine-text transition-colors">
                About
            </a>
            <a href="/contact" class="block text-rose-pine-subtle hover:text-rose-pine-text transition-colors">
                Contact
            </a>
            @auth('admin')
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 bg-rose-pine-gold text-gray-900 rounded-lg font-semibold hover:bg-rose-pine-gold/90 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Admin Panel
            </a>
            @endauth
            <div class="pt-2 border-t border-rose-pine-overlay">
                @include('components.dark-mode-toggle')
            </div>
        </div>
    </div>
</header>
