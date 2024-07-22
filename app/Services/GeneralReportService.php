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
        $codesWithId = KostCode::getCodesWithId();

        $categoriesWithId = [
            Payment::CATEGORY_SALARY => 0,
            Payment::CATEGORY_TAX => 1,
            Payment::CATEGORY_MATERIAL => 2,
            Payment::CATEGORY_OPSTE => 3,
            Payment::CATEGORY_RAD => 4,
            Payment::CATEGORY_CUSTOMERS => 5,
            Payment::CATEGORY_TRANSFER => 6,
        ];

        foreach ($payments->sort(function($a, $b) use($categoriesWithId) {
            return ($categoriesWithId[$a->category] ?? 0) - ($categoriesWithId[$b->category] ?? 0);
        })->groupBy('category') as $category => $groupedPaymentsByCategory) {
            $categoryItem = [
                'name' => empty($category) ? 'Не указана' : $category,
                'amount' => (clone $groupedPaymentsByCategory)->whereBetween('date', [$years[count($years) - 1] . '-01-01', $years[0] . '-12-31'])->sum('amount'),
                'codes' => [
                    'receive' => [],
                    'pay' => [],
                ]
            ];

            if ($categoryItem['amount'] > -1 && $categoryItem['amount'] < 1) {
                continue;
            }

            foreach ($years as $year) {
                $categoryItem['years'][$year] = (clone $groupedPaymentsByCategory)->whereBetween('date', [$year . '-01-01', $year . '-12-31'])->sum('amount');
            }

            foreach ($groupedPaymentsByCategory->where('amount', '<', 0)->sort(function($a, $b) use($codesWithId) {
                return ($codesWithId[$a->code] ?? 0) - ($codesWithId[$b->code] ?? 0);
            })->groupBy('code') as $code => $groupedPaymentsByCode) {
                $codeItem = [
                    'name' => empty($code) ? 'Не указана' : KostCode::getTitleByCode($code),
                    'amount' => (clone $groupedPaymentsByCode)->whereBetween('date', [$years[count($years) - 1] . '-01-01', $years[0] . '-12-31'])->sum('amount')
                ];

                if ($codeItem['amount'] > -1 && $codeItem['amount'] < 1) {
                    continue;
                }

                foreach ($years as $year) {
                    $codeItem['years'][$year] = (clone $groupedPaymentsByCode)->whereBetween('date', [$year . '-01-01', $year . '-12-31'])->sum('amount');
                }

                $categoryItem['codes']['pay'][] = $codeItem;
            }

            foreach ($groupedPaymentsByCategory->where('amount', '>=', 0)->sort(function($a, $b) use($codesWithId) {
                return ($codesWithId[$a->code] ?? 0) - ($codesWithId[$b->code] ?? 0);
            })->groupBy('code') as $code => $groupedPaymentsByCode) {
                $codeItem = [
                    'name' => empty($code) ? 'Не указана' : KostCode::getTitleByCode($code),
                    'amount' => (clone $groupedPaymentsByCode)->whereBetween('date', [$years[count($years) - 1] . '-01-01', $years[0] . '-12-31'])->sum('amount')
                ];

                if ($codeItem['amount'] > -1 && $codeItem['amount'] < 1) {
                    continue;
                }

                foreach ($years as $year) {
                    $codeItem['years'][$year] = (clone $groupedPaymentsByCode)->whereBetween('date', [$year . '-01-01', $year . '-12-31'])->sum('amount');
                }

                $categoryItem['codes']['receive'][] = $codeItem;
            }

            $items[] = $categoryItem;
        }

        return $items;
    }
}
