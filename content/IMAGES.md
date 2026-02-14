# Working with Images in Blog Posts

This project uses a **git-based image workflow** where images are stored alongside your markdown content and automatically synced to the web server.

## Quick Start

1. **Add images** to the `content/images/` folder in your git repository
2. **Reference images** in your markdown using relative paths: `![Alt text](images/screenshot.png)`
3. **Push to git** - images sync automatically via webhook
4. **Images display** on your blog posts without manual intervention

## Directory Structure

```
content/
├── posts/
│   ├── hello-world.md
│   └── my-tutorial.md
└── images/
    ├── screenshot.png
    ├── diagram.svg
    └── architecture.jpg
```

## Referencing Images

In your markdown files, use relative paths starting with `images/`:

```markdown
# My Tutorial

Here's a screenshot of the dashboard:

![Dashboard Screenshot](images/screenshot.png)

You can also use subdirectories:

![Architecture Diagram](images/diagrams/system-arch.png)
```

**Supported formats:** PNG, JPG, JPEG, GIF, SVG, WebP

## How It Works

### 1. Git Storage (Source of Truth)
- All images live in `content/images/` in your git repository
- This keeps content and images versioned together
- Easy to track changes and collaborate

### 2. Automatic Sync
When you push to git:
- Webhook triggers `SyncContentFromGitJob`
- Posts are indexed from `content/posts/`
- Images are copied from `content/images/` to `storage/app/public/content/images/`
- Images removed from git are also removed from storage

### 3. Path Resolution
When rendering posts, the markdown parser automatically transforms:
- Markdown: `![Alt](images/screenshot.png)`
- HTML: `<img src="/storage/content/images/images/screenshot.png" alt="Alt">`

The parser handles various path formats:
- `images/file.png` ✓
- `./images/file.png` ✓
- `/images/file.png` ✓

## Syncing Manually

If you need to sync images manually (e.g., after adding images locally):

```bash
# Sync everything (posts + images)
php artisan content:sync

# Force re-sync all content
php artisan content:sync --force
```

## Best Practices

### Naming Images
- Use descriptive names: `dashboard-dark-mode.png` instead of `img1.png`
- Use lowercase with hyphens: `system-architecture.png`
- Avoid spaces in filenames

### Organizing Images
- Flat structure is fine for small blogs: all images in `content/images/`
- Use subdirectories for large blogs: `content/images/tutorials/`, `content/images/projects/`
- Keep related images together

### Image Optimization
- Optimize images before committing (use tools like ImageOptim, TinyPNG)
- Use appropriate formats:
  - **PNG**: Screenshots, diagrams, images with transparency
  - **JPG**: Photographs
  - **SVG**: Icons, logos, diagrams
  - **WebP**: Modern alternative for photos (smaller file size)

### Size Guidelines
- **Hero images**: 1200x630px (good for social sharing)
- **Inline images**: Max 800px width
- **Thumbnails**: 400x300px
- Keep individual files under 500KB when possible

## Example Post

```markdown
---
title: "Building a Real-time Dashboard"
slug: "real-time-dashboard"
published_at: "2026-02-14"
category: "tutorials"
tags: ["laravel", "vue", "websockets"]
---

# Building a Real-time Dashboard

In this tutorial, we'll build a real-time analytics dashboard.

## Architecture

Here's the system architecture:

![System Architecture](images/architecture.png)

## Dashboard Preview

The final dashboard looks like this:

![Dashboard Preview](images/dashboard-preview.png)

## Key Components

1. **WebSocket Server** - Handles real-time updates
2. **Vue.js Frontend** - Reactive UI components
3. **Laravel Backend** - API and data aggregation

![Component Diagram](images/component-diagram.svg)

## Conclusion

That's it! You now have a working real-time dashboard.
```

## Troubleshooting

### Images not showing after sync

1. Check that images exist in git: `git ls-files content/images/`
2. Verify sync ran: Check logs or run `php artisan content:sync`
3. Check storage: `ls -la storage/app/public/content/images/`
4. Ensure storage link exists: `php artisan storage:link`

### Wrong image paths

Make sure you're using relative paths starting with `images/`:

✓ Correct: `![Alt](images/screenshot.png)`  
✗ Wrong: `![Alt](/images/screenshot.png)`  
✗ Wrong: `![Alt](screenshot.png)`  
✗ Wrong: `![Alt](https://example.com/image.png)` (external URLs work but bypass the sync)

### Images too large

- Optimize before committing: `npx imagemin content/images/* --out-dir=content/images/`
- Or use a tool like ImageOptim, Squoosh, or TinyPNG

### Storage permission errors

```bash
# Fix storage permissions
chmod -R 775 storage/app/public/content/images
chown -R www-data:www-data storage/app/public/content/images
```

## Migration from Old System

If you previously used the admin image manager:

1. Download your existing images from `storage/app/public/uploads/blog/`
2. Move them to `content/images/` in your git repo
3. Update markdown references from absolute URLs to relative paths:
   - Old: `![Alt](/storage/uploads/blog/image.png)`
   - New: `![Alt](images/image.png)`
4. Commit and push

## Admin Panel Changes

The old "per-post image manager" has been **removed** in favor of this git-based workflow:

- No more manual uploads in admin
- No more copying markdown snippets
- Images are just files in your repo

The admin panel still shows:
- **Posts list** - Read-only view of all posts
- But no "Manage Images" links (images are now managed via git)

## Advanced: Custom Image Directories

If you need images in subdirectories:

```
content/images/
├── tutorials/
│   ├── step-1.png
│   └── step-2.png
├── projects/
│   └── dashboard.jpg
└── logo.png
```

Reference them with full path:

```markdown
![Step 1](images/tutorials/step-1.png)
![Dashboard](images/projects/dashboard.jpg)
```

## Questions?

- Review the [PROJECT.md](PROJECT.md) for architecture details
- Check the [deployment guide](DEPLOYMENT.md) for webhook setup
- The sync process is automatic via webhook, but you can always run `php artisan content:sync` manually
