<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\Status;
use App\Models\TaxPlanItem;

class TaxPlanItemService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function createItem(array $requestData): void
    {
        $item = TaxPlanItem::create([
            'name' => $requestData['name'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'due_date' => $requestData['due_date'],
            'period' => $requestData['period'],
            'in_one_c' => $requestData['in_one_c'],
            'payment_date' => $requestData['payment_date'],
            'status_id' => Status::STATUS_ACTIVE,
        ]);
    }

    public function updateItem(TaxPlanItem $item, array $requestData): void
    {
        $item->update([
            'name' => $requestData['name'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'due_date' => $requestData['due_date'],
            'period' => $requestData['period'],
            'in_one_c' => $requestData['in_one_c'],
            'payment_date' => $requestData['payment_date'],
            'status_id' => $requestData['status_id'],
        ]);
    }

    public function destroyItem(TaxPlanItem $item): void
    {
        $item->delete();
    }
}
