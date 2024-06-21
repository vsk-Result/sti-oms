<?php

namespace App\Services;

use App\Models\KostCode;
use App\Models\Object\BObject;
use App\Models\Payment;

class GeneralReportService
{
    public function getItems(array $years): array
    {
        $items = [];

        $object27_1 = BObject::where('code', '27.1')->first();
        $paymentsOffice = Payment::whereIn('company_id', [1, 5])->where('object_id', $object27_1->id)->get();
        $paymentsGeneral = Payment::whereIn('company_id', [1, 5])->where('code', '!=', '7.15')->where('type_id', Payment::TYPE_GENERAL)->get();

        $payments = $paymentsGeneral->merge($paymentsOffice);

        foreach ($payments->sortBy('category')->groupBy('category') as $category => $groupedPaymentsByCategory) {
            $item = [
                'type' => 'category',
                'name' => empty($category) ? 'Не указана' : $category,
                'amount' => (clone $groupedPaymentsByCategory)->whereBetween('date', [$years[count($years) - 1] . '-01-01', $years[0] . '-12-31'])->sum('amount')
            ];

            if ($item['amount'] > -1 && $item['amount'] < 1) {
                continue;
            }

            foreach ($years as $year) {
                $item['years'][$year] = (clone $groupedPaymentsByCategory)->whereBetween('date', [$year . '-01-01', $year . '-12-31'])->sum('amount');
            }

            $items[] = $item;

            foreach ($groupedPaymentsByCategory->sortBy('code')->groupBy('code') as $code => $groupedPaymentsByCode) {
                $item = [
                    'type' => 'code',
                    'name' => empty($code) ? 'Не указана' : KostCode::getTitleByCode($code),
                    'amount' => (clone $groupedPaymentsByCode)->whereBetween('date', [$years[count($years) - 1] . '-01-01', $years[0] . '-12-31'])->sum('amount')
                ];

                if ($item['amount'] > -1 && $item['amount'] < 1) {
                    continue;
                }

                foreach ($years as $year) {
                    $item['years'][$year] = (clone $groupedPaymentsByCode)->whereBetween('date', [$year . '-01-01', $year . '-12-31'])->sum('amount');
                }

                $items[] = $item;
            }
        }

        return $items;
    }
}
