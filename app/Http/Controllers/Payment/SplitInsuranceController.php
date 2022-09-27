<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\CRM\CObject;
use App\Models\CRM\Workhour;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Services\ObjectService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SplitInsuranceController extends Controller
{
    private PaymentService $paymentService;
    private ObjectService $objectService;

    public function __construct(PaymentService $paymentService, ObjectService $objectService)
    {
        $this->paymentService = $paymentService;
        $this->objectService = $objectService;
    }

    public function store(Request $request): RedirectResponse
    {
        $month = $request->get('month');
        $paymentsForSplit = Payment::where('type_id', Payment::TYPE_GENERAL)
            ->where('company_id', 1)
            ->where('description', 'LIKE', 'Страховые взносы%')
            ->where('description', 'LIKE', '%за ' . mb_strtolower($month, 'UTF-8') . '%')
            ->orderBy('amount')
            ->get();

        if ($paymentsForSplit->count() === 0) {
            session()->flash('split_insurance_status', 'Данных для разбивки нет.');
            return redirect()->back();
        }

        $year = substr($month, strpos($month, ' ') + 1);
        $date = '';
        if (str_contains($month, 'Январь')) {
            $date = '01';
        } elseif (str_contains($month, 'Февраль')) {
            $date = '02';
        } elseif (str_contains($month, 'Март')) {
            $date = '03';
        } elseif (str_contains($month, 'Апрель')) {
            $date = '04';
        } elseif (str_contains($month, 'Май')) {
            $date = '05';
        } elseif (str_contains($month, 'Июнь')) {
            $date = '06';
        } elseif (str_contains($month, 'Июль')) {
            $date = '07';
        } elseif (str_contains($month, 'Август')) {
            $date = '08';
        } elseif (str_contains($month, 'Сентябрь')) {
            $date = '09';
        } elseif (str_contains($month, 'Октябрь')) {
            $date = '10';
        } elseif (str_contains($month, 'Ноябрь')) {
            $date = '11';
        } elseif (str_contains($month, 'Декабрь')) {
            $date = '12';
        }
        $employeesCount = [];
        $crm = Workhour::select(['id', 'date', 'o_id', 'e_id'])->where('date', 'LIKE', $year . '-' . $date . '%')->get()->groupBy('o_id');
        foreach ($crm as $oId => $entries) {
            $object = CObject::find($oId);
            $code = substr($object->code, 0, strpos($object->code, '.'));

            if ($code == 27) {
                $code = '27.1';
            }
            if (! isset($employeesCount[$code])) {
                $employeesCount[$code] = 0;
            }
            $employeesCount[$code] += $entries->groupBy('e_id')->count();
        }

        arsort($employeesCount);
        $totalEmployees = array_sum($employeesCount);

        $totalAmount = abs($paymentsForSplit->sum('amount'));

        $payment = Payment::find($paymentsForSplit[0]->id);

        foreach ($employeesCount as $code => $count) {
            if ($code == 27) {
                $code = '27.1';
            }

            $object = BObject::where('code', $code)->first();
            if (! $object) {
                session()->flash('split_insurance_status', 'Объект "' . $code . '" не найден в системе. Разбивка не удалась.');
                return redirect()->back();
            }
        }

        foreach ($employeesCount as $code => $count) {
            $amount = ($count / $totalEmployees) * $totalAmount;
            $requestData = $payment->attributesToArray();

            if ($code == 27) {
                $code = '27.1';
            }

            $object = BObject::where('code', $code)->first();

            $requestData['type_id'] = Payment::TYPE_OBJECT;
            $requestData['object_id'] = $object->id;
            $requestData['object_worktype_id'] = 1;
            $requestData['amount'] = -$amount;
            $requestData['amount_without_nds'] = -$amount;
            $this->paymentService->createPayment($requestData);
        }

        foreach ($paymentsForSplit as $payment) {
            $this->paymentService->destroyPayment($payment);
        }

        return redirect()->back();
    }
}
