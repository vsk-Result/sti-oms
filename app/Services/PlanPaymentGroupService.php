<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\CashFlow\PlanPayment;
use App\Models\CashFlow\PlanPaymentGroup;
use App\Models\Status;

class PlanPaymentGroupService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function createPlanPaymentGroup(array $requestData): void
    {
        PlanPaymentGroup::create([
            'name' => $this->sanitizer->set($requestData['name'])->upperCaseFirstWord()->get(),
            'object_id' => $requestData['object_id'],
            'status_id' => Status::STATUS_ACTIVE,
        ]);
    }

    public function updatePlanPaymentGroup(array $requestData): void
    {
        foreach ($requestData['payments'] as $groupId => $payments) {
            $group = $this->findGroup($groupId);

            $group->payments()->update([
                'group_id' => null
            ]);

            foreach ($payments as $paymentId) {
                $payment = PlanPayment::find($paymentId);
                if ($payment) {
                    $payment->update([
                        'group_id' => $group->id
                    ]);
                }
            }

            $group->update([
                'object_id' => $requestData['objects'][$groupId]
            ]);
        }
    }

    public function findGroup(int $groupId): PlanPaymentGroup | null
    {
        return PlanPaymentGroup::find($groupId);
    }
}
