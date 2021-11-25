<?php

namespace App\Http\Controllers\PaymentImport;

use App\Exports\Payment\PaymentExport;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportAllController extends Controller
{
    public function store(Request $request): BinaryFileResponse
    {
        return Excel::download(
            new PaymentExport(
                Payment::whereIn('import_id', json_decode($request->input('payment_imports_ids')))
                    ->with('object', 'company', 'organizationReceiver', 'organizationSender')
                    ->get()
            ),
            'Экспорт выбранных загрузок оплат.xlsx'
        );
    }
}
