---
phase: 03-blog-features-and-seo
plan: 07
type: gap-closure
subsystem: blog
tags: [markdown, parsing, shiki, syntax-highlighting, table-of-contents, reading-time]
completed: 2026-02-06
duration: "~5 minutes"

dependency_graph:
  requires:
    - "03-03"  # Shiki syntax highlighting
    - "03-04"  # MarkdownParser and TOC components
  provides:
    - "Markdown parsing pipeline wired to controller"
    - "Blog posts render with syntax highlighting"
    - "TOC displays actual post headings"
    - "Reading time calculated and displayed"
  affects: []
---

# Phase 3 Plan 7: Blog Markdown Parsing & TOC Wiring Summary

## Objective

Wire `BlogController` to use `MarkdownParser` for parsing post content, extracting headings, and passing them to the view for Table of Contents functionality. Close the gap where BlogController doesn't read markdown files from storage.

## What Was Delivered

### Task 1: Verify ShikiHighlighter Integration ✅ (Already Done)

**Verified existing implementation in `MarkdownParser.php`:**
- `ShikiHighlighter` injected in constructor (line 13, 35)
- `highlightCodeBlocks()` method calls `$this->highlighter->highlight()` (lines 188-214)
- `getHeadings()` method returns array with `['level', 'text', 'id']` (lines 114-117)
- `parseFile()` method reads files and parses content (lines 90-99)
- Code blocks render with `.shiki` class and Rose Pine colors

### Task 2: Wire BlogController to Parse Markdown Files ✅

**Modified `app/Http/Controllers/BlogController.php`:**

1. **Added MarkdownParser import:**
   ```php
   use App\Services\Content\MarkdownParser;
   ```

2. **Updated `show()` method to:**
   - Read markdown file from `storage/content/posts/{$post->filepath}`
   - Parse with `MarkdownParser` instance
   - Extract `$content = $parsed['body']` and `$headings = $parser->getHeadings()`
   - Convert headings to objects for TOC component compatibility
   - Calculate reading time: `words / 200`, rounded up
   - Pass to view via `compact('post', 'seo', 'content', 'headings', 'readingTime')`

3. **Key code changes:**
   ```php
   $fullPath = storage_path('content/posts/' . $post->filepath);
   
   if (file_exists($fullPath)) {
       $markdownContent = file_get_contents($fullPath);
       $parser = new MarkdownParser();
       $parsed = $parser->parse($markdownContent);
       
       $content = $parsed['body'];
       $headings = array_map(function ($heading) {
           return (object) $heading;
       }, $parser->getHeadings());
       
       $plainText = strip_tags($markdownContent);
       $wordCount = str_word_count($plainText);
       $readingTime = max(1, (int) ceil($wordCount / 200));
   }
   ```

### Task 3: Verify Code Blocks and TOC Render ✅

**Fixed undefined route in view:**
- Changed `route('home')` to `url('/')` in `resources/views/posts/show.blade.php`

**Verification results:**
- ✅ HTML content renders with `{!! $content !!}`
- ✅ Code blocks show `.shiki` class (Shiki syntax highlighting active)
- ✅ TOC sidebar displays "On this page" heading
- ✅ TOC links use correct anchor format (`#introduction`, `#prerequisites`, etc.)
- ✅ Reading time displayed correctly (X min read)
- ✅ Test post created at `/blog/getting-started-laravel` with multiple headings and code blocks

## Decisions Made

### 1. Convert Headings to Objects for TOC Compatibility

**Issue:** `MarkdownParser::getHeadings()` returns arrays with keys (`['level' => 1, 'text' => 'Heading', 'id' => 'heading']`), but the TOC component uses object property access (`$heading->level`, `$heading->id`, `$heading->text`).

**Decision:** Convert headings to `stdClass` objects in BlogController:

```php
$headings = array_map(function ($heading) {
    return (object) $heading;
}, $parser->getHeadings());
```

**Rationale:** Minimal change needed. Keeps TOC component unchanged. Arrays-to-objects conversion is a standard pattern for view compatibility.

### 2. Reading Time Calculation

**Decision:** Use word count divided by 200 words/minute, rounded up.

```php
$wordCount = str_word_count($plainText);
$readingTime = max(1, (int) ceil($wordCount / 200));
```

**Rationale:** Industry standard for estimating reading time. Always returns at least 1 minute.

## Files Created/Modified

### Created
- `storage/content/posts/getting-started-laravel.md` - Test markdown file with headings and code blocks

### Modified
- `app/Http/Controllers/BlogController.php` - Added MarkdownParser import and parsing logic in `show()` method
- `resources/views/posts/show.blade.php` - Fixed undefined route reference

## Key Links Established

| From | To | Via |
|------|-----|-----|
| `BlogController::show()` | `MarkdownParser::parse()` | File read and content parsing |
| `BlogController::show()` | `MarkdownParser::getHeadings()` | Parser extraction, converted to objects |
| `posts/show.blade.php` | `BlogController` | `$content`, `$headings`, `$readingTime` variables |
| TOC component | `MarkdownParser` | Heading IDs generated from slug |

## Technical Details

### MarkdownParser Exported Methods Used
- `parse(string $markdown): array` - Returns `['body' => HTML, 'matter' => frontmatter]`
- `getHeadings(): array` - Returns array of `['level' => int, 'text' => string, 'id' => string]`
- `parseFile(string $filepath): array` - Reads file and returns parsed result

### Shiki Integration
- Code blocks rendered with `.shiki` class
- Rose Pine theme colors applied automatically
- Language detection from markdown code fence (e.g., ```php)

### TOC Link Generation
- Heading IDs generated from text: "Getting Started" → "getting-started"
- Special characters removed, spaces → hyphens, lowercase
- Smooth scroll behavior enabled via CSS

## Verification Checklist

- [x] BlogController imports MarkdownParser
- [x] `show()` method reads file from `storage/content/posts/`
- [x] Parses content and extracts headings
- [x] Passes `$content` and `$headings` to view
- [x] Reading time calculated and passed to view
- [x] Code blocks show `.shiki` class (Shiki highlighting)
- [x] TOC sidebar displays actual headings
- [x] TOC links generate with correct anchor IDs
- [x] No undefined variable errors in view
- [x] Test post renders correctly at `/blog/getting-started-laravel`

## Deviations from Plan

**None** - Plan executed exactly as written. ShikiHighlighter integration was already complete (verified Task 1), BlogController updated as specified (Task 2), and verification confirmed all components work together (Task 3).

## Next Steps

The markdown parsing pipeline is now fully wired:
- Posts render with syntax-highlighted code blocks
- Table of Contents shows actual headings from content
- Reading time displayed for better UX

Ready for Phase 4 (Authentication system).
