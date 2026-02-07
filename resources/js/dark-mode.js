/**
 * Dark Mode Manager
 * Rose Pine theme with cookie persistence
 * Default theme: Rose Pine Main (dark)
 */

(function() {
    'use strict';

    const DEFAULT_THEME = 'dark';
    const THEME_KEY = 'theme';
    const THEME_COOKIE_NAME = 'theme';
    const THEME_COOKIE_EXPIRY_DAYS = 30;

    /**
     * Get cookie value by name
     * @param {string} name - Cookie name
     * @returns {string|null} Cookie value or null if not found
     */
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) {
            return parts.pop().split(';').shift();
        }
        return null;
    }

    /**
     * Set cookie with expiry
     * @param {string} name - Cookie name
     * @param {string} value - Cookie value
     * @param {number} days - Days until expiry
     */
    function setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = `expires=${date.toUTCString()}`;
        document.cookie = `${name}=${value};${expires};path=/;SameSite=Lax`;
    }

    /**
     * Get current theme from localStorage, cookie, or default
     * @returns {string} 'dark' or 'light'
     */
    function getTheme() {
        // Check localStorage first (primary storage)
        const savedTheme = localStorage.getItem(THEME_KEY);
        if (savedTheme === 'dark' || savedTheme === 'light') {
            return savedTheme;
        }
        
        // Fallback to cookie
        const cookieTheme = getCookie(THEME_COOKIE_NAME);
        if (cookieTheme === 'dark' || cookieTheme === 'light') {
            // Sync to localStorage
            localStorage.setItem(THEME_KEY, cookieTheme);
            return cookieTheme;
        }
        
        return DEFAULT_THEME;
    }

    /**
     * Apply theme to document
     * @param {string} theme - 'dark' or 'light'
     */
    function applyTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
            document.documentElement.removeAttribute('data-theme');
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.setAttribute('data-theme', 'light');
        }
    }

    /**
     * Toggle between dark and light theme
     */
    function toggleTheme() {
        const currentTheme = getTheme();
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        // Persist to both localStorage and cookie
        localStorage.setItem(THEME_KEY, newTheme);
        setCookie(THEME_COOKIE_NAME, newTheme, THEME_COOKIE_EXPIRY_DAYS);
        applyTheme(newTheme);

        // Dispatch event for components listening to theme changes
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: newTheme } }));

        return newTheme;
    }

    /**
     * Set theme and persist
     * @param {string} theme - 'dark' or 'light'
     */
    function setTheme(theme) {
        if (theme !== 'dark' && theme !== 'light') {
            console.warn(`Invalid theme: ${theme}. Expected 'dark' or 'light'.`);
            return;
        }
        // Persist to both localStorage and cookie
        localStorage.setItem(THEME_KEY, theme);
        setCookie(THEME_COOKIE_NAME, theme, THEME_COOKIE_EXPIRY_DAYS);
        applyTheme(theme);

        // Dispatch event for components listening to theme changes
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme } }));
    }

    /**
     * Initialize dark mode on page load
     */
    function initialize() {
        const theme = getTheme();
        applyTheme(theme);
        console.log(`Theme initialized: ${theme} (Rose Pine ${theme === 'dark' ? 'Main' : 'Dawn'})`);
    }

    /**
     * Toggle theme via API endpoint
     * @param {string} theme - 'dark' or 'light'
     * @returns {Promise<object>} Response from server
     */
    async function toggleViaApi(theme) {
        try {
            const response = await fetch('/api/theme/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ theme })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Failed to toggle theme via API:', error);
            throw error;
        }
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }

    // Expose API globally for components - must be done after IIFE executes
    window.darkMode = {
        toggle: function() {
            return toggleTheme();
        },
        setTheme: function(theme) {
            return setTheme(theme);
        },
        getTheme: function() {
            return getTheme();
        },
        toggleViaApi: function(theme) {
            return toggleViaApi(theme);
        }
    };

    /**
     * Theme initialization started: dark (Rose Pine Main)
     * Cookie persistence: 30 days
     * Rose Pine colors available via CSS custom properties
     */
})();
