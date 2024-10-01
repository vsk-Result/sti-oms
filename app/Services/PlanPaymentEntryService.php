<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\CashFlow\PlanPaymentEntry;
use App\Models\Status;

class PlanPaymentEntryService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function createOrUpdatePlanPaymentEntry(array $requestData): PlanPaymentEntry
    {
        $entry = $this->findEntry($requestData['payment_id'], $requestData['date']);

        if ($entry) {
            $entry = $this->updatePlanPaymentEntry($entry, $requestData);
            return $entry;
        }

        return $this->createPlanPaymentEntry($requestData);
    }

    public function createPlanPaymentEntry(array $requestData): PlanPaymentEntry
    {
        return PlanPaymentEntry::create([
            'payment_id' => $requestData['payment_id'],
            'date' => $requestData['date'],
            'amount' => isset($requestData['amount']) ? $this->sanitizer->set($requestData['amount'])->toAmount()->get() : 0,
            'status_id' => Status::STATUS_ACTIVE,
        ]);
    }

    public function updatePlanPaymentEntry(PlanPaymentEntry $entry, array $requestData): PlanPaymentEntry
    {
        $entry->update([
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get()
        ]);

        return $entry;
    }

    public function findEntry(int $paymentId, string $date): PlanPaymentEntry | null
    {
        return PlanPaymentEntry::where('payment_id', $paymentId)->where('date', $date)->first();
    }
}
