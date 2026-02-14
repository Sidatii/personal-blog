@extends('layouts.admin')

@section('title', 'Edit Project')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-rose-pine-text">Edit Project</h1>
        <p class="text-rose-pine-subtle mt-2">Update the details for {{ $project->title }}.</p>
    </div>

    <form action="{{ route('admin.projects.update', $project) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Title -->
        <div>
            <label for="title" class="block text-sm font-medium text-rose-pine-text mb-2">
                Title <span class="text-rose-pine-love">*</span>
            </label>
            <input type="text"
                   name="title"
                   id="title"
                   value="{{ old('title', $project->title) }}"
                   required
                   class="w-full px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('title') border-rose-pine-love @enderror">
            @error('title')
            <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
            @enderror
        </div>

        <!-- Slug -->
        <div>
            <label for="slug" class="block text-sm font-medium text-rose-pine-text mb-2">
                Slug <span class="text-rose-pine-love">*</span>
            </label>
            <input type="text"
                   name="slug"
                   id="slug"
                   value="{{ old('slug', $project->slug) }}"
                   required
                   class="w-full px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('slug') border-rose-pine-love @enderror">
            @error('slug')
            <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
            @enderror
        </div>

        <!-- Short Description -->
        <div>
            <label for="short_description" class="block text-sm font-medium text-rose-pine-text mb-2">
                Short Description <span class="text-rose-pine-love">*</span>
            </label>
            <textarea name="short_description"
                      id="short_description"
                      rows="2"
                      required
                      maxlength="500"
                      class="w-full px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('short_description') border-rose-pine-love @enderror">{{ old('short_description', $project->short_description) }}</textarea>
            @error('short_description')
            <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-rose-pine-subtle">Maximum 500 characters for project card display.</p>
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-rose-pine-text mb-2">
                Full Description
            </label>
            <textarea name="description"
                      id="description"
                      rows="8"
                      class="w-full px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('description') border-rose-pine-love @enderror">{{ old('description', $project->description) }}</textarea>
            @error('description')
            <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-rose-pine-subtle">Detailed project information for the project page.</p>
        </div>

        <!-- Tech Stack -->
        <div x-data="{
            tags: {{ json_encode(old('tech_stack', $project->tech_stack ?? [])) }},
            newTag: '',
            addTag() {
                if (this.newTag.trim() && !this.tags.includes(this.newTag.trim())) {
                    this.tags.push(this.newTag.trim());
                    this.newTag = '';
                }
            },
            removeTag(index) {
                this.tags.splice(index, 1);
            }
        }">
            <label class="block text-sm font-medium text-rose-pine-text mb-2">
                Tech Stack
            </label>
            <div class="flex gap-2 mb-2">
                <input type="text"
                       x-model="newTag"
                       @keydown.enter.prevent="addTag()"
                       placeholder="Add technology (e.g., Laravel, Vue.js)"
                       class="flex-1 px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold">
                <button type="button"
                        @click="addTag()"
                        class="px-4 py-2 bg-rose-pine-gold text-rose-pine-base rounded-lg hover:bg-opacity-80 transition">
                    Add
                </button>
            </div>
            <div class="flex flex-wrap gap-2 min-h-[40px] p-3 bg-rose-pine-surface border border-rose-pine-base/30 rounded-lg">
                <template x-for="(tag, index) in tags" :key="index">
                    <div class="flex items-center gap-1 px-3 py-1 bg-rose-pine-gold text-rose-pine-base font-semibold rounded-full text-sm">
                        <span x-text="tag"></span>
                        <button type="button"
                                @click="removeTag(index)"
                                class="ml-1 text-rose-pine-love hover:text-rose-pine-gold">×</button>
                        <input type="hidden" name="tech_stack[]" :value="tag">
                    </div>
                </template>
                <template x-if="tags.length === 0">
                    <span class="text-rose-pine-subtle text-sm">No technologies added yet</span>
                </template>
            </div>
            @error('tech_stack')
            <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
            @enderror
        </div>

        <!-- Status and Featured Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-rose-pine-text mb-2">
                    Status <span class="text-rose-pine-love">*</span>
                </label>
                <select name="status"
                        id="status"
                        required
                        class="w-full px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('status') border-rose-pine-love @enderror">
                    <option value="active" {{ old('status', $project->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="in-progress" {{ old('status', $project->status) == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="archived" {{ old('status', $project->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
                @error('status')
                <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
                @enderror
            </div>

            <!-- Featured -->
            <div class="flex items-center">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox"
                           name="is_featured"
                           id="is_featured"
                           value="1"
                           {{ old('is_featured', $project->is_featured) ? 'checked' : '' }}
                           class="rounded border-rose-pine-base/30 text-rose-pine-gold focus:ring-rose-pine-gold focus:ring-offset-rose-pine-surface">
                    <span class="text-sm font-medium text-rose-pine-text">Mark as Featured Project</span>
                </label>
            </div>
        </div>

        <!-- URLs -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Live URL -->
            <div>
                <label for="live_url" class="block text-sm font-medium text-rose-pine-text mb-2">
                    Live URL
                </label>
                <input type="url"
                       name="live_url"
                       id="live_url"
                       value="{{ old('live_url', $project->live_url) }}"
                       placeholder="https://example.com"
                       class="w-full px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('live_url') border-rose-pine-love @enderror">
                @error('live_url')
                <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
                @enderror
            </div>

            <!-- GitHub URL -->
            <div>
                <label for="github_url" class="block text-sm font-medium text-rose-pine-text mb-2">
                    GitHub URL
                </label>
                <input type="url"
                       name="github_url"
                       id="github_url"
                       value="{{ old('github_url', $project->github_url) }}"
                       placeholder="https://github.com/username/repo"
                       class="w-full px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold @error('github_url') border-rose-pine-love @enderror">
                @error('github_url')
                <p class="mt-1 text-sm text-rose-pine-love">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Thumbnail -->
        <x-admin-image-upload name="thumbnail" directory="uploads/projects" label="Thumbnail Image" :current="$project->thumbnail_url ?? null" />

        <!-- Screenshots -->
        <div
            x-data="{
                screenshots: @json(collect($project->screenshots ?? [])->map(fn($p) => ['path' => $p, 'url' => asset('storage/' . $p)])->values()->all()),
                async upload(event) {
                    const files = Array.from(event.target.files);
                    for (const file of files) {
                        const form = new FormData();
                        form.append('file', file);
                        form.append('directory', 'uploads/projects');
                        form.append('_token', document.querySelector('meta[name=csrf-token]').content);
                        const res = await fetch('{{ route('admin.images.store') }}', { method: 'POST', body: form });
                        const data = await res.json();
                        if (res.ok) this.screenshots.push({ path: data.path, url: data.url });
                    }
                    event.target.value = '';
                },
                remove(index) { this.screenshots.splice(index, 1); }
            }"
            class="space-y-2"
        >
            <label class="block text-sm font-medium text-rose-pine-text">Screenshots</label>
            <input type="file" accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml" multiple @change="upload($event)"
                   class="w-full px-4 py-2 bg-rose-pine-surface text-rose-pine-text border border-rose-pine-base/30 rounded-lg focus:outline-none focus:border-rose-pine-gold text-sm cursor-pointer">
            <div class="flex flex-wrap gap-2 mt-2">
                <template x-for="(s, i) in screenshots" :key="i">
                    <div class="relative">
                        <img :src="s.url" class="h-20 w-28 object-cover rounded border border-rose-pine-muted">
                        <button type="button" @click="remove(i)"
                                class="absolute top-0 right-0 bg-rose-pine-love text-white text-xs rounded-full w-5 h-5 flex items-center justify-center leading-none">&#x2715;</button>
                        <input type="hidden" name="screenshots[]" :value="s.path">
                    </div>
                </template>
            </div>
            <p class="text-sm text-rose-pine-subtle">Select multiple images to upload as project screenshots. Existing screenshots are shown above.</p>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-rose-pine-base/30">
            <a href="{{ route('admin.projects.index') }}"
               class="px-6 py-2 text-rose-pine-text hover:text-rose-pine-gold transition">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-rose-pine-gold text-rose-pine-base rounded-lg hover:bg-opacity-80 transition">
                Update Project
            </button>
        </div>
    </form>
</div>
@endsection
