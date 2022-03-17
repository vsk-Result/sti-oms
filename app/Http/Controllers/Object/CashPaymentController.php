<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Company;
use App\Models\Object\BObject;
use App\Models\Object\WorkType;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\PaymentImport;
use App\Services\PaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CashPaymentController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(BObject $object, Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $payments = Payment::where('payment_type_id', Payment::PAYMENT_TYPE_CASH)
                ->where('object_id', $object->id)
                ->where('date', 'LIKE', $request->get('year') . '-' . $request->get('month') . '%')
                ->orderBy('date', 'DESC')
                ->paginate(30);

            return response()->json([
                'status' => 'success',
                'message' => 'Оплаты успешно получены',
                'payments_view' => view('objects.parts._cash_payments', compact('payments'))->render()
            ]);
        }

        $years = [
            2022 => '2022',
            2021 => '2021',
            2020 => '2020',
            2019 => '2019',
            2018 => '2018',
            2017 => '2017'
        ];
        $months = [
            '01' => 'Январь',
            '02' => 'Февраль',
            '03' => 'Март',
            '04' => 'Апрель',
            '05' => 'Май',
            '06' => 'Июнь',
            '07' => 'Июль',
            '08' => 'Август',
            '09' => 'Сентябрь',
            '10' => 'Октябрь',
            '11' => 'Ноябрь',
            '12' => 'Декабрь'
        ];
//        $years = Payment::where('payment_type_id', Payment::PAYMENT_TYPE_CASH)->where('object_id', $object->id)->get()->

        return view('objects.tabs.cash', compact('object', 'years', 'months'));
    }
}
