<?php

namespace App\Services;

use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Debt\DebtManual;
use App\Models\Object\BObject;
use App\Models\Organization;
use App\Services\Contract\ContractService;
use Illuminate\Pagination\LengthAwarePaginator;

class DebtService
{
    public function getPivot(int $id = null): array
    {
        $debtImport = DebtImport::where('type_id', DebtImport::TYPE_SUPPLY)->latest('date')->first();
        $debtDTImport = DebtImport::where('type_id', DebtImport::TYPE_DTTERMO)->latest('date')->first();
        $debt1CImport = DebtImport::where('type_id', DebtImport::TYPE_1C)->latest('date')->first();
        $debtObjectImport = DebtImport::where('type_id', DebtImport::TYPE_OBJECT)->latest('date')->first();
        $debt1CServiceImport = DebtImport::where('type_id', DebtImport::TYPE_SERVICE_1C)->latest('date')->first();

        $debtsObjectImport = $debtObjectImport->debts()->with('organization', 'object')->get();

        $debtsQuery = Debt::whereIn('import_id', [$debtImport?->id, $debtDTImport?->id, $debt1CImport?->id, $debtObjectImport?->id, $debt1CServiceImport?->id])->with('organization', 'object');

        $objectsQuery = BObject::query();

        if ($id) {
            $objectsQuery->where('id', $id);
            $debtsQuery->where('object_id', $id);
        } else {
            $objectsQuery->whereIn('id', (clone $debtsQuery)->groupBy('object_id')->pluck('object_id'));
        }

        $objects = $objectsQuery->orderByDesc('code')->get();

        $pivot = [
            'objects' => $objects,
            'organizations' => Organization::whereIn('id', (clone $debtsQuery)->groupBy('organization_id')->pluck('organization_id'))->orderBy('name')->get(),
            'entries' => [],
            'manuals' => [],
            'total' => []
        ];

        foreach ($pivot['objects'] as $object) {
            $pivot['total'][$object->id] = 0;
        }

        foreach ((clone $debtsQuery)->get()->groupBy('type_id') as $typeId => $debtsGroupedByType) {
            foreach ($debtsGroupedByType->groupBy('organization_id') as $organizationId => $debtsGrouped) {
                foreach ($debtsGrouped->groupBy('object_id') as $objectId => $debts) {
                    $objectExistInObjectImport = $debtsObjectImport->where('object_id', $objectId)->first();
                    $debtManuals = DebtManual::where('organization_id', $organizationId)->where('object_id', $objectId)->get();

                    if ($debtManuals->count() > 0) {
                        $debtsAmount = $debtManuals->sum('amount');
                    } else {
                        if ($objectExistInObjectImport && $typeId === Debt::TYPE_CONTRACTOR) {
                            $dQuery = $debtsObjectImport->where('organization_id', $organizationId)
                                ->where('object_id', $objectId);
                            $debtsAmount = $dQuery->sum('amount') + $dQuery->sum('avans');
                        } else {
                            $debtsAmount = $debts->sum('amount');
                        }
                    }

                    $pivot['entries'][$organizationId][$objectId] = $debtsAmount;
                    $pivot['manuals'][$organizationId][$objectId] = $debtManuals->count() > 0;
                    $pivot['total'][$objectId] += $pivot['entries'][$organizationId][$objectId];
                }
            }
        }
        return $pivot;
    }

    public function filterDebts(array $requestData, array &$total): LengthAwarePaginator
    {
        $query = Debt::query();

        if (! empty($requestData['import_id'])) {
            $query->whereIn('import_id', $requestData['import_id']);
        } else {
            $debtImport = DebtImport::where('type_id', DebtImport::TYPE_SUPPLY)->latest('date')->first();
            $debtDTImport = DebtImport::where('type_id', DebtImport::TYPE_DTTERMO)->latest('date')->first();
            $debt1CImport = DebtImport::where('type_id', DebtImport::TYPE_1C)->latest('date')->first();
            $debtObjectImport = DebtImport::where('type_id', DebtImport::TYPE_OBJECT)->latest('date')->first();
            $debt1CServiceImport = DebtImport::where('type_id', DebtImport::TYPE_SERVICE_1C)->latest('date')->first();

            $query->whereIn('import_id', [$debtImport?->id, $debtDTImport?->id, $debt1CImport?->id, $debtObjectImport?->id, $debt1CServiceImport?->id]);
        }

        if (! empty($requestData['type_id'])) {
            $query->whereIn('type_id', $requestData['type_id']);
        }

        if (! empty($requestData['object_id'])) {
            $query->whereIn('object_id', $requestData['object_id']);
        } else {
            if (auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
                $query->whereIn('object_id', auth()->user()->objects->pluck('id'));
            }
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

        $total['amount_contractor']['RUB'] = (clone $query)->where('type_id', Debt::TYPE_CONTRACTOR)->sum('amount') + (clone $query)->where('type_id', Debt::TYPE_CONTRACTOR)->sum('avans');
        $total['amount_provider']['RUB'] = (clone $query)->where('type_id', Debt::TYPE_PROVIDER)->sum('amount');
        $total['amount_service']['RUB'] = (clone $query)->where('type_id', Debt::TYPE_SERVICE)->sum('amount');

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
            'guarantee' => $requestData['guarantee'] ?? 0,
            'avans' => $requestData['avans'] ?? 0,
            'amount_without_nds' => $requestData['amount_without_nds'],
            'status_id' => $requestData['status_id'],
            'category' => $requestData['category'] ?? null,
            'code' => $requestData['code'] ?? null,
            'invoice_number' => $requestData['invoice_number'] ?? null,
            'order_author' => $requestData['order_author'] ?? null,
            'description' => $requestData['description'] ?? null,
            'contract' => $requestData['contract'] ?? null,
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
