<?php

namespace App\Services;

use App\Models\CurrencyExchangeRate;
use App\Models\Object\BObject;
use App\Models\Payment;

class SplitTaxPaymentsService
{
    public function __construct(private PaymentService $paymentService) {}

    public function splitPayments(array $paymentIds, string|null $splitDate, array $splitInfo):? string
    {
        if (count($paymentIds) === 0) {
            return 'Не выбраны оплаты для разбивки';
        }

        if (count($splitInfo) === 0) {
            return 'Нет информации для разбивки из загруженного файла';
        }

        $payments = Payment::whereIn('id', $paymentIds)->get();
        $totalSplitInfoAmount = array_sum($splitInfo);

        $totalsDiff = $totalSplitInfoAmount - abs($payments->sum('amount'));

        if ($totalsDiff > 0) {
            return 'Сумма налогов в файле превышает сумму выбранных оплат на ' . CurrencyExchangeRate::format($totalsDiff, 'RUB');
        }

        return '';

        $objectCodes = [];
        $codesWithoutWorktype = BObject::getCodesWithoutWorktype();

        foreach (array_keys($splitInfo) as $code) {
            if (isset($codesWithoutWorktype[$code])) {
                $objectCodes[$code] = $codesWithoutWorktype[$code] . '::null';
                continue;
            }

            [$mainCode, $workType] = explode('.', $code);

            $objectCodes[$code] = $mainCode . '::' . $workType;
        }

        foreach ($objectCodes as $code) {
            [$objectCode] = explode('::', $code);
            if (! BObject::where('code', $objectCode)->exists()) {
                return 'Объект ' . $code . ' не найден в OMS. Разбивка не произошла.';
            }
        }

//        foreach ($splitInfo as $code => $amount) {
//            $requestData = $payment->attributesToArray();
//
//            if ($code == 27) {
//                $code = '27.1';
//            }
//
//            $object = BObject::where('code', $code)->first();
//
//            $requestData['type_id'] = Payment::TYPE_OBJECT;
//            $requestData['object_id'] = $object->id;
//            $requestData['object_worktype_id'] = 1;
//            $requestData['amount'] = -$amount;
//            $requestData['amount_without_nds'] = -$amount;
//            $requestData['description'] = 'Налог на доходы физических лиц за ' . $month;
//            $requestData['code'] = '7.1';
//            $requestData['was_split'] = true;
//            $this->paymentService->createPayment($requestData);
//        }
//
//        foreach ($paymentsForSplit as $payment) {
//            $this->paymentService->destroyPayment($payment);
//        }
    }

    public function prepareSplitInfo(array $splitInfo): array
    {
        $result = [];

        foreach ($splitInfo as $index => $info) {
            if ($index < 13 || $info[0] === 'Итого') {
                continue;
            }

            $objectCode = empty($info[1]) ? '27.1' : $info[1];
            $amount = $info[9] + $info[11] + $info[13] + $info[14];

            if (! isset($result[$objectCode])) {
                $result[$objectCode] = 0;
            }

            $result[$objectCode] += $amount;
        }

        ksort($result);

        return $result;
    }
}