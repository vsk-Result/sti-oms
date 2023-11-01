<?php

namespace App\Services;

use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Debt\DebtManual;
use App\Models\Object\BObject;
use App\Models\Organization;
use App\Models\PivotObjectDebt;
use Carbon\Carbon;

class PivotObjectDebtService
{
    private BObject $object;

    public function getPivotDebtForObject(int $objectId): array
    {
        $emptyData = [
            'debts' => [],
            'total_amount' => 0,
        ];

        $date = Carbon::now()->format('Y-m-d');
        $pivot = PivotObjectDebt::where('date', $date)->where('object_id', $objectId)->first();

        if (!$pivot) {
            $date = Carbon::now()->subDay()->format('Y-m-d');
            $pivot = PivotObjectDebt::where('date', $date)->where('object_id', $objectId)->first();
        }

        return [
            'contractor' => $pivot ? json_decode($pivot->contractor) : collect($emptyData),
            'provider' => $pivot ? json_decode($pivot->provider) : collect($emptyData),
            'service' => $pivot ? json_decode($pivot->service) : collect($emptyData),
        ];
    }

    public function updatePivotDebtForObject(int $objectId): void
    {
        $this->object = BObject::find($objectId);

        $contractorDebts = $this->getContractorDebts();
        $contractorInfo = [
            'debts' => $contractorDebts,
            'total_amount' => array_sum($contractorDebts)
        ];

        $providerDebts = $this->getProviderDebts();
        $providerInfo = [
            'debts' => $providerDebts,
            'total_amount' => array_sum($providerDebts)
        ];

        $serviceDebts = $this->getServiceDebts();
        $serviceInfo = [
            'debts' => $serviceDebts,
            'total_amount' => array_sum($serviceDebts)
        ];

        $date = Carbon::now()->format('Y-m-d');
        $pivot = PivotObjectDebt::where('date', $date)->where('object_id', $objectId)->first();
        $debts = [
            'date' => $date,
            'object_id' => $this->object->id,
            'contractor' => json_encode($contractorInfo),
            'provider' => json_encode($providerInfo),
            'service' => json_encode($serviceInfo),
        ];

        if ($pivot) {
            $pivot->update($debts);
        } else {
            PivotObjectDebt::create($debts);
        }
    }

    public function updatePivotDebtForAllObjects(): void
    {
        $objects = BObject::all();

        foreach ($objects as $object) {
            $this->updatePivotDebtForObject($object->id);
        }
    }

    private function getContractorDebts($forApi = false): array
    {
        $debtManuals = DebtManual::where('type_id', Debt::TYPE_CONTRACTOR)->where('object_id', $this->object->id)->with('organization')->get();
        $debtImport = DebtImport::where('type_id', DebtImport::TYPE_SUPPLY)->latest('date')->first();
        $debtDTImport = DebtImport::where('type_id', DebtImport::TYPE_DTTERMO)->latest('date')->first();
        $debt1CImport = DebtImport::where('type_id', DebtImport::TYPE_1C)->latest('date')->first();
        $debtObjectImport = DebtImport::where('type_id', DebtImport::TYPE_OBJECT)->latest('date')->first();

        $existInObjectImport = $this->object
            ->debts()
            ->where('import_id', $debtObjectImport?->id)
            ->first();

        if ($existInObjectImport) {
            $debts = $this->object
                ->debts()
                ->where('import_id', $debtObjectImport?->id)
                ->where('type_id', Debt::TYPE_CONTRACTOR)
                ->orderBy(Organization::select('name')->whereColumn('organizations.id', 'debts.organization_id'))
                ->with('organization')
                ->orderBy('amount')
                ->get();
        } else {
            $debts = $this->object
                ->debts()
                ->whereIn('import_id', [$debtImport?->id, $debtDTImport?->id, $debt1CImport?->id, $debtObjectImport?->id])
                ->where('type_id', Debt::TYPE_CONTRACTOR)
                ->orderBy(Organization::select('name')->whereColumn('organizations.id', 'debts.organization_id'))
                ->with('organization')
                ->orderBy('amount')
                ->get();
        }

        if ($this->object->code === '288') {
            $result = [];

            foreach ($debts as $debt) {
                if (! isset($result[$debt->organization_id]['worktype'][$debt->object_worktype_id])) {
                    $result[$debt->organization_id]['name'] = $debt->organization->name;
                    $result[$debt->organization_id]['worktype'][$debt->object_worktype_id] = 0;
                }
                $result[$debt->organization_id]['worktype'][$debt->object_worktype_id] += $debt->amount;
            }
        } else {
            $result = [];

            foreach ($debts as $debt) {
                $id = $debt->organization_id . '::' . $debt->organization->name;
                if (! isset($result[$id])) {
                    $result[$id] = 0;
                }
                $result[$id] += $debt->amount;

                if ($forApi && $existInObjectImport) {
                    $result[$id] += $debt->avans;
                }
            }

            foreach ($debtManuals as $debtManual) {
                $issetDebt = $debts->where('organization_id', $debtManual->organization_id)->first();

                if (! $issetDebt && isset($debtManual->organization)) {
                    $id = $debtManual->organization_id . '::' . $debtManual->organization->name;
                    $result[$id] = $debtManual->amount;

                    if ($forApi && $existInObjectImport) {
                        $result[$id] += $debtManual->avans;
                    }
                }
            }

            asort($result);

            foreach ($result as $organization => $amount) {
                $organizationId = substr($organization, 0, strpos($organization, '::'));
                $debtManual = $debtManuals->where('organization_id', $organizationId)->first();

                if ($debtManual) {
                    $result[$organization] = $debtManual->amount;

                    if ($forApi && $existInObjectImport) {
                        $result[$organization] += $debtManual->avans;
                    }
                }
            }
        }


        return $result;
    }

