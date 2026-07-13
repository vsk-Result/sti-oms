<?php

namespace App\Services\Object;

use App\Models\Object\BObject;
use App\Services\UploadService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ObjectFilesService
{
    public function __construct(private UploadService $uploadService) {}

    public function getObjectFiles(BObject $object): Collection
    {
       return collect(Storage::files($object->getFilesPath()))
           ->map(fn ($file) => (object) [
               'filename' => basename($file),
               'extension' => pathinfo($file, PATHINFO_EXTENSION),
               'size' => $this->formatBytes(Storage::size($file)),
               'last_modified' => Carbon::createFromTimestamp(
                   Storage::lastModified($file)
               ),
               'url' => Storage::url($file),
           ]);
    }

    public function uploadObjectFile(BObject $object, UploadedFile $file): void
    {
        $this->uploadService->uploadFile(
            $object->getFilesPath(),
            $file,
            $file->getClientOriginalName()
        );
    }

    public function destroyObjectFile(BObject $object, string $filename): void
    {
        $filepath = $object->getFilesPath() . '/' . $filename;

        if (Storage::exists($filepath)) {
            Storage::delete($filepath);
        }
    }

    private function formatBytes($bytes): string
    {
        $units = ['б', 'кб', 'мб', 'гб', 'тб'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
