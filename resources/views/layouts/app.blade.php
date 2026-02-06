<!DOCTYPE html>
<html lang="en" class="{{ cookie('theme', 'dark') === 'dark' ? 'dark' : '' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $description ?? 'A personal blog built with Laravel and Rose Pine theme' }}">

    <title>{{ $title ?? config('app.name') }}</title>

    {{-- Dark mode script (blocking to prevent FOUC) --}}
    <script src="{{ asset('js/dark-mode.js') }}"></script>

    {{-- Vite CSS --}}
    @vite(['resources/css/app.css'])

    {{-- Additional head assets --}}
    @stack('head')
</head>

<body class="bg-rose-pine-base text-rose-pine-text antialiased min-h-screen flex flex-col font-sans">
    {{-- Header --}}
    @include('components.header')

    {{-- Main Content --}}
    <main class="flex-grow">
        {{ $slot ?? yield('content') }}
    </main>

    {{-- Footer --}}
    @include('components.footer')

    {{-- Scripts stack --}}
    @stack('scripts')

    {{-- Vite JS --}}
    @vite(['resources/js/app.js'])
</body>

</html>