    private function getProviderDebts(): array
    {
        $debtManuals = DebtManual::where('type_id', Debt::TYPE_PROVIDER)->where('object_id', $this->object->id)->with('organization')->get();
        $debtImport = DebtImport::where('type_id', DebtImport::TYPE_SUPPLY)->latest('date')->first();
        $debtDTImport = DebtImport::where('type_id', DebtImport::TYPE_DTTERMO)->latest('date')->first();
        $debt1CImport = DebtImport::where('type_id', DebtImport::TYPE_1C)->latest('date')->first();

        $debts = $this->object
            ->debts()
            ->whereIn('import_id', [$debtImport?->id, $debtDTImport?->id, $debt1CImport?->id])
            ->where('type_id', Debt::TYPE_PROVIDER)
            ->orderBy(Organization::select('name')->whereColumn('organizations.id', 'debts.organization_id'))
            ->with('organization')
            ->orderBy('amount')
            ->get();

        if ($this->object->code === '288') {
            $result = [];

            foreach ($debts as $debt) {
                if (! isset($result[$debt->organization_id]['worktype'][$debt->object_worktype_id])) {
                    $result[$debt->organization_id]['name'] = $debt->organization->name;
                    $result[$debt->organization_id]['worktype'][$debt->object_worktype_id] = 0;
                }
                $result[$debt->organization_id]['worktype'][$debt->object_worktype_id] += $debt->amount;
            }
        } else {
            $result = [];

            foreach ($debts as $debt) {
                $id = $debt->organization_id . '::' . $debt->organization->name;
                if (! isset($result[$id])) {
                    $result[$id] = 0;
                }

                $result[$id] += $debt->amount;
            }

            foreach ($debtManuals as $debtManual) {
                $issetDebt = $debts->where('organization_id', $debtManual->organization_id)->first();

                if (! $issetDebt) {
                    $id = $debtManual->organization_id . '::' . $debtManual->organization->name;
                    $result[$id] = $debtManual->amount;
                }
            }

            asort($result);

            foreach ($result as $organization => $amount) {
                $organizationId = substr($organization, 0, strpos($organization, '::'));
                $debtManual = $debtManuals->where('organization_id', $organizationId)->first();

                if ($debtManual) {
                    $result[$organization] = $debtManual->amount;
                }
            }
        }

        return $result;
    }

    private function getServiceDebts($forApi = false): array
    {
        $debt1CServiceImport = DebtImport::where('type_id', DebtImport::TYPE_SERVICE_1C)->latest('date')->first();
        $debts = $this->object
            ->debts()
            ->where('import_id', $debt1CServiceImport?->id)
            ->where('type_id', Debt::TYPE_SERVICE)
            ->orderBy(Organization::select('name')->whereColumn('organizations.id', 'debts.organization_id'))
            ->with('organization')
            ->orderBy('amount')
            ->get();

        $result = [];

        foreach ($debts as $debt) {
            $id = $debt->organization_id . '::' . $debt->organization->name;
            if (! isset($result[$id])) {
                $result[$id] = 0;
            }
            $result[$id] += $debt->amount;

            if ($forApi) {
                $result[$id] += $debt->avans;
            }
        }

        asort($result);

        return $result;
    }
}