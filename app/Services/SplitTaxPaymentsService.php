<?php

namespace App\Services;

use App\Models\CurrencyExchangeRate;
use App\Models\Object\BObject;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class SplitTaxPaymentsService
{
    public function __construct(private PaymentService $paymentService) {}

    public function splitPayments(array $paymentIds, string|null $splitDate, array $splitInfo, string $fileName):? string
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

        $resultSplitInfo = [];
        $codesWithoutWorktype = BObject::getCodesWithoutWorktype();

        foreach ($splitInfo as $code => $amount) {
            if (isset($codesWithoutWorktype[$code])) {
                $objectCode = $codesWithoutWorktype[$code];
            } else {
                $objectCode = explode('.', $code)[0];
            }

            if (! isset($resultSplitInfo[$objectCode])) {
                $resultSplitInfo[$objectCode] = 0;
            }

            $resultSplitInfo[$objectCode] += $amount;
        }

        foreach ($resultSplitInfo as $code => $amount) {
            if (! BObject::where('code', $code)->exists()) {
                return 'Объект ' . $code . ' не найден в OMS. Разбивка не произошла.';
            }
        }

        $objectIds = BObject::whereIn('code', array_keys($resultSplitInfo))->pluck('id', 'code')->toArray();
        $logs = [];

        $logs[] = '[' . now()->format('d.m.Y H:i:s') . '] Разбивка взносов из файла ' . $fileName;

        foreach ($payments as $payment) {
            $logs[] = 'Рассматриваем оплату с ID "' . $payment->id . '", описанием "' . $payment->description . '", датой "' . $payment->date . '", суммой "' . $payment->amount . '"';
            foreach ($resultSplitInfo as $code => $amount) {
                if (! is_valid_amount_in_range($amount)) {
                    continue;
                }

                $logs[] = 'Рассматриваем объект с кодом "' . $code . '", суммой "' . -$amount . '"';

                $diff = $payment->amount + $amount;

                if ($diff >= 0) {
                    $logs[] = 'Разница >= 0, поэтому создаем новую оплату на основе текущей для объекта с кодом "' . $code . '"';
                    $newPayment = $this->paymentService->createPayment(['base_payment_id' => $payment->id]);

                    $newPayment->update([
                        'object_id' => $objectIds[$code],
                        'object_worktype_id' => null,
                        'type_id' => Payment::TYPE_OBJECT,
                        'was_split' => true
                    ]);

                    $logs[] = 'Создали оплату с ID "' . $newPayment->id . '", суммой "' . $newPayment->amount . '", объектом "' . $code . '"';

                    $logs[] = 'Удаляем оплату с ID "' . $payment->id . '"';
                    $this->paymentService->destroyPayment($payment);

                    if ($diff > 0) {
                        $resultSplitInfo[$code] = $diff;
                    } else {
                        unset($resultSplitInfo[$code]);
                    }

                    continue;
                }

                $logs[] = 'Разница < 0, поэтому создаем новую оплату на основе текущей для объекта с кодом "' . $code . '"';

                $newPayment = $this->paymentService->createPayment(['base_payment_id' => $payment->id]);
                $newPayment->update([
                    'object_id' => $objectIds[$code],
                    'object_worktype_id' => null,
                    'type_id' => Payment::TYPE_OBJECT,
                    'amount' => -$amount,
                    'amount_without_nds' => -$amount,
                    'was_split' => true
                ]);

                $logs[] = 'Создали оплату с ID "' . $newPayment->id . '", суммой "' . $newPayment->amount . '", объектом "' . $code . '"';

                $payment->update([
                    'amount' => $diff,
                    'amount_without_nds' => $diff,
                ]);

                $logs[] = 'Изменили текущую оплату с ID "' . $payment->id . '", на сумму "' . $diff . '"';

                unset($resultSplitInfo[$code]);
            }
        }

        $logs[] = '[' . now()->format('d.m.Y H:i:s') . '] Разбивка взносов завершена';

        foreach ($logs as $log) {
            Log::channel('payments_split_log')->info($log);
        }

        return null;
    }

    public function prepareSplitInfo(array $splitInfo): array
    {
        $result = [];

        foreach ($splitInfo as $index => $info) {
            if ($index < 13 || $info[0] === 'Итого') {
                continue;
            }

            $objectCode = empty($info[1]) ? '27.1' : $info[1];
            $amount = $info[9] + $info[10] + $info[11] + $info[12] + $info[13] + $info[14];

            if (! isset($result[$objectCode])) {
                $result[$objectCode] = 0;
            }

            $result[$objectCode] += $amount;
        }

        ksort($result);

        return $result;
    }
}