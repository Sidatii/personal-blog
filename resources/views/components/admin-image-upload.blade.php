@props([
    'name',
    'directory',
    'current' => null,
    'label' => 'Image',
])

<div x-data="{
    uploading: false,
    preview: {{ $current ? "'$current'" : 'null' }},
    path: '',
    error: '',
    async handleFile(event) {
        const file = event.target.files[0];
        if (!file) return;

        this.uploading = true;
        this.error = '';

        const formData = new FormData();
        formData.append('file', file);
        formData.append('directory', '{{ $directory }}');

        try {
            const response = await fetch('{{ route('admin.images.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            if (!response.ok) {
                if (response.status === 413) {
                    this.error = 'File too large. Your server limits uploads to less than 4 MB. Ask your host to raise client_max_body_size.';
                } else if (response.status === 419) {
                    this.error = 'Session expired. Please refresh the page and try again.';
                } else {
                    let msg = 'Upload failed. Please try again.';
                    try { const d = await response.json(); msg = d.error || msg; } catch {}
                    this.error = msg;
                }
            } else {
                const data = await response.json();
                this.path = data.path;
                this.preview = data.url;
            }
        } catch (err) {
            this.error = 'Upload failed. Please check your connection and try again.';
        } finally {
            this.uploading = false;
        }
    }
}">
    <label class="block text-sm font-medium text-rose-pine-text mb-2">
        {{ $label }}
    </label>

    {{-- Hidden input stores the path for form submission --}}
    <input type="hidden" name="{{ $name }}" :value="path">

    {{-- Current image preview --}}
    <div x-show="preview" class="mb-3">
        <img :src="preview" alt="Image preview"
             class="max-h-48 rounded-lg border border-rose-pine-base/30 object-contain bg-rose-pine-surface p-1">
    </div>

    {{-- File input --}}
    <div class="flex items-center gap-3">
        <label class="cursor-pointer">
            <span class="inline-flex items-center px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg hover:border-rose-pine-gold transition-colors text-sm font-medium"
                  :class="{ 'opacity-50 cursor-not-allowed': uploading }">
                <span x-show="!uploading">Choose file</span>
                <span x-show="uploading">Uploading...</span>
            </span>
            <input type="file"
                   accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml"
                   class="sr-only"
                   :disabled="uploading"
                   @change="handleFile($event)">
        </label>

        <span x-show="preview && !uploading" class="text-sm text-rose-pine-subtle">
            Image uploaded successfully.
        </span>
    </div>

    {{-- Error message --}}
    <p x-show="error" x-text="error" class="mt-2 text-sm text-rose-pine-love"></p>

    <p class="mt-1 text-sm text-rose-pine-subtle">
        Accepted formats: JPEG, PNG, GIF, WebP, SVG. Maximum size: 4 MB.
    </p>
</div>
