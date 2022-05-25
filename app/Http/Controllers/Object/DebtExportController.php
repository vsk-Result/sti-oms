<?php

namespace App\Http\Controllers\Object;

use App\Exports\Debt\Export;
use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DebtExportController extends Controller
{
    public function store(BObject $object): BinaryFileResponse
    {
        return Excel::download(new Export($object), 'Экспорт долгов по объекту ' . $object->getName() . '.xlsx');
    }
}
