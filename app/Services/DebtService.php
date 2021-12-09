<?php

namespace App\Services;

use App\Models\Debt\Debt;

class DebtService
{
    public function createDebt(array $requestData): Debt
    {
        $debt = Debt::create([
            'import_id' => $requestData['import_id'],
            'type_id' => $requestData['type_id'],
            'company_id' => $requestData['company_id'],
            'object_id' => $requestData['object_id'],
            'object_worktype_id' => $requestData['object_worktype_id'],
            'organization_id' => $requestData['organization_id'],
            'date' => $requestData['date'],
            'amount' => $requestData['amount'],
            'amount_without_nds' => $requestData['amount_without_nds'],
            'status_id' => $requestData['status_id']
        ]);

        return $debt;
    }

    public function destroyDebt(Debt $debt): void
    {
        $debt->delete();
    }
}
