<?php

namespace App\Services;

use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use Illuminate\Pagination\LengthAwarePaginator;

class DebtService
{
    public function filterDebts(array $requestData, array &$total): LengthAwarePaginator
    {
        $query = Debt::query();

        if (! empty($requestData['import_id'])) {
            $query->whereIn('import_id', $requestData['import_id']);
        } else {
            $debtImport = DebtImport::where('type_id', DebtImport::TYPE_SUPPLY)->latest('date')->first();
            $debtDTImport = DebtImport::where('type_id', DebtImport::TYPE_DTTERMO)->latest('date')->first();
            $query->whereIn('import_id', [$debtImport?->id, $debtDTImport?->id]);
        }

        if (! empty($requestData['type_id'])) {
            $query->whereIn('type_id', $requestData['type_id']);
        }

        if (! empty($requestData['object_id'])) {
            $query->whereIn('object_id', $requestData['object_id']);
        }

        if (! empty($requestData['organization_id'])) {
            $query->whereIn('organization_id', $requestData['organization_id']);
        }

        if (! empty($requestData['category'])) {
            $query->whereIn('category', $requestData['category']);
        }

        if (! empty($requestData['object_worktype_id'])) {
            $query->whereIn('object_worktype_id', $requestData['object_worktype_id']);
        }

        if (! empty($requestData['description'])) {
            $query->where('description', 'LIKE', '%' . $requestData['description'] . '%');
        }

        if (! empty($requestData['invoice_number'])) {
            $query->where('invoice_number', 'LIKE', '%' . $requestData['invoice_number'] . '%');
        }

        $query->with('organization', 'object');
        $query->orderByDesc('amount');

        $perPage = 30;
        if (! empty($requestData['count_per_page'])) {
            $perPage = (int) preg_replace("/[^0-9]/", '', $requestData['count_per_page']);
        }

        $total['amount_contractor']['RUB'] = (clone $query)->where('type_id', Debt::TYPE_CONTRACTOR)->sum('amount');
        $total['amount_provider']['RUB'] = (clone $query)->where('type_id', Debt::TYPE_PROVIDER)->sum('amount');

        return $query->paginate($perPage)->withQueryString();
    }

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
            'status_id' => $requestData['status_id'],
            'category' => $requestData['category'] ?? null,
            'code' => $requestData['code'] ?? null,
            'invoice_number' => $requestData['invoice_number'] ?? null,
            'order_author' => $requestData['order_author'] ?? null,
            'description' => $requestData['description'] ?? null,
            'comment' => $requestData['comment'] ?? null,
            'invoice_payment_due_date' => $requestData['invoice_payment_due_date'] ?? null,
            'invoice_amount' => $requestData['invoice_amount'] ?? 0,
        ]);

        return $debt;
    }

    public function destroyDebt(Debt $debt): void
    {
        $debt->delete();
    }
}
