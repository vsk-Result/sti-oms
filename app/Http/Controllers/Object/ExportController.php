<?php

namespace App\Http\Controllers\Object;

use App\Exports\Object\Export;
use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function store(BObject $object): BinaryFileResponse
    {
        $exportFileName = $object->getName() . '.xlsx';
        return Excel::download(new Export($object), $exportFileName);
    }
}
