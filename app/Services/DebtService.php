<?php

namespace App\Services;

use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Debt\DebtManual;
use App\Models\Object\BObject;
use App\Models\Organization;
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
            'total' => []
        ];

        $objects = $id !== null ? BObject::where('id', $id)->get() : BObject::orderByDesc('code')->get();

        $objectIds = [];
        $organizationIds = [];
        foreach ($objects as $object) {
            if ($object->code === '288') continue;
            $debts = $this->pivotObjectDebtService->getPivotDebtForObject($object->id);

            $contractorDebts = $debts['contractor'];
            $providerDebts = $debts['provider'];
            $serviceDebts = $debts['service'];

            $contractorDebtsAmount = $contractorDebts->total_amount;

            $debtObjectImport = DebtImport::where('type_id', DebtImport::TYPE_OBJECT)->latest('date')->first();
            $objectExistInObjectImport = $debtObjectImport->debts()->where('object_id', $object->id)->count() > 0;
            $contractorDebtsImport = $debtObjectImport->debts()->where('type_id', Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->get();

            if ($objectExistInObjectImport) {
                $contractorDebtsAvans = $contractorDebtsImport->sum('avans');
                $contractorDebtsGU = $contractorDebtsImport->sum('guarantee');
                $contractorDebtsAmount = $contractorDebtsAmount + $contractorDebtsAvans + $contractorDebtsGU;
            }

            if (($contractorDebtsAmount + $providerDebts->total_amount + $serviceDebts->total_amount) === 0) {
                continue;
            }

            if ($contractorDebtsAmount !== 0) {
                $objectIds[] = $object->id;

                if (!isset($pivot['total'][$object->id])) {
                    $pivot['total'][$object->id] = 0;
                }

                foreach ($contractorDebts->debts as $organization => $amount) {
                    $organizationId = substr($organization, 0, strpos($organization, '::'));
                    $organizationIds[] = $organizationId;

                    if (!isset($pivot['entries'][$organizationId][$object->id])) {
                        $pivot['entries'][$organizationId][$object->id] = 0;
                    }

                    $newAmount = $amount;

                    if ($objectExistInObjectImport) {
                        $newAmount += $contractorDebtsImport->where('organization_id', $organizationId)->sum('avans');
                        $newAmount += $contractorDebtsImport->where('organization_id', $organizationId)->sum('guarantee');
                    }

                    $pivot['entries'][$organizationId][$object->id] += $newAmount;

                    $pivot['total'][$object->id] += $newAmount;
                }
            }

            if ($providerDebts->total_amount !== 0) {
                $objectIds[] = $object->id;

                if (!isset($pivot['total'][$object->id])) {
                    $pivot['total'][$object->id] = 0;
                }

                foreach ($providerDebts->debts as $organization => $amount) {
                    $organizationId = substr($organization, 0, strpos($organization, '::'));
                    $organizationIds[] = $organizationId;

                    if (!isset($pivot['entries'][$organizationId][$object->id])) {
                        $pivot['entries'][$organizationId][$object->id] = 0;
                    }

                    $pivot['entries'][$organizationId][$object->id] += $amount;

                    $pivot['total'][$object->id] += $amount;
                }
            }

            if ($serviceDebts->total_amount !== 0) {
                $objectIds[] = $object->id;

                if (!isset($pivot['total'][$object->id])) {
                    $pivot['total'][$object->id] = 0;
                }

                foreach ($serviceDebts->debts as $organization => $amount) {
                    $organizationId = substr($organization, 0, strpos($organization, '::'));
                    $organizationIds[] = $organizationId;

                    if (!isset($pivot['entries'][$organizationId][$object->id])) {
                        $pivot['entries'][$organizationId][$object->id] = 0;
                    }

                    $pivot['entries'][$organizationId][$object->id] += $amount;

                    $pivot['total'][$object->id] += $amount;
                }
            }
        }

        $objectIds = array_unique($objectIds);
        $organizationIds = array_unique($organizationIds);

        $pivot['objects'] = BObject::whereIn('id', $objectIds)->orderByDesc('code')->get();
        $pivot['organizations'] = Organization::whereIn('id', $organizationIds)->orderBy('name')->get();

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
