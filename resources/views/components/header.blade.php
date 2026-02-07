{{-- Header Component --}}
{{-- Rose Pine theme with logo, navigation, and dark mode toggle --}}
<header class="sticky top-0 z-50 bg-rose-pine-base/80 backdrop-blur-md border-b border-rose-pine-overlay">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo/Brand (left) --}}
            <a href="/" class="text-xl font-bold text-rose-pine-text hover:text-rose-pine-rose transition-colors">
                {{ config('app.name') }}
            </a>

            {{-- Desktop Navigation (center/right) --}}
            <nav class="hidden md:flex items-center space-x-8">
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
                @include('components.dark-mode-toggle')
            </nav>

            {{-- Mobile Hamburger Button --}}
            <button
                x-data="{ open: false }"
                @click="open = !open"
                class="md:hidden p-2 text-rose-pine-subtle hover:text-rose-pine-text transition-colors"
                aria-label="Toggle mobile menu"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    {{-- Hamburger icon (when menu closed) --}}
                    <path
                        x-show="!open"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                    {{-- Close icon (when menu open) --}}
                    <path
                        x-show="open"
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
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                @click.away="open = false"
                class="md:hidden absolute top-16 left-0 right-0 bg-rose-pine-base border-b border-rose-pine-overlay p-4 space-y-4"
            >
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
                <div class="pt-2 border-t border-rose-pine-overlay">
                    @include('components.dark-mode-toggle')
                </div>
            </div>
    </div>
</header>
