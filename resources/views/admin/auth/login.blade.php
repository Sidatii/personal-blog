<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-rose-pine-base text-rose-pine-text min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-rose-pine-surface p-8 rounded-lg shadow-lg border border-rose-pine-overlay">
            <h1 class="text-2xl font-bold text-rose-pine-foam mb-6 text-center">Admin Login</h1>

            @if ($errors->any())
                <div class="bg-rose-pine-love/10 border border-rose-pine-love text-rose-pine-love p-4 rounded-lg mb-6">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="block text-rose-pine-subtle mb-2">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full px-4 py-2 bg-rose-pine-base border border-rose-pine-overlay rounded-lg focus:outline-none focus:border-rose-pine-gold text-rose-pine-text"
                    >
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-rose-pine-subtle mb-2">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full px-4 py-2 bg-rose-pine-base border border-rose-pine-overlay rounded-lg focus:outline-none focus:border-rose-pine-gold text-rose-pine-text"
                    >
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-rose-pine-overlay text-rose-pine-gold focus:ring-rose-pine-gold">
                        <span class="ml-2 text-rose-pine-subtle">Remember me</span>
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full bg-rose-pine-gold hover:bg-rose-pine-gold/80 text-rose-pine-base font-bold py-3 rounded-lg transition-colors"
                >
                    Login
                </button>
            </form>
        </div>
    </div>
</body>
</html>
