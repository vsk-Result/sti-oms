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

        $paymentIds = json_decode($requestData['payment_ids'], true);
        foreach ($paymentIds as $paymentId) {
            $payment = Payment::find($paymentId);
            if (!$payment) {
                session()->flash('split_residence_excel_status', 'Не найдена оплата с ID: ' . $paymentId);
                return redirect()->back()->withInput();
            }
        }

        $importData = Excel::toArray(new SplitResidenceExcelImport(), $requestData['file']);

        if (!isset($importData['Отчет'])) {
            session()->flash('split_residence_excel_status', 'Отсутствует лист "Отчет"');
            return redirect()->back()->withInput();
        }

        $totalAmount = 0;
        $groupedObjectAmount = [];
        $month = $requestData['month'];
        foreach ($importData['Отчет'] as $rowIndex => $row) {
            if ($rowIndex === 4) {
                if (mb_strpos($row[0], $month) === false) {
                    session()->flash('split_residence_excel_status', 'Дата в заголовке таблицы не совпадает с выбранной');
                    return redirect()->back()->withInput();
                }
            }

            if ($rowIndex < 7) {
                continue;
            }

            if (empty($row[0])) {
                break;
            }

            $objectInfo = $row[3];
            $objectCode = mb_substr($objectInfo, 0, strpos($objectInfo, ' -'));

            if (empty($objectCode)) {
                session()->flash('split_residence_excel_status', 'На строке ' . ($rowIndex + 1) . ' не указан объект');
                return redirect()->back()->withInput();
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

        $payments = Payment::whereIn('id', $paymentIds)->get();
        $paymentsTotalAmount = $payments->sum('amount');

        if ((float) abs($paymentsTotalAmount) !== (float) $totalAmount) {
            session()->flash('split_residence_excel_status', 'Сумма оплат (' . $paymentsTotalAmount . ') не совпадает с суммой в таблице (' . $totalAmount . ')');
            return redirect()->back()->withInput();
        }

        $description = $requestData['description'];

        if (empty($description)) {
            $description = $payments->first()->description;
        }

        $requestData = $payments->first()->attributesToArray();
        $requestData['description'] = $description;

        foreach($groupedObjectAmount as $code => $info) {
            $object = BObject::where('code', $code)->first();

            foreach ($info as $worktype => $amount) {
                $requestData['object_id'] = $object->id;
                $requestData['object_worktype_id'] = $worktype;
                $requestData['amount'] = -$amount;

                $nds = $this->paymentService->checkNeedNDS($requestData['description'], null) ? round($amount / 6, 2) : 0;
                $amountWithoutNds = $amount - $nds;

                $requestData['amount_without_nds'] = -$amountWithoutNds;
                $requestData['was_split'] = true;

                $this->paymentService->createPayment($requestData);
            }
        }

        foreach ($payments as $payment) {
            $this->paymentService->destroyPayment($payment);
        }

        return redirect()->back();
    }
}
