<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LogService
{
    public function getLogs(): array
    {
        $files = [];
        foreach (Storage::disk('logs')->allFiles() as $file) {
            if (substr($file, -4) === '.log') {
                $files[] = [
                    'name' => substr($file, 0, strpos($file, '.log')),
                    'modified_at' => Carbon::parse(Storage::disk('logs')->lastModified($file)),
                    'size' => $this->prettySize(Storage::disk('logs')->size($file))
                ];
            }
        }

        return $files;
    }

    public function downloadLog(string $log): RedirectResponse|StreamedResponse
    {
        if (! Storage::disk('logs')->exists("$log.log")) {
            return redirect()->back();
        }
        return Storage::disk('logs')->download("$log.log");
    }

    public function getLogDetails(string $log, int $count = 10): array
    {
        if (! Storage::disk('logs')->exists("$log.log") || $count < 1) {
            return [];
        }

        $details = [];
        $content = Storage::disk('logs')->read("$log.log");
        $pattern = "/^\[(?<date>.*)\]\s(?<env>\w+)\.(?<type>\w+):(?<message>.*)/m";
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $details[] = [
                'date' => $match['date'],
                'env' => $match['env'],
                'type' => $match['type'],
                'message' => $match['message']
            ];
        }
        return array_slice(array_reverse($details), 0, $count);
    }

    private function prettySize(int $size): string
    {
        if ($size >= 1073741824) {
            $size = number_format($size / 1073741824, 2) . ' GB';
        } elseif ($size >= 1048576) {
            $size = number_format($size / 1048576, 2) . ' MB';
        } elseif ($size >= 1024) {
            $size = number_format($size / 1024, 2) . ' KB';
        } elseif ($size > 1) {
            $size = $size . ' байты';
        } elseif ($size == 1) {
            $size = $size . ' байт';
        } else {
            $size = '0 байтов';
        }

        return $size;
    }
}
