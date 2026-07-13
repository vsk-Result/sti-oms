<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\Object\ObjectFilesService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function __construct(private ObjectFilesService $objectFilesService) {}

    public function index(BObject $object): View
    {
        $files = $this->objectFilesService->getObjectFiles($object);

        return view('objects.tabs.files', compact('object', 'files'));
    }

    public function store(BObject $object, Request $request): RedirectResponse | JsonResponse
    {
        $this->objectFilesService->uploadObjectFile($object, $request->file);

        if ($request->ajax()) {
            return response()->json();
        }

        return redirect()->back();
    }

    public function destroy(BObject $object, Request $request): RedirectResponse
    {
        $this->objectFilesService->destroyObjectFile($object, $request->get('filename'));

        return redirect()->back();
    }
}
