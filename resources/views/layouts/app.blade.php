{{-- Base Layout --}}
{{-- Rose Pine theme with dark mode initialization, header, content slot, and footer --}}
<!DOCTYPE html>
<html lang="en" class="{{ cookie('theme', 'dark') === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta Tags --}}
    <x-seo-meta
        :title="$seo['title'] ?? null"
        :description="$seo['description'] ?? null"
        :image="$seo['image'] ?? null"
        :type="$seo['type'] ?? 'website'"
        :url="$seo['url'] ?? null"
        :published-time="$seo['publishedTime'] ?? null"
        :modified-time="$seo['modifiedTime'] ?? null"
        :author="$seo['author'] ?? null"
    />

    {{-- Dark mode script (blocking inline to prevent FOUC) --}}
    <script>
        (function() {
            const DEFAULT_THEME = 'dark';
            const THEME_KEY = 'theme';
            
            function getTheme() {
                const saved = localStorage.getItem(THEME_KEY);
                if (saved === 'dark' || saved === 'light') {
                    return saved;
                }
                return DEFAULT_THEME;
            }
            
            function applyTheme(theme) {
                const html = document.documentElement;
                if (theme === 'dark') {
                    html.classList.add('dark');
                    html.removeAttribute('data-theme');
                } else {
                    html.classList.remove('dark');
                    html.setAttribute('data-theme', 'light');
                }
            }
            
            applyTheme(getTheme());
        })();
    </script>
    @vite(['resources/js/dark-mode.js'])

    {{-- Vite CSS --}}
    @vite(['resources/css/app.css'])

    {{-- Additional head content --}}
    @stack('head')

    {{-- Umami Analytics (production only) --}}
    <x-umami-tracking />
</head>
<body class="bg-rose-pine-base text-rose-pine-text antialiased min-h-screen flex flex-col">
    {{-- Header --}}
    @include('components.header')

    {{-- Main Content --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('components.footer')

    {{-- Scripts stack --}}
    @stack('scripts')

    {{-- Vite JS --}}
    @vite(['resources/js/app.js'])
</body>
</html>
