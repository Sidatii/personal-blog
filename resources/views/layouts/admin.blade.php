<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Dark mode script (blocking to prevent FOUC) -->
    <script>
        // Apply theme immediately before page renders
        (function() {
            const theme = localStorage.getItem('theme') || 'dark';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="antialiased bg-rose-pine-base text-rose-pine-text dark">
    <div class="flex min-h-screen" x-data="{ sidebarOpen: false }">
        <!-- Sidebar -->
        <aside class="w-64 bg-rose-pine-overlay border-r border-rose-pine-base/30 fixed inset-y-0 left-0 z-30 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static"
               :class="{ '-translate-x-full': !sidebarOpen }">
            @include('components.admin-sidebar')
        </aside>

        <!-- Main content area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Mobile menu toggle (only visible on mobile) -->
            <div class="lg:hidden bg-rose-pine-surface border-b border-rose-pine-base/20 px-4 py-3">
                <button @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded-lg text-rose-pine-subtle hover:bg-rose-pine-overlay hover:text-rose-pine-text transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Page content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-rose-pine-base p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile sidebar backdrop -->
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden"
         style="display: none;"></div>
</body>
</html>
