<?php

namespace App\Http\Controllers\PaymentImport;

use App\Exports\Payment\PaymentExport;
use App\Http\Controllers\Controller;
use App\Models\PaymentImport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function store(PaymentImport $import): BinaryFileResponse
    {
        return Excel::download(
            new PaymentExport(
                $import->payments()->with('object', 'company', 'organizationReceiver', 'organizationSender')->get()
            ),
            '(' . $import->getType() . ') Экспорт оплат за ' . $import->getDateFormatted('d.m.Y') . '.xlsx'
        );
    }
}
