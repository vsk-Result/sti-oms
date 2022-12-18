<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\Debt\DebtManual;

class DebtManualService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function storeDebtManual(array $requestData): void
    {
        $objectId = $requestData['debt_manual_object_id'];
        $organizationId = $requestData['debt_manual_organization_id'];
        $organizationTypeId = $requestData['debt_manual_organization_type_id'];
        $amount = $requestData['debt_manual_amount'];

        if (empty($organizationId) || empty($amount)) {
            return;
        }

        DebtManual::create([
            'type_id' => $organizationTypeId,
            'object_id' => $objectId,
            'object_worktype_id' => null,
            'organization_id' => $organizationId,
            'amount' => $this->sanitizer->set($amount)->toAmount()->get(),
        ]);
    }

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
                'amount' => $this->sanitizer->set($requestData['debt_manual_amount'])->toAmount()->get(),
            ]);
        }
    }
}