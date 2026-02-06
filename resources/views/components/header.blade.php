<?php

/**
 * Header Component
 * Sticky navigation with logo, navigation links, and dark mode toggle
 * Includes mobile hamburger menu with dropdown
 */
?>

<header class="sticky top-0 z-50 bg-rose-pine-base/80 backdrop-blur-md border-b border-rose-pine-overlay">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo/Brand -->
            <a href="/" class="text-xl font-bold text-rose-pine-text hover:text-rose-pine-rose transition-colors duration-200" aria-label="Home">
                {{ config('app.name') }}
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center space-x-8">
                <a href="/blog" class="text-rose-pine-subtle hover:text-rose-pine-text transition-colors duration-200">Blog</a>
                <a href="/about" class="text-rose-pine-subtle hover:text-rose-pine-text transition-colors duration-200">About</a>
                @include('components.dark-mode-toggle')
            </nav>

            <!-- Mobile Hamburger Button -->
            <button
                type="button"
                x-data="{ open: false }"
                @click="open = !open"
                class="md:hidden p-2 text-rose-pine-subtle hover:text-rose-pine-text transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-rose-pine-rose rounded-lg"
                aria-label="Toggle mobile menu"
                :aria-expanded="open"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <!-- Hamburger icon (shown when menu is closed) -->
                    <path
                        x-show="!open"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                    <!-- Close icon (shown when menu is open) -->
                    <path
                        x-show="open"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                        style="display: none;"
                    />
                </svg>
            </button>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            @click.away="open = false"
            class="md:hidden absolute top-16 left-0 right-0 bg-rose-pine-base border-b border-rose-pine-overlay p-4 space-y-4 shadow-lg"
            style="display: none;"
        >
            <a href="/blog" class="block text-rose-pine-subtle hover:text-rose-pine-text transition-colors duration-200">Blog</a>
            <a href="/about" class="block text-rose-pine-subtle hover:text-rose-pine-text transition-colors duration-200">About</a>
            <div class="pt-2 border-t border-rose-pine-overlay">
                <span class="text-xs text-rose-pine-muted uppercase tracking-wider">Theme</span>
                @include('components.dark-mode-toggle')
            </div>
        </div>
    </div>
</header>
