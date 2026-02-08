<div class="flex flex-col h-full">
    <!-- Logo/Branding -->
    <div class="flex items-center justify-center h-16 border-b border-rose-pine-highlight-low">
        <h1 class="text-xl font-bold text-rose-pine-foam">Admin Panel</h1>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 overflow-y-auto py-4 px-3">
        <ul class="space-y-1">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-rose-pine-highlight-med text-rose-pine-foam' : 'text-rose-pine-subtle hover:bg-rose-pine-highlight-low hover:text-rose-pine-text' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="font-medium">Dashboard</span>
                </a>
            </li>

            <!-- Projects -->
            <li>
                <a href="{{ route('admin.projects.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.projects.*') ? 'bg-rose-pine-highlight-med text-rose-pine-foam' : 'text-rose-pine-subtle hover:bg-rose-pine-highlight-low hover:text-rose-pine-text' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span class="font-medium">Projects</span>
                </a>
            </li>

            <!-- Contacts -->
            <li>
                <a href="{{ route('admin.contacts.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.contacts.*') ? 'bg-rose-pine-highlight-med text-rose-pine-foam' : 'text-rose-pine-subtle hover:bg-rose-pine-highlight-low hover:text-rose-pine-text' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium">Contacts</span>
                </a>
            </li>

            <!-- Activity Log -->
            <li>
                <a href="{{ route('admin.activity.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.activity.*') ? 'bg-rose-pine-highlight-med text-rose-pine-foam' : 'text-rose-pine-subtle hover:bg-rose-pine-highlight-low hover:text-rose-pine-text' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m-6 8h6m-3-8v8m-9-4h18M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" />
                    </svg>
                    <span class="font-medium">Activity Log</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Logout button -->
    <div class="p-3 border-t border-rose-pine-highlight-low">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-rose-pine-love hover:bg-rose-pine-highlight-low transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="font-medium">Logout</span>
            </button>
        </form>
    </div>
</div>
