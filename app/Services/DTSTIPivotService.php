<?php

namespace App\Services;

use App\Models\Debt\DebtImport;

class DTSTIPivotService
{
    public function getPivot(): array
    {
        $pivot = [
            'total' => 0,
            'entries' => [],
        ];

        $debtDTImport = DebtImport::where('type_id', DebtImport::TYPE_DTTERMO)->latest('date')->first();

        foreach ($debtDTImport->debts()->orderBy('amount', 'ASC')->get() as $debt) {
            $pivot['entries'][] = [
                'object' => [
                    'id' => $debt->object->id,
                    'name' => $debt->object->getName()
                ],
                'amount' => $debt->amount,
                'sti' => $debt->contract,
                'dt' => str_replace('Договор с ДТ: ', '', $debt->comment),
            ];
            $pivot['total'] += $debt->amount;
        }

        return $pivot;
    }
}