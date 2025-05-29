<?php

namespace App\Services;

use App\Models\Object\BObject;
use App\Models\PivotObjectDebt;
use Illuminate\Pagination\LengthAwarePaginator;

class DebtService
{
    private PivotObjectDebtService $pivotObjectDebtService;

    public function __construct(PivotObjectDebtService $pivotObjectDebtService)
    {
        $this->pivotObjectDebtService = $pivotObjectDebtService;
    }

    public function getPivot(int $id = null): array
    {
        $pivot = [
            'entries' => [],
            'total' => [],
        ];

        $objects = $id !== null ? BObject::where('id', $id)->get() : BObject::orderByDesc('code')->get();

        $objectIds = [];
        $organizations = [];
        foreach ($objects as $object) {
            if ($object->code === '288') continue;

            $contractorDebts = $this->pivotObjectDebtService->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_CONTRACTOR);
            $providerDebts = $this->pivotObjectDebtService->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_PROVIDER);
            $serviceDebts = $this->pivotObjectDebtService->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_SERVICE);

            $contractorDebtsAmount = $contractorDebts['total']['total_amount'];
            $providerDebtsAmount = $providerDebts['total']['amount'];
            $serviceDebtsAmount = $serviceDebts['total']['amount'];

            if (($contractorDebtsAmount + $providerDebtsAmount + $serviceDebtsAmount) == 0) {
                continue;
            }

            $objectIds[] = $object->id;
            $pivot['total'][$object->id] = 0;

            foreach ($contractorDebts['organizations'] as $organizationInfo) {
                if (!isset($pivot['entries'][$organizationInfo['organization_name']][$object->id])) {
                    $pivot['entries'][$organizationInfo['organization_name']][$object->id] = [
                        'amount' => 0,
                        'avans' => 0,
                        'amount_without_guarantee' => 0,
                        'guarantee' => 0,
                    ];
                }

                if (!array_key_exists($organizationInfo['organization_name'], $organizations)) {
                    $organizations[$organizationInfo['organization_name']] = 0;
                }

                $pivot['entries'][$organizationInfo['organization_name']][$object->id]['amount'] += $organizationInfo['total_amount'];
                $pivot['entries'][$organizationInfo['organization_name']][$object->id]['avans'] += $organizationInfo['avans'];
                $pivot['entries'][$organizationInfo['organization_name']][$object->id]['amount_without_guarantee'] += $organizationInfo['amount'];
                $pivot['entries'][$organizationInfo['organization_name']][$object->id]['guarantee'] += $organizationInfo['guarantee'];
                $pivot['total'][$object->id] += $organizationInfo['total_amount'];
                $organizations[$organizationInfo['organization_name']] += $organizationInfo['total_amount'];
            }

            foreach ($providerDebts['organizations'] as $organizationInfo) {
                if (!isset($pivot['entries'][$organizationInfo['organization_name']][$object->id])) {
                    $pivot['entries'][$organizationInfo['organization_name']][$object->id] = [
                        'amount' => 0,
                        'avans' => 0,
                        'amount_without_guarantee' => 0,
                        'guarantee' => 0,
                    ];
                }

                if (!array_key_exists($organizationInfo['organization_name'], $organizations)) {
                    $organizations[$organizationInfo['organization_name']] = 0;
                }

                $pivot['entries'][$organizationInfo['organization_name']][$object->id]['amount'] += $organizationInfo['amount'];
                $pivot['entries'][$organizationInfo['organization_name']][$object->id]['avans'] += $organizationInfo['avans'];
                $pivot['entries'][$organizationInfo['organization_name']][$object->id]['amount_without_guarantee'] += $organizationInfo['amount'];
                $pivot['total'][$object->id] += $organizationInfo['amount'];
                $organizations[$organizationInfo['organization_name']] += $organizationInfo['amount'];
            }

            foreach ($serviceDebts['organizations'] as $organizationInfo) {
                if (!isset($pivot['entries'][$organizationInfo['organization_name']][$object->id])) {
                    $pivot['entries'][$organizationInfo['organization_name']][$object->id] = [
                        'amount' => 0,
                        'avans' => 0,
                        'amount_without_guarantee' => 0,
                        'guarantee' => 0,
                    ];
                }

                if (!array_key_exists($organizationInfo['organization_name'], $organizations)) {
                    $organizations[$organizationInfo['organization_name']] = 0;
                }

                $pivot['entries'][$organizationInfo['organization_name']][$object->id]['amount'] += $organizationInfo['amount'];
                $pivot['entries'][$organizationInfo['organization_name']][$object->id]['avans'] += $organizationInfo['avans'];
                $pivot['entries'][$organizationInfo['organization_name']][$object->id]['amount_without_guarantee'] += $organizationInfo['amount'];
                $pivot['total'][$object->id] += $organizationInfo['amount'];
                $organizations[$organizationInfo['organization_name']] += $organizationInfo['amount'];
            }
        }

        $objectIds = array_unique($objectIds);

        asort($organizations);

        foreach ($organizations as $organizationName => $amount) {
            if (is_valid_amount_in_range($amount)) {
                $pivot['organizations'][$organizationName] = $amount;
            }
        }

        $pivot['objects'] = BObject::whereIn('id', $objectIds)->orderByDesc('code')->get();

        return $pivot;
    }

    public function filterDebts(array $requestData, array &$total): LengthAwarePaginator
    {
        $query = Debt::query();

        if (! empty($requestData['import_id'])) {
            $query->whereIn('import_id', $requestData['import_id']);
        } else {
            $debtImport = DebtImport::where('type_id', DebtImport::TYPE_SUPPLY)->latest('date')->first();
            $debt1CImport = DebtImport::where('type_id', DebtImport::TYPE_1C)->latest('date')->first();
            $debtObjectImport = DebtImport::where('type_id', DebtImport::TYPE_OBJECT)->latest('date')->first();
            $debt1CServiceImport = DebtImport::where('type_id', DebtImport::TYPE_SERVICE_1C)->latest('date')->first();

            $query->whereIn('import_id', [$debtImport?->id, $debt1CImport?->id, $debtObjectImport?->id, $debt1CServiceImport?->id]);
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
            'guarantee_deadline' => $requestData['guarantee_deadline'] ?? 0,
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
            'balance_contract' => $requestData['balance_contract'] ?? 0,
            'unwork_avans' => $requestData['unwork_avans'] ?? 0,
            'fix_float_type' => $requestData['fix_float_type'] ?? null,
        ]);

        return $debt;
    }

    public function destroyDebt(Debt $debt): void
    {
        $debt->delete();
    }
}
