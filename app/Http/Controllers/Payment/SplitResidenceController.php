<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\CRM\CObject;
use App\Models\CRM\Workhour;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Services\ObjectService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SplitResidenceController extends Controller
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
        $object = BObject::where('code', '288')->first();
        if (! $object) {
            return redirect()->back();
        }

        $paymentsForSplit = Payment::where('object_id', $object->id)
            ->where('description', 'LIKE', '%проживание по договору К-003 от 30.10.17 за ' . mb_strtolower($month, 'UTF-8') . '%')
            ->orderBy('amount')
            ->get();

        if ($paymentsForSplit->count() === 0) {
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
        $notCodes = ['349', '346', '358', 349, 346, 358];
        foreach ($crm as $oId => $entries) {
            $object = CObject::find($oId);
            $code = substr($object->code, 0, strpos($object->code, '.'));
            if (in_array($code, $notCodes)) {
                continue;
            }
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

        $codeAmount = [];
        foreach ($employeesCount as $code => $count) {
            $codeAmount[$code] = ($count / $totalEmployees) * $totalAmount;
        }

        $payments = [];
        foreach ($paymentsForSplit as $payment) {
            $payments[$payment->id] = abs($payment->amount);
        }

        $result = [];
        foreach ($codeAmount as $code => $amount) {
            foreach ($payments as $id => $pAmount) {
                if (! isset($result[$id][$code])) {
                    $result[$id][$code] = 0;
                }
                if ($amount > $pAmount) {
                    $amount -= $pAmount;
                    $codeAmount[$code] -= $pAmount;
                    $result[$id][$code] += $pAmount;
                    unset($payments[$id]);
                } else {
                    $payments[$id] -= $amount;
                    $result[$id][$code] += $amount;
                    unset($codeAmount[$code]);

                    if ($payments[$id] === 0) {
                        unset($payments[$id]);
                    }
                    break;
                }
            }
        }

        foreach ($result as $paymentId => $codes) {
            $payment = Payment::find($paymentId);
            $requestData = $payment->attributesToArray();

            foreach($codes as $code => $amount) {
                if ($code == 27) {
                    $code = '27.1';
                }

                $object = BObject::where('code', $code)->first();
                if (! $object) {
                    $object = $this->objectService->createObject([
                        'code' => $code,
                        'name' => 'Без названия',
                        'address' => null,
                        'responsible_name' => null,
                        'responsible_email' => null,
                        'responsible_phone' => null,
                        'photo' => null
                    ]);
                }

                $requestData['object_id'] = $object->id;
                $requestData['amount'] = -$amount;
                $this->paymentService->createPayment($requestData);
            }
        }

        foreach ($paymentsForSplit as $payment) {
            $this->paymentService->destroyPayment($payment);
        }

        return redirect()->back();
    }
}
