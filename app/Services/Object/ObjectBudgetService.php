<?php

namespace App\Services\Object;

use App\Helpers\Sanitizer;
use App\Models\Object\Budget;

class ObjectBudgetService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function getObjectBudgets(int $objectId)
    {
        $this->updateObjectBudgetList($objectId);

        return Budget::where('object_id', $objectId)->get();
    }

    public function updateObjectBudgetList(int $objectId): void
    {
        $budgetList = Budget::getBudgetList();

        foreach ($budgetList as $budget) {
            $budgetExist = Budget::where('type_id', $budget['type_id'])->first();

            if ($budgetExist) {
                continue;
            }

            Budget::create([
                'object_id' => $objectId,
                'type_id' => $budget['type_id'],
                'name' => $budget['name'],
                'amount' => 0
            ]);
        }
    }

    public function updateObjectBudget(int $objectId, $amount): void
    {
        $budget = Budget::find($objectId);

        if (!$budget) {
            return;
        }

        $budget->update([
            'amount' => $this->sanitizer->set($amount)->toAmount()->get()
        ]);
    }
}
