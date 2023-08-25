<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Imports\Payment\SplitResidenceExcelImport;
use App\Models\CRM\CObject;
use App\Models\CRM\Workhour;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Services\ObjectService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SplitResidenceExcelController extends Controller
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
        $requestData = $request->toArray();

        $payments = Payment::where('organization_receiver_id', $requestData['organization_id'])->where('was_split', false)->get();

        if ($payments->count() === 0) {
            session()->flash('split_residence_excel_status', 'Не найдены оплаты у выбранного контрагента');
            return redirect()->back()->withInput();
        }

        $importData = Excel::toArray(new SplitResidenceExcelImport(), $requestData['file']);

        if (!isset($importData['Отчет'])) {
            session()->flash('split_residence_excel_status', 'Отсутствует лист "Отчет"');
            return redirect()->back()->withInput();
        }

        $totalAmount = 0;
        $groupedObjectAmount = [];
        foreach ($importData['Отчет'] as $rowIndex => $row) {
            if ($rowIndex < 7) {
                continue;
            }

            if (empty($row[0])) {
                break;
            }

            $objectInfo = $row[3];
            $objectCode = mb_substr($objectInfo, 0, strpos($objectInfo, ' -'));

            if (empty($objectCode)) {
                $objectCode = '27.1';
            }

            $worktype = null;
            $objectMainCode = $objectCode;
            if ($objectCode !== '27.1' && mb_strpos($objectCode, '.') !== false) {
                $objectMainCode = mb_substr($objectCode, 0, strpos($objectCode, '.'));
                $worktype = mb_substr($objectCode, strpos($objectCode, '.') + 1);
            }

            $object = BObject::where('code', $objectMainCode)->first();
            if (!$object) {
                session()->flash('split_residence_excel_status', 'На строке ' . ($rowIndex + 1) . ' не найден объект с кодом ' . $objectCode);
                return redirect()->back()->withInput();
            }

            if (!isset($groupedObjectAmount[$objectMainCode][$worktype])) {
                $groupedObjectAmount[$objectMainCode][$worktype] = 0;
            }

            $groupedObjectAmount[$objectMainCode][$worktype] += $row[40];
            $totalAmount += $row[40];
        }

        $paymentsTotalAmount = $payments->sum('amount');

        if ((float) abs($paymentsTotalAmount) < (float) $totalAmount) {
            session()->flash('split_residence_excel_status', 'Сумма оплат по контрагенту (' . $paymentsTotalAmount . ') меньше суммы в таблице (' . $totalAmount . ')');
            return redirect()->back()->withInput();
        }

        foreach($groupedObjectAmount as $code => $info) {
            $object = BObject::where('code', $code)->first();

            foreach ($info as $worktype => $amount) {
                $this->splitPayment($object, $worktype, -$amount, $requestData['organization_id']);
            }
        }

        return redirect()->back();
    }

    private function splitPayment(BObject $object, int|string|null $worktype, float $amount, int $organizationId): void
    {
        $payment = Payment::where('organization_receiver_id', $organizationId)->where('was_split', false)->orderBy('date')->first();
        $requestData = $payment->attributesToArray();
        $requestData['object_id'] = $object->id;
        $requestData['object_worktype_id'] = empty($worktype) ? null : $worktype;
        $requestData['was_split'] = true;

        if (abs((float) $payment->amount) >= abs($amount)) {
            $requestData['amount'] = $amount;

            $nds = $this->paymentService->checkNeedNDS($requestData['description'], null) ? round($amount / 6, 2) : 0;
            $amountWithoutNds = $amount - $nds;

            $requestData['amount_without_nds'] = $amountWithoutNds;

            $this->paymentService->createPayment($requestData);

            if (abs((float) $payment->amount) === abs($amount)) {
                $this->paymentService->destroyPayment($payment);
            } else {
                $this->paymentService->updatePayment($payment, [
                    'amount' => (float) $payment->amount - $requestData['amount']
                ]);
            }
        } else {
            $amountForNewSplit = $amount - (float) $payment->amount;

            $this->paymentService->createPayment($requestData);
            $this->paymentService->destroyPayment($payment);

            $this->splitPayment($object, $worktype, $amountForNewSplit, $organizationId);
        }
    }
}
