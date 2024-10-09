<?php

namespace App\Http\Controllers\Pivot\MoneyMovement;

use App\Exports\Pivot\MoneyMovement\Export;
use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function store(Request $request): BinaryFileResponse
    {
        $payments = $this->paymentService->filterPayments(
            [
                'object_id' => $request->get('object_id', BObject::active(['27.1'])->orderBy('code')->pluck('id')->toArray()),
                'period' => $request->get('period', []),
                'bank_id' => $request->get('bank_id', []),
                'organization_id' => $request->get('organization_id', []),
                'sort_by' => 'object_id',
            ]
        );
        return Excel::download(new Export($payments), 'Отчет по категориям.xlsx');
    }
}
