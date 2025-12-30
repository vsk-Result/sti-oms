<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    public function index(Request $request): StreamedResponse
    {
        if ($request->has('name')) {
            return Storage::disk('public')->download(base64_decode($request->file), $request->name);
        }

        return Storage::disk('public')->download(base64_decode($request->file));
    }
}
