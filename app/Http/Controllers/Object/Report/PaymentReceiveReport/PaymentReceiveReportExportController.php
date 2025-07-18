<?php

namespace App\Http\Controllers\Object\Report\PaymentReceiveReport;

use App\Exports\Object\Report\PaymentReceiveReport\Export;
use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\Object\Report\PaymentReceiveReportService;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PaymentReceiveReportExportController extends Controller
{
    public function __construct(private PaymentReceiveReportService $paymentReceiveReportService) {}

    public function store(BObject $object): BinaryFileResponse
    {
        $reportInfo = $this->paymentReceiveReportService->getReportInfo($object);

        return Excel::download(
            new Export($reportInfo),
            $object->code . '_Отчет_доходов_и_расходов.xlsx'
        );
    }
}
