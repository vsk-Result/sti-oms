<?php

namespace App\Services;

use App\Models\Debt\DebtManual;

class DebtManualService
{
    public function updateDebtManual(array $requestData): void
    {
        $debtManualId = $requestData['debt_manual_id'];
        $debtManual = DebtManual::find($debtManualId);

        if ($requestData['debt_manual_amount'] === '' || is_null($requestData['debt_manual_amount'])) {
            $debtManual?->delete();
            return;
        }

        if ($debtManual) {
            $debtManual->update([
                'amount' => $requestData['debt_manual_amount'],
            ]);
        } else {
            DebtManual::create([
                'type_id' => $requestData['debt_manual_type_id'] ?? DebtManual::TYPE_CONTRACTOR,
                'object_id' => $requestData['debt_manual_object_id'],
                'object_worktype_id' => $requestData['debt_manual_object_worktype_id'] ?? null,
                'organization_id' => $requestData['debt_manual_organization_id'],
                'amount' => $requestData['debt_manual_amount'],
            ]);
        }
    }
}