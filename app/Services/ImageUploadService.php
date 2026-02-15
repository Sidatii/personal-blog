<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
    private const MAX_SIZE_BYTES = 4 * 1024 * 1024;

    public function upload(UploadedFile $file, string $directory): string
    {
        $this->validate($file);
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $filename, 'public');

        if ($path === false) {
            throw new \RuntimeException('Failed to store the uploaded file. Check that storage/app/public is writable by the web server.');
        }

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function url(string $path): string
    {
        return Storage::disk('public')->url($path);
    }

    private function validate(UploadedFile $file): void
    {
        if (!in_array($file->getMimeType(), self::ALLOWED_MIMES, true)) {
            throw new \InvalidArgumentException('Invalid image type. Allowed: JPEG, PNG, GIF, WebP, SVG.');
        }
        if ($file->getSize() > self::MAX_SIZE_BYTES) {
            throw new \InvalidArgumentException('Image too large. Maximum size is 4 MB.');
        }
    }
}
