<?php

namespace App\Http\Controllers\Object\Report\PaymentReceiveReport;

use App\Exports\Object\Report\PaymentReceiveReport\Export;
use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\Object\Report\PaymentReceiveReportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PaymentReceiveReportExportController extends Controller
{
    public function __construct(private PaymentReceiveReportService $paymentReceiveReportService) {}

    public function store(BObject $object, Request $request): BinaryFileResponse
    {
        $year = $request->get('year', date('Y'));
        $reportInfo = $this->paymentReceiveReportService->getReportInfo($object, ['year' => $year]);

        return Excel::download(
            new Export($reportInfo, $year),
            'Отчет о доходах и расходах на ' . now()->format('d.m.Y') . ' по объекту ' . $object->getName() . '.xlsx'
        );
    }
}
