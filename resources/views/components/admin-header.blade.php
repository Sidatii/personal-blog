<div class="flex items-center justify-between px-6 py-4">
    <!-- Left side: Mobile menu toggle + Page title -->
    <div class="flex items-center gap-4">
        <!-- Mobile menu toggle button -->
        <button @click="open = !open"
                class="lg:hidden p-2 rounded-lg text-rose-pine-subtle hover:bg-rose-pine-highlight-low hover:text-rose-pine-text transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Page title -->
        <h2 class="text-xl font-semibold text-rose-pine-text">
            @yield('page-title', 'Dashboard')
        </h2>
    </div>

    <!-- Right side: Dark mode toggle + User dropdown -->
    <div class="flex items-center gap-4">
        <!-- Dark mode toggle -->
        <button @click="toggleTheme()"
                class="p-2 rounded-lg text-rose-pine-subtle hover:bg-rose-pine-highlight-low hover:text-rose-pine-text transition-colors duration-200"
                title="Toggle theme">
            <!-- Sun icon (shown in dark mode) -->
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-5 w-5 dark:hidden"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <!-- Moon icon (shown in light mode) -->
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-5 w-5 hidden dark:block"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>

        <!-- User dropdown -->
        <div x-data="{ userDropdown: false }" class="relative">
            <button @click="userDropdown = !userDropdown"
                    class="flex items-center gap-2 p-2 rounded-lg text-rose-pine-text hover:bg-rose-pine-highlight-low transition-colors duration-200">
                <div class="w-8 h-8 rounded-full bg-rose-pine-foam flex items-center justify-center">
                    <span class="text-sm font-medium text-rose-pine-base">
                        {{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'A', 0, 1)) }}
                    </span>
                </div>
                <span class="hidden md:block font-medium">{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown menu -->
            <div x-show="userDropdown"
                 @click.away="userDropdown = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-48 bg-rose-pine-surface border border-rose-pine-highlight-low rounded-lg shadow-lg overflow-hidden z-50"
                 style="display: none;">
                <div class="py-1">
                    <div class="px-4 py-2 border-b border-rose-pine-highlight-low">
                        <p class="text-sm font-medium text-rose-pine-text">{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</p>
                        <p class="text-xs text-rose-pine-subtle">{{ Auth::guard('admin')->user()->email ?? '' }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-rose-pine-love hover:bg-rose-pine-highlight-low transition-colors duration-200">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleTheme() {
        const html = document.documentElement;
        const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        if (newTheme === 'dark') {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }

        localStorage.setItem('theme', newTheme);
    }
</script>
