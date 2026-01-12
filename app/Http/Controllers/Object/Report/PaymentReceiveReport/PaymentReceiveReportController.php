<?php

namespace App\Http\Controllers\Object\Report\PaymentReceiveReport;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\Object\Report\PaymentReceiveReportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PaymentReceiveReportController extends Controller
{
    public function __construct(private PaymentReceiveReportService $paymentReceiveReportService) {}

    public function index(BObject $object, Request $request): View
    {
        $years = ['2026', '2025', '2024', '2023', '2022'];
        $year = $request->get('year', date('Y'));
        $reportInfo = $this->paymentReceiveReportService->getReportInfo($object, ['year' => $year]);

        return view(
            'objects.tabs.payment_receive_report',
            compact(
                'object', 'reportInfo', 'year', 'years'
            )
        );
    }
}
