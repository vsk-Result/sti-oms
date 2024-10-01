<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\CashFlow\PlanPayment;
use App\Models\Status;

class PlanPaymentService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function createPlanPayment(array $requestData): PlanPayment
    {
        return PlanPayment::create([
            'group_id' => $requestData['group_id'] ?? null,
            'object_id' => $requestData['object_id'] ?? null,
            'name' => $this->sanitizer->set($requestData['name'])->upperCaseFirstWord()->get(),
            'status_id' => Status::STATUS_ACTIVE,
        ]);
    }

    public function updatePlanPayment(array $requestData): PlanPayment
    {
        $payment = $this->findPlanPayment($requestData['payment_id']);
        $objectId = $payment->object_id;

        if (isset($requestData['object_id'])) {
            $objectId = $requestData['object_id'] === 'null' ? null : $requestData['object_id'];
        }

        $payment->update([
            'group_id' => $requestData['group_id'] ?? $payment->group_id,
            'object_id' => $objectId,
            'name' => isset($requestData['name'])
                ? $this->sanitizer->set($requestData['name'])->upperCaseFirstWord()->get()
                : $payment->name,
        ]);

        return $payment;
    }

    public function destroyPlanPayment(array $requestData): void
    {
        $payment = $this->findPlanPayment($requestData['payment_id']);
        $payment->entries()->delete();
        $payment->delete();
    }

    public function findPlanPayment($id): PlanPayment | null
    {
        return PlanPayment::find($id);
    }
}
