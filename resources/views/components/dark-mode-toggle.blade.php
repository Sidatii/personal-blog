<?php

/**
 * Dark Mode Toggle Component
 * Rose Pine theme with sun/moon icons and cookie persistence
 * Uses Alpine.js for interactivity and window.darkMode API for theme management
 */
?>

<div x-data="{
    isDark: document.documentElement.classList.contains('dark') || window.darkMode.getTheme() === 'dark'
}" class="relative">
    <button
        type="button"
        class="p-2 rounded-lg hover:bg-rose-pine-overlay transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-rose-pine-rose"
        :class="{ 'text-rose-pine-gold': isDark, 'text-rose-pine-text': !isDark }"
        aria-label="Toggle dark mode"
        @click="
            isDark = !isDark;
            const newTheme = isDark ? 'dark' : 'light';
            fetch('/api/theme/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ theme: newTheme })
            }).then(() => {
                window.darkMode.setTheme(newTheme);
            }).catch(error => {
                console.error('Failed to toggle theme:', error);
                // Revert state on error
                isDark = !isDark;
            });
        "
    >
        <!-- Sun icon (visible when dark mode is active - click to switch to light) -->
        <svg x-show="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>

        <!-- Moon icon (visible when light mode is active - click to switch to dark) -->
        <svg x-show="!isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
    </button>
</div>
