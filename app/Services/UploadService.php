<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadService
{
    public function uploadFile(string $path, UploadedFile $file, ?string $fileName = null): string
    {
        if (! is_null($fileName)) {
            return Storage::putFileAs($path, $file, $fileName);
        }

        return Storage::putFile($path, $file);
    }
}
