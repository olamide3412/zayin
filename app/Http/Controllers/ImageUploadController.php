<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageUploadController extends Controller
{
    private ?ImageManager $imageManager = null;

    private function getImageManager(): ImageManager
    {
        if (!$this->imageManager) {
            $this->imageManager = new ImageManager(new Driver());
        }
        return $this->imageManager;
    }

    /**
     * Compress an image file: resize if overly large and encode as optimized WebP or JPEG.
     */
    private function compressAndSaveImage($file, string $directory = 'uploads'): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        // For GIFs (which might be animated) or unsupported formats, save as-is
        if ($extension === 'gif') {
            return $file->store($directory, 'public');
        }

        try {
            $manager = $this->getImageManager();
            $image = $manager->read($file->getRealPath());

            // Scale down if width or height exceeds 1920px, preserving aspect ratio
            if ($image->width() > 1920 || $image->height() > 1920) {
                $image->scaleDown(width: 1920, height: 1920);
            }

            // Encode to modern WebP with 80% quality for drastic byte-size reduction
            // If the client uploaded PNG with transparency, WebP supports transparency while cutting size by ~70-80%
            $encoded = $image->toWebp(quality: 80);

            $filename = Str::random(40) . '.webp';
            $path = $directory . '/' . $filename;

            Storage::disk('public')->put($path, (string) $encoded);

            return $path;
        } catch (\Throwable $e) {
            // Fallback to direct store if GD fails on a specific format
            return $file->store($directory, 'public');
        }
    }

    /**
     * Upload a single image and return its public URL.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB input max, will be compressed
        ]);

        $path = $this->compressAndSaveImage($request->file('image'), 'uploads');
        $url  = '/storage/' . $path;

        return response()->json(['url' => $url]);
    }

    /**
     * Upload a photo or video and return its public URL & type.
     */
    public function uploadMedia(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,webp,mp4,mov,ogg,qt,webm,avi|max:51200', // 50MB max
        ]);

        $file = $request->file('file');
        $mime = $file->getMimeType();
        $type = str_contains($mime, 'video') ? 'video' : 'image';

        if ($type === 'image') {
            $path = $this->compressAndSaveImage($file, 'uploads');
        } else {
            // Video file - stored directly
            $path = $file->store('uploads', 'public');
        }

        $url  = '/storage/' . $path;

        return response()->json([
            'url' => $url,
            'type' => $type,
        ]);
    }
}

