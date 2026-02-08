@props(['post' => null, 'parentId' => null])

<form 
    action="{{ route('comments.store', $post) }}" 
    method="POST"
    hx-post="{{ route('comments.store', $post) }}"
    hx-target="#comments-list"
    hx-swap="afterbegin"
    hx-indicator="#comment-submit-spinner"
    class="space-y-4"
>
    @csrf
    
    {{-- Honeypot field - hidden from users but visible to bots --}}
    <div style="position: absolute; left: -9999px;" aria-hidden="true">
        <input 
            type="text" 
            name="trap_field" 
            tabindex="-1" 
            autocomplete="off"
            aria-hidden="true"
        >
    </div>
    
    {{-- Another honeypot field that bots often fill --}}
    <input 
        type="hidden" 
        name="website" 
        value=""
        aria-hidden="true"
    >
    
    @if($parentId)
        <input type="hidden" name="parent_id" value="{{ $parentId }}">
    @endif

    <div>
        <label for="content" class="sr-only">Comment</label>
        <textarea
            id="content"
            name="content"
            required
            rows="4"
            placeholder="Share your thoughts..."
            class="w-full px-4 py-3 border border-rose-pine-200 dark:border-rose-pine-700 rounded-lg focus:ring-2 focus:ring-rose-pine-500 focus:border-transparent bg-white dark:bg-rose-pine-900 text-rose-pine-900 dark:text-rose-pine-100 placeholder-rose-pine-400 dark:placeholder-rose-pine-600 resize-none"
        ></textarea>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="author_name" class="sr-only">Name (optional)</label>
            <input
                type="text"
                id="author_name"
                name="author_name"
                placeholder="Name (optional)"
                maxlength="255"
                class="w-full px-4 py-2 border border-rose-pine-200 dark:border-rose-pine-700 rounded-lg focus:ring-2 focus:ring-rose-pine-500 focus:border-transparent bg-white dark:bg-rose-pine-900 text-rose-pine-900 dark:text-rose-pine-100 placeholder-rose-pine-400 dark:placeholder-rose-pine-600"
            >
        </div>

        <div>
            <label for="author_email" class="sr-only">Email (optional, not published)</label>
            <input
                type="email"
                id="author_email"
                name="author_email"
                placeholder="Email (optional, not published)"
                maxlength="255"
                class="w-full px-4 py-2 border border-rose-pine-200 dark:border-rose-pine-700 rounded-lg focus:ring-2 focus:ring-rose-pine-500 focus:border-transparent bg-white dark:bg-rose-pine-900 text-rose-pine-900 dark:text-rose-pine-100 placeholder-rose-pine-400 dark:placeholder-rose-pine-600"
            >
        </div>
    </div>

    <div class="flex items-center justify-between">
        <button
            type="submit"
            class="px-6 py-2 bg-rose-pine-500 hover:bg-rose-pine-600 text-white rounded-lg font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-rose-pine-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
            hx-disabled-elt="this"
        >
            <span class="flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <span>{{ $parentId ? 'Reply' : 'Post Comment' }}</span>
            </span>
        </button>

        <div id="comment-submit-spinner" class="htmx-indicator hidden">
            <div class="flex items-center space-x-2 text-rose-pine-500">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm">Posting...</span>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <ul class="text-sm text-red-600 dark:text-red-400 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <p class="text-xs text-rose-pine-500 dark:text-rose-pine-400">
        Comments are moderated before appearing publicly.
    </p>
</form>