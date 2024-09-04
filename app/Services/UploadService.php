<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class UploadService
{
    /**
     * Upload an image and return its base64 encoded string.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    public function uploadImage($file)
    {
        $path = $file->store('images', 'public');
        $base64 = base64_encode(Storage::disk('public')->get($path));
        return 'data:image/' . $file->extension() . ';base64,' . $base64;
    }
}
