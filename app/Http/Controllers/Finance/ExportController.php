<?php

namespace App\Http\Controllers\Finance;

use App\Exports\Finance\GeneralCosts\Export;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function store(): BinaryFileResponse
    {
        return Excel::download(
            new Export(),
            'Общие затраты на ' . Carbon::now()->format('d-m-Y') . '.xlsx'
        );
    }
}
