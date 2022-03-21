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

class SplitNDFLController extends Controller
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
        $paymentsForSplit = Payment::where('type_id', Payment::TYPE_GENERAL)
            ->whereBetween('date', ['2021-11-01', '2021-12-15'])
            ->where('description', 'NDFL')
            ->orderBy('amount')
            ->get();

        $employeesCount = [];
        $crm = Workhour::select(['id', 'date', 'o_id', 'e_id'])->where('date', 'LIKE', '2021-11' . '%')->get()->groupBy('o_id');
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
            $amount = ($count / $totalEmployees) * $totalAmount;
            $requestData = $payment->attributesToArray();

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

            $requestData['type_id'] = Payment::TYPE_OBJECT;
            $requestData['object_id'] = $object->id;
            $requestData['object_worktype_id'] = 1;
            $requestData['amount'] = -$amount;
            $requestData['description'] = 'НДФЛ за ноябрь 2021';
            $this->paymentService->createPayment($requestData);
        }

        foreach ($paymentsForSplit as $payment) {
            $this->paymentService->destroyPayment($payment);
        }

        // -----------------------------------------------------------

        $paymentsForSplit = Payment::where('type_id', Payment::TYPE_GENERAL)
            ->whereBetween('date', ['2021-12-16', '2021-12-31'])
            ->where('description', 'NDFL')
            ->orderBy('amount')
            ->get();

        $employeesCount = [];
        $crm = Workhour::select(['id', 'date', 'o_id', 'e_id'])->where('date', 'LIKE', '2021-12' . '%')->get()->groupBy('o_id');
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
            $amount = ($count / $totalEmployees) * $totalAmount;
            $requestData = $payment->attributesToArray();

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

            $requestData['type_id'] = Payment::TYPE_OBJECT;
            $requestData['object_id'] = $object->id;
            $requestData['object_worktype_id'] = 1;
            $requestData['amount'] = -$amount;
            $requestData['description'] = 'НДФЛ за декабрь 2021';
            $this->paymentService->createPayment($requestData);
        }

        foreach ($paymentsForSplit as $payment) {
            $this->paymentService->destroyPayment($payment);
        }

        return redirect()->back();
    }
}
