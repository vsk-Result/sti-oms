<?php

namespace App\Http\Controllers\Organization;

use App\Exports\Organization\Export;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function store(): BinaryFileResponse
    {
        return Excel::download(new Export(), 'Контрагенты.xlsx');
    }
}
