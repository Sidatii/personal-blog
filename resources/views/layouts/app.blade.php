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

    {{-- Dark mode script (blocking to prevent FOUC) --}}
    <script src="{{ asset('js/dark-mode.js') }}"></script>

    {{-- Vite CSS --}}
    @vite(['resources/css/app.css'])

    {{-- Additional head content --}}
    @stack('head')
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
