<?php

namespace App\Http\Controllers\Finance\DistributionTransferService;

use App\Exports\Finance\DistributionTransferService\Export;
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
            'Распределение услуг по трансферу ' . Carbon::now()->format('d-m-Y') . '.xlsx'
        );
    }
}
