<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class UploadServiceCloud implements UploadServiceCloudInterface
{
    /**
     * Upload an image to cloud storage and return its URL.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    public function uploadImage($file)
    {
        // Définissez le disque cloud dans le fichier config/filesystems.php
        $path = $file->store('images', 's3'); // Changez 's3' si vous utilisez un autre disque

        // Obtenez l'URL publique de l'image stockée
        $url = Storage::disk('s3')->url($path);
        return $url;
    }
}
