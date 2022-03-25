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
        if (auth()->id() !== 1) {
            return redirect()->back();
        }

        $ids = [
            86493,
            86988,
            86939,
            86072,
            85571,
            86023,
            85424,
            86783,
            98838,
            85431,
            664,
            86428,
            86427,
            663,
            661,
            662,
            85289,
            85812,
            86970,
            85395,
            86175,
            85530,
            85819,
            86782,
            85811,
            86219,
            85362,
            87193,
            85617,
            85461,
            86591,
            86953,
            1396,
            1394,
            87218,
            86174,
            85905,
            86590,
            86777,
            85832,
            85288,
            86589,
            85804,
            86163,
            1024,
            85818,
            85265,
        ];

//        dd(Payment::whereIn('id', $ids)->where('object_id', 40)->orderBy('amount')->sum('amount'));

        $paymentsForSplit = Payment::whereIn('id', $ids)->orderBy('amount')->get();
        $totalAmount = abs($paymentsForSplit->sum('amount'));

        $codes = [
            '349' => 1255075.774,
            '288' => 945179.5535,
            '338' => 584575.0735,
            '346' => 544213.7167,
            '353' => 457252.3035,
            '344' => 386969.6069,
            '342' => 257225.6529,
            '358' => 43659.17667,
            '321' => 6493.392857,
            '335' => 4368,
            '257' => 1328,
            '350' => 1236.25
        ];

//        dd($paymentsForSplit, number_format($totalAmount), number_format(array_sum($codes)), number_format($totalAmount - array_sum($codes)));

        $ignorePayments = [];

        $resultPaymentsSum = 0;

        foreach ($paymentsForSplit as $payment) {

            if (in_array($payment->id, $ignorePayments)) {
                continue;
            }

            if (count($codes) === 0) {
                $this->paymentService->updatePayment($payment, ['code' => '5.10']);
                continue;
            }
            foreach ($codes as $code => $amount) {

                if (in_array($payment->id, $ignorePayments)) {
                    break;
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

                if (abs($payment->amount) > $amount) {
                    $ostatok = $payment->amount + $amount;

                    $requestData = $payment->attributesToArray();
                    $requestData['code'] = '5.10';
                    $requestData['object_id'] = $object->id . '::7';
                    $requestData['amount'] = -$amount;

                    $this->paymentService->prepareRequestData($requestData, $payment);
                    $this->paymentService->createPayment($requestData);
                    $resultPaymentsSum += $amount;

                    unset($codes[$code]);

                    $this->paymentService->updatePayment($payment, ['code' => '5.10', 'amount' => $ostatok]);
                } else {
                    $this->paymentService->updatePayment(
                        $payment,
                        [
                            'code' => '5.10',
                            'object_id' => $object->id . '::7',
                        ]
                    );
                    $resultPaymentsSum += abs($payment->amount);

                    $codes[$code] = $amount - abs($payment->amount);

                    $ignorePayments[] = $payment->id;

                    if ($codes[$code] === 0) {
                        unset($codes[$code]);
                    }
                }
            }
        }

        dd('all');
        // ----------------------

        $paymentsForSplit = Payment::where('type_id', Payment::TYPE_GENERAL)
            ->where('company_id', 1)
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
            ->where('company_id', 1)
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
