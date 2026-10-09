<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    /**
     * Upload a single image and return its public URL.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
        ]);

        $path = $request->file('image')->store('uploads', 'public');
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

        $path = $file->store('uploads', 'public');
        $url  = '/storage/' . $path;

        return response()->json([
            'url' => $url,
            'type' => $type,
        ]);
    }
}
