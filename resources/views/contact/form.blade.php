@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="grid lg:grid-cols-2 gap-14 items-start">

        {{-- Left: Mailer.java + context --}}
        <div>
            <h1 class="text-4xl font-bold mb-3" style="color:var(--rose-pine-text)">Get in touch.</h1>
            <p class="text-lg leading-relaxed mb-10" style="color:var(--rose-pine-subtle)">
                Whether you have a project in mind, a question, or just want to say hello —
                send a message and I'll get back to you.
            </p>

            <div class="rounded-xl overflow-hidden border" style="border-color:var(--rose-pine-overlay);background:var(--rose-pine-surface)">
                <div class="flex items-center gap-2 px-4 py-3 border-b" style="border-color:var(--rose-pine-overlay);background:var(--rose-pine-overlay)">
                    <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-love)"></span>
                    <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-gold)"></span>
                    <span class="w-3 h-3 rounded-full" style="background:var(--rose-pine-foam)"></span>
                    <span class="ml-3 text-xs font-mono" style="color:var(--rose-pine-muted)">Mailer.java</span>
                </div>
                <pre class="p-5 text-xs sm:text-sm leading-6 overflow-x-auto" style="color:var(--rose-pine-text)"><span class="text-rose-pine-pine">public</span> <span class="text-rose-pine-iris">Response</span> <span class="text-rose-pine-rose">send</span><span class="text-rose-pine-subtle">(</span><span class="text-rose-pine-iris">ContactForm</span> form<span class="text-rose-pine-subtle">) {</span>
    validator<span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">validate</span><span class="text-rose-pine-subtle">(</span>form<span class="text-rose-pine-subtle">);</span>
    mailer<span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">dispatch</span><span class="text-rose-pine-subtle">(</span>
        <span class="text-rose-pine-pine">new</span> <span class="text-rose-pine-iris">Message</span><span class="text-rose-pine-subtle">(</span>form<span class="text-rose-pine-subtle">)</span>
    <span class="text-rose-pine-subtle">);</span>
    <span class="text-rose-pine-pine">return</span> <span class="text-rose-pine-iris">Response</span><span class="text-rose-pine-subtle">.</span><span class="text-rose-pine-rose">created</span><span class="text-rose-pine-subtle">();</span>
    <span class="text-rose-pine-muted">// Let's talk.</span>
<span class="text-rose-pine-subtle">}</span></pre>
            </div>
        </div>

        {{-- Right: Contact form --}}
        <div>
            @if($errors->any())
                <div class="mb-6 p-4 rounded-lg border" style="background:color-mix(in srgb,var(--rose-pine-love) 8%,transparent);border-color:color-mix(in srgb,var(--rose-pine-love) 30%,transparent);color:var(--rose-pine-love)">
                    <p class="font-medium text-sm mb-2">Please correct the following errors:</p>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('contact.store') }}" method="POST"
                  x-data="{ submitting: false }" @submit="submitting = true"
                  class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium mb-1.5" style="color:var(--rose-pine-text)">
                        Name <span style="color:var(--rose-pine-love)">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           placeholder="Your name"
                           class="w-full px-4 py-2.5 rounded-lg text-sm transition-colors focus:outline-none focus:ring-2"
                           style="background:var(--rose-pine-surface);border:1px solid var(--rose-pine-overlay);color:var(--rose-pine-text);--tw-ring-color:var(--rose-pine-gold)">
                    @error('name')
                        <p class="mt-1 text-xs" style="color:var(--rose-pine-love)">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium mb-1.5" style="color:var(--rose-pine-text)">
                        Email <span style="color:var(--rose-pine-love)">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           placeholder="you@example.com"
                           class="w-full px-4 py-2.5 rounded-lg text-sm transition-colors focus:outline-none focus:ring-2"
                           style="background:var(--rose-pine-surface);border:1px solid var(--rose-pine-overlay);color:var(--rose-pine-text);--tw-ring-color:var(--rose-pine-gold)">
                    @error('email')
                        <p class="mt-1 text-xs" style="color:var(--rose-pine-love)">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="subject" class="block text-sm font-medium mb-1.5" style="color:var(--rose-pine-text)">
                        Subject <span class="font-normal text-xs" style="color:var(--rose-pine-muted)">(optional)</span>
                    </label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                           placeholder="What's this about?"
                           class="w-full px-4 py-2.5 rounded-lg text-sm transition-colors focus:outline-none focus:ring-2"
                           style="background:var(--rose-pine-surface);border:1px solid var(--rose-pine-overlay);color:var(--rose-pine-text);--tw-ring-color:var(--rose-pine-gold)">
                    @error('subject')
                        <p class="mt-1 text-xs" style="color:var(--rose-pine-love)">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium mb-1.5" style="color:var(--rose-pine-text)">
                        Message <span style="color:var(--rose-pine-love)">*</span>
                    </label>
                    <textarea id="message" name="message" rows="6" required minlength="10"
                              placeholder="Tell me about your project, question, or just say hello..."
                              class="w-full px-4 py-2.5 rounded-lg text-sm transition-colors focus:outline-none focus:ring-2 resize-y"
                              style="background:var(--rose-pine-surface);border:1px solid var(--rose-pine-overlay);color:var(--rose-pine-text);--tw-ring-color:var(--rose-pine-gold)">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-xs" style="color:var(--rose-pine-love)">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" :disabled="submitting"
                        class="w-full py-3 px-6 rounded-lg font-semibold text-sm transition-opacity hover:opacity-85 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer flex items-center justify-center"
                        style="background:var(--rose-pine-gold);color:#111827">
                    <span x-show="!submitting">Send Message</span>
                    <span x-show="submitting" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Sending...
                    </span>
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
