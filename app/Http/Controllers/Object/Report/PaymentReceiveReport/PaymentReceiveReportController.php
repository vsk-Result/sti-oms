<?php

namespace App\Http\Controllers\Object\Report\PaymentReceiveReport;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\Object\Report\PaymentReceiveReportService;
use Illuminate\Contracts\View\View;

class PaymentReceiveReportController extends Controller
{
    public function __construct(private PaymentReceiveReportService $paymentReceiveReportService) {}

    public function index(BObject $object): View
    {
        $info = $this->paymentReceiveReportService->getReportInfo($object);

        return view(
            'objects.tabs.payment_receive_report',
            compact('object', 'info')
        );
    }
}
