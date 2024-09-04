<?php

namespace App\Services;

interface UploadServiceCloudInterface
{
    /**
     * Upload an image to cloud storage and return its URL.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    public function uploadImage($file);
}
