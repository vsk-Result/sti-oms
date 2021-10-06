<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadService
{
    public function uploadFile(string $path, UploadedFile $file): string
    {
        return Storage::putFile($path, $file);
    }
}
