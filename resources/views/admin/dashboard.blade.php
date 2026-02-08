<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-rose-pine-base text-rose-pine-text min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-rose-pine-foam">Admin Dashboard</h1>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button
                    type="submit"
                    class="bg-rose-pine-love hover:bg-rose-pine-love/80 text-rose-pine-base font-bold px-6 py-2 rounded-lg transition-colors"
                >
                    Logout
                </button>
            </form>
        </div>

        <div class="bg-rose-pine-surface p-8 rounded-lg shadow-lg border border-rose-pine-overlay">
            <p class="text-rose-pine-text">Welcome to the admin panel!</p>
        </div>
    </div>
</body>
</html>
