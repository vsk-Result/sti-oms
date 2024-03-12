<?php

namespace App\Services;

use App\Models\FinanceReport;
use App\Models\Object\PlanPayment;

class ObjectPlanPaymentService
{
    public function makeDefaultPaymentsForObject(int $objectId): void
    {
        $createConfig = FinanceReport::getPrognozFields();

        foreach ($createConfig as $field) {
            PlanPayment::create([
                'object_id' => $objectId,
                'field' => $field,
                'amount' => 0,
                'type_id' => PlanPayment::TYPE_AUTO,
            ]);
        }
    }
}
