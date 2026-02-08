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
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-rose-pine-surface border-r border-rose-pine-highlight-low fixed inset-y-0 left-0 z-30 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static"
               x-data="{ open: false }"
               :class="{ '-translate-x-full': !open }">
            @include('components.admin-sidebar')
        </aside>

        <!-- Main content area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header -->
            <header class="bg-rose-pine-surface border-b border-rose-pine-highlight-low sticky top-0 z-20">
                @include('components.admin-header')
            </header>

            <!-- Page content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-rose-pine-base p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile sidebar backdrop -->
    <div x-data="{ open: false }"
         x-show="open"
         @click="open = false"
         class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden"></div>
</body>
</html>
