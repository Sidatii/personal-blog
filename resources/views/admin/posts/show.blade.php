@extends('layouts.admin')

@section('title', 'Images — ' . $post->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.posts.index') }}"
           class="inline-flex items-center gap-2 text-sm text-rose-pine-subtle hover:text-rose-pine-text transition-colors mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Posts
        </a>
        <h1 class="text-3xl font-bold text-rose-pine-text">Images — {{ $post->title }}</h1>
    </div>

    <!-- Workflow explanation -->
    <div class="mb-8 p-4 bg-rose-pine-surface rounded-lg border border-rose-pine-highlight/30 flex items-start gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-rose-pine-pine flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-sm text-rose-pine-subtle">
            Upload an image, copy the markdown snippet, paste it into your <code class="text-rose-pine-iris bg-rose-pine-overlay px-1 py-0.5 rounded text-xs">.md</code> file in git, then push to sync.
        </p>
    </div>

    <!-- Alpine.js image uploader -->
    <div x-data="{
        uploading: false,
        images: [],
        copied: null,
        error: null,
        uploadFile(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.error = null;
            this.uploading = true;
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', document.querySelector('meta[name=csrf-token]').content);
            fetch('{{ route('admin.posts.images.store', $post->id) }}', {
                method: 'POST',
                body: formData,
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    this.error = data.error;
                } else {
                    this.images.unshift({ url: data.url, path: data.path, markdown: data.markdown });
                }
                this.uploading = false;
                event.target.value = '';
            })
            .catch(() => {
                this.error = 'Upload failed. Please try again.';
                this.uploading = false;
                event.target.value = '';
            });
        },
        copy(markdown, i) {
            navigator.clipboard.writeText(markdown).then(() => {
                this.copied = i;
                setTimeout(() => { this.copied = null; }, 2000);
            });
        }
    }">

        <!-- Upload area -->
        <div class="mb-8 p-6 bg-rose-pine-surface rounded-lg border-2 border-dashed border-rose-pine-highlight/50 hover:border-rose-pine-gold/50 transition-colors">
            <div class="text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-rose-pine-muted mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <label class="cursor-pointer">
                    <span class="px-4 py-2 bg-rose-pine-gold text-rose-pine-base rounded-lg hover:bg-opacity-80 transition font-semibold text-sm"
                          x-text="uploading ? 'Uploading...' : 'Choose Image'">
                    </span>
                    <input type="file" class="hidden" accept="image/*" @change="uploadFile($event)" :disabled="uploading">
                </label>
                <p class="mt-3 text-xs text-rose-pine-muted">JPEG, PNG, GIF, WebP, SVG — max 4 MB</p>
            </div>

            <!-- Error message -->
            <div x-show="error" x-cloak class="mt-4 p-3 bg-rose-pine-love/20 border border-rose-pine-love/40 rounded-lg text-sm text-rose-pine-love text-center" x-text="error"></div>
        </div>

        <!-- Uploaded images -->
        <div class="space-y-4">
            <template x-if="images.length === 0">
                <p class="text-center text-rose-pine-muted text-sm py-8">No images uploaded yet for this post.</p>
            </template>

            <template x-for="(img, i) in images" :key="img.path">
                <div class="flex items-start gap-4 p-4 bg-rose-pine-surface rounded-lg">
                    <!-- Thumbnail -->
                    <img :src="img.url" :alt="img.path" class="w-24 h-24 object-cover rounded-lg flex-shrink-0 border border-rose-pine-highlight/30">

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-rose-pine-muted mb-2 font-medium">Markdown snippet</p>
                        <code class="block bg-rose-pine-overlay text-rose-pine-iris text-xs p-3 rounded-lg break-all font-mono" x-text="img.markdown"></code>
                    </div>

                    <!-- Copy button -->
                    <button
                        @click="copy(img.markdown, i)"
                        class="flex-shrink-0 px-4 py-2 rounded-lg text-sm font-semibold transition"
                        :class="copied === i
                            ? 'bg-rose-pine-foam/20 text-rose-pine-foam border border-rose-pine-foam/40'
                            : 'bg-rose-pine-gold text-rose-pine-base hover:bg-opacity-80'"
                        x-text="copied === i ? 'Copied \u2713' : 'Copy'">
                    </button>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection
