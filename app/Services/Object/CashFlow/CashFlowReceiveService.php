<?php

namespace App\Services\Object\CashFlow;

use App\Services\ReceivePlanService;

class CashFlowReceiveService
{
    public function __construct(private ReceivePlanService $receivePlanService) { }

    public function createReceive(array $requestData): void
    {
        $this->receivePlanService->updatePlan($requestData);
    }
}
