<?php

namespace App\Services;

use App\Models\Object\BObject;
use App\Models\PivotObjectDebt;
use App\Models\Status;
use App\Models\TaxPlanItem;
use Carbon\Carbon;

class PivotObjectDebtService
{
    const EXPIRED_UPLOAD_DEBTS_DAYS = 2;

    const SOURCES_TO_EXCEPT = [
        PivotObjectDebt::DEBT_SOURCE_PROVIDER_SUPPLY,
        PivotObjectDebt::DEBT_SOURCE_CONTRACTOR_SUPPLY,
    ];

    const SOURCES_TO_CHECK_EXPIRED = [
        PivotObjectDebt::DEBT_SOURCE_CONTRACTOR_1C,
        PivotObjectDebt::DEBT_SOURCE_PROVIDER_1C,
        PivotObjectDebt::DEBT_SOURCE_SERVICE_1C,
    ];

    public function getPivotDebts(int $objectId, int $debtType, ?array $options = []): array
    {
        $info = [];

        $withSortedDetails = $options['with_sorted_details'] ?? false;

        $closedObjectsId = BObject::where('code', '000')->first()->id;
        $isClosedObject = $objectId === $closedObjectsId;

        $objectIds = [$objectId];
        if ($isClosedObject) {
            $objectIds = BObject::where('status_id', Status::STATUS_BLOCKED)->pluck('id')->toArray();
        }

        foreach ($objectIds as $objId) {
            $info[$objId] = [
                'sources' => [],
                'total' => [
                    'amount' => 0,
                    'amount_without_nds' => 0,
                    'amount_fix' => 0,
                    'amount_float' => 0,
                    'unwork_avans' => 0,
                    'guarantee' => 0,
                    'guarantee_deadline' => 0,
                    'avans' => 0,
                    'balance_contract' => 0,
                    'total_amount' => 0,
                    'total_amount_without_nds' => 0,
                ],
                'organizations' => [],
            ];

            $needSkip = false;

            foreach (PivotObjectDebt::getSourcesByType($debtType) as $source) {

                if (in_array($source, self::SOURCES_TO_EXCEPT)) {
                    continue;
                }

                if ($needSkip) {
                    continue;
                }

                $pivot = PivotObjectDebt::where('debt_type_id', $debtType)
                    ->where('object_id', $objId)
                    ->where('debt_source_id', $source)
                    ->latest('date')
                    ->first();

                if (! $pivot) {
                    continue;
                }

                if (in_array($source, self::SOURCES_TO_CHECK_EXPIRED) && now()->diffInDays(Carbon::parse($pivot->date)) >= self::EXPIRED_UPLOAD_DEBTS_DAYS) {
                    continue;
                }

                if (! $isClosedObject) {
                    $info[$objId]['sources'][] = [
                        'source_name' => PivotObjectDebt::getSourceName($source),
                        'uploaded_date' => Carbon::parse($pivot->date)->format('d.m.Y H:i'),
                        'filepath' => $pivot->filepath,
                    ];
                }

                $details = json_decode($pivot->details, true) ?? [];

                if (count($details) > 0 && in_array($source, PivotObjectDebt::getManualSources())) {
                    $needSkip = true;

                    $info[$objId]['total'] = [
                        'amount' => 0,
                        'amount_without_nds' => 0,
                        'amount_fix' => 0,
                        'amount_float' => 0,
                        'unwork_avans' => 0,
                        'guarantee' => 0,
                        'guarantee_deadline' => 0,
                        'avans' => 0,
                        'balance_contract' => 0,
                        'total_amount' => 0,
                        'total_amount_without_nds' => 0,
                    ];
                    $info[$objId]['organizations'] = [];
                }

                $info[$objId]['total']['amount'] += $pivot->amount;
                $info[$objId]['total']['amount_without_nds'] += $pivot->amount_without_nds;
                $info[$objId]['total']['amount_fix'] += $pivot->amount_fix;
                $info[$objId]['total']['amount_float'] += $pivot->amount_float;
                $info[$objId]['total']['unwork_avans'] += $pivot->unwork_avans;
                $info[$objId]['total']['avans'] += $pivot->avans;
                $info[$objId]['total']['guarantee'] += $pivot->guarantee;
                $info[$objId]['total']['guarantee_deadline'] += $pivot->guarantee_deadline;
                $info[$objId]['total']['balance_contract'] += $pivot->balance_contract;

                foreach ($details as $organizationData) {
                    if (! isset($info[$objId]['organizations'][$organizationData['organization_name']])) {
                        $info[$objId]['organizations'][$organizationData['organization_name']] = [
                            'organization_id' => $organizationData['organization_id'],
                            'organization_name' => $organizationData['organization_name'],
                            'unwork_avans' => 0,
                            'balance_contract' => 0,
                            'guarantee' => 0,
                            'guarantee_deadline' => 0,
                            'avans' => 0,
                            'amount' => 0,
                            'amount_fix' => 0,
                            'amount_float' => 0,
                            'amount_without_nds' => 0,
                            'total_amount' => 0,
                            'total_amount_without_nds' => 0,
                            'details' => []
                        ];
                    }

                    $info[$objId]['organizations'][$organizationData['organization_name']]['amount'] += $organizationData['amount'] ?? 0;
                    $info[$objId]['organizations'][$organizationData['organization_name']]['amount_fix'] += $organizationData['amount_fix'] ?? 0;
                    $info[$objId]['organizations'][$organizationData['organization_name']]['amount_float'] += $organizationData['amount_float'] ?? 0;
                    $info[$objId]['organizations'][$organizationData['organization_name']]['amount_without_nds'] += $organizationData['amount_without_nds'] ?? 0;
                    $info[$objId]['organizations'][$organizationData['organization_name']]['unwork_avans'] += $organizationData['unwork_avans'] ?? 0;
                    $info[$objId]['organizations'][$organizationData['organization_name']]['guarantee'] += $organizationData['guarantee'] ?? 0;
                    $info[$objId]['organizations'][$organizationData['organization_name']]['guarantee_deadline'] += $organizationData['guarantee_deadline'] ?? 0;
                    $info[$objId]['organizations'][$organizationData['organization_name']]['avans'] += $organizationData['avans'] ?? 0;
                    $info[$objId]['organizations'][$organizationData['organization_name']]['balance_contract'] += $organizationData['balance_contract'] ?? 0;
                    $info[$objId]['organizations'][$organizationData['organization_name']]['total_amount'] += ($organizationData['amount'] ?? 0) + ($organizationData['avans'] ?? 0);
                    $info[$objId]['organizations'][$organizationData['organization_name']]['total_amount_without_nds'] += ($organizationData['amount_without_nds'] ?? 0) + ($organizationData['avans'] ?? 0);
                    $info[$objId]['organizations'][$organizationData['organization_name']]['details'] = array_merge($info[$objId]['organizations'][$organizationData['organization_name']]['details'], $organizationData['details'] ?? []);
                }
            }

            $info[$objId]['total']['total_amount'] = $info[$objId]['total']['amount'] + $info[$objId]['total']['avans'];
            $info[$objId]['total']['total_amount_without_nds'] = $info[$objId]['total']['amount_without_nds'] + $info[$objId]['total']['avans'];

            if ($debtType === PivotObjectDebt::DEBT_TYPE_SERVICE) {
                $this->addAdditionalServiceDebts($objId, $info);
            }

            $info[$objId]['organizations'] = $this->getClearedDetails($info[$objId]['organizations'], $withSortedDetails);
        }

        $total = [
            'sources' => [],
            'total' => [
                'amount' => 0,
                'amount_without_nds' => 0,
                'amount_fix' => 0,
                'amount_float' => 0,
                'unwork_avans' => 0,
                'guarantee' => 0,
                'guarantee_deadline' => 0,
                'avans' => 0,
                'balance_contract' => 0,
                'total_amount' => 0,
                'total_amount_without_nds' => 0,
            ],
            'organizations' => [],
        ];

        foreach ($info as $totalInfo) {
            foreach ($totalInfo['total'] as $name => $value) {
                $total['total'][$name] += $value;
            }

            $total['sources'] = array_merge($total['sources'], $totalInfo['sources']);

            foreach ($totalInfo['organizations'] as $organizationName => $organizationInfo) {
                if (! isset($total['organizations'][$organizationName])) {
                    $total['organizations'][$organizationName] = [
                        'organization_id' => $organizationInfo['organization_id'],
                        'organization_name' => $organizationInfo['organization_name'],
                        'unwork_avans' => 0,
                        'balance_contract' => 0,
                        'guarantee' => 0,
                        'guarantee_deadline' => 0,
                        'avans' => 0,
                        'amount' => 0,
                        'amount_fix' => 0,
                        'amount_float' => 0,
                        'amount_without_nds' => 0,
                        'total_amount' => 0,
                        'total_amount_without_nds' => 0,
                        'details' => [],
                    ];
                }

                $total['organizations'][$organizationName]['amount'] += $organizationInfo['amount'] ?? 0;
                $total['organizations'][$organizationName]['amount_fix'] += $organizationInfo['amount_fix'] ?? 0;
                $total['organizations'][$organizationName]['amount_float'] += $organizationInfo['amount_float'] ?? 0;
                $total['organizations'][$organizationName]['amount_without_nds'] += $organizationInfo['amount_without_nds'] ?? 0;
                $total['organizations'][$organizationName]['unwork_avans'] += $organizationInfo['unwork_avans'] ?? 0;
                $total['organizations'][$organizationName]['guarantee'] += $organizationInfo['guarantee'] ?? 0;
                $total['organizations'][$organizationName]['guarantee_deadline'] += $organizationInfo['guarantee_deadline'] ?? 0;
                $total['organizations'][$organizationName]['avans'] += $organizationInfo['avans'] ?? 0;
                $total['organizations'][$organizationName]['balance_contract'] += $organizationInfo['balance_contract'] ?? 0;
                $total['organizations'][$organizationName]['total_amount'] += ($organizationInfo['amount'] ?? 0) + ($organizationInfo['avans'] ?? 0);
                $total['organizations'][$organizationName]['total_amount_without_nds'] += ($organizationInfo['amount_without_nds'] ?? 0) + ($organizationInfo['avans'] ?? 0);
                $total['organizations'][$organizationName]['details'] = array_merge($total['organizations'][$organizationName]['details'], $organizationInfo['details'] ?? []);
            }
        }

        if ($withSortedDetails) {
            $total['organizations'] = $this->getSortedDetails($total['organizations']);
        }

        return $total;
    }

    public function updatePivotDebtInfo(array $data, int $debtType, int $debtSource, string $filepath): void
    {
        foreach ($data as $objectData) {
            $this->updatePivotDebtForObject($objectData, $debtType, $debtSource, $filepath);
        }
    }

    public function updatePivotDebtForObject(array $data, int $debtType, int $debtSource, string $filepath): void
    {
        $pivot = PivotObjectDebt::where('date', now()->format('Y-m-d'))
            ->where('object_id', $data['object_id'])
            ->where('debt_type_id', $debtType)
            ->where('debt_source_id', $debtSource)
            ->first();

        if (! $pivot) {
            $pivot = PivotObjectDebt::create([
                'date' => now(),
                'object_id' => $data['object_id'],
                'debt_type_id' => $debtType,
                'debt_source_id' => $debtSource,
            ]);
        }

        $pivot->update([
            'date' => now(),
            'filepath' => $filepath,
            'amount' => $data['total_amount'] ?? 0,
            'amount_without_nds' => $data['total_amount_without_nds'] ?? 0,
            'amount_fix' => $data['total_amount_fix'] ?? 0,
            'amount_float' => $data['total_amount_float'] ?? 0,
            'avans' => $data['total_avans'] ?? 0,
            'guarantee' => $data['total_guarantee'] ?? 0,
            'guarantee_deadline' => $data['total_guarantee_deadline'] ?? 0,
            'balance_contract' => $data['total_balance_contract'] ?? 0,
            'unwork_avans' => $data['total_unwork_avans'] ?? 0,
            'details' => json_encode($data['organizations'])
        ]);
    }

    public function hasExpiredManualPivots(int $objectId): bool
    {
        $pivots = PivotObjectDebt::where('object_id', $objectId)
            ->whereIn('debt_source_id', [
                PivotObjectDebt::DEBT_SOURCE_CONTRACTOR_MANUAL,
                PivotObjectDebt::DEBT_SOURCE_PROVIDER_MANUAL,
                PivotObjectDebt::DEBT_SOURCE_SERVICE_MANUAL
            ])
            ->get();

        foreach ($pivots as $pivot) {
            if (Carbon::now()->diffInDays(Carbon::parse($pivot->date)) >= self::EXPIRED_UPLOAD_DEBTS_DAYS) {
                return true;
            }
        }

        return false;
    }


    private function addAdditionalServiceDebts(int $objectId, array &$info): void
    {
        $komissiyaServiceAmount = 0;
        $komissiyaBGServiceAmount = 0;
        $komissiyaBG_GU_ServiceAmount = 0;

        $komissiyaServiceItems = TaxPlanItem::where('object_id', $objectId)->where('paid', 0)->where('name', 'LIKE', '%комис%')->get();
        foreach ($komissiyaServiceItems as $item) {
            if (mb_strpos($item->name, 'БГ') > 0 || mb_strpos($item->name, 'бг') > 0) {

                if (mb_strpos($item->name, 'гу') > 0 || mb_strpos($item->name, 'ГУ') > 0) {
                    $komissiyaBG_GU_ServiceAmount += $item->amount;
                    continue;
                }

                $komissiyaBGServiceAmount += $item->amount;
                continue;
            }

            $komissiyaServiceAmount += $item->amount;
        }

        $konsaltingServiceAmount = TaxPlanItem::where('object_id', $objectId)->where('paid', 0)->where('name', 'LIKE', '%консалтинг%')->sum('amount');

        if ($komissiyaServiceAmount != 0) {
            $info[$objectId]['organizations']['Комиссия'] = [
                'organization_id' => null,
                'organization_name' => 'Комиссия',
                'amount' => 0,
                'amount_without_nds' => 0,
                'avans' => -$komissiyaServiceAmount,
                'total_amount' => -$komissiyaServiceAmount,
            ];
            $info[$objectId]['total']['avans'] += -$komissiyaServiceAmount;
            $info[$objectId]['total']['amount'] += 0;
            $info[$objectId]['total']['amount_without_nds'] += 0;
            $info[$objectId]['total']['total_amount'] += -$komissiyaServiceAmount;
        }

        if ($komissiyaBGServiceAmount != 0) {
            $info[$objectId]['organizations']['Комиссия за БГ'] = [
                'organization_id' => null,
                'organization_name' => 'Комиссия за БГ',
                'avans' => -$komissiyaBGServiceAmount,
                'amount' => 0,
                'amount_without_nds' => 0,
                'total_amount' => -$komissiyaBGServiceAmount,
            ];
            $info[$objectId]['total']['avans'] += -$komissiyaBGServiceAmount;
            $info[$objectId]['total']['amount'] += 0;
            $info[$objectId]['total']['amount_without_nds'] += 0;
            $info[$objectId]['total']['total_amount'] += -$komissiyaBGServiceAmount;
        }

        if ($komissiyaBG_GU_ServiceAmount != 0) {
            $info[$objectId]['organizations']['Комиссия за БГ (г/у)'] = [
                'organization_id' => null,
                'organization_name' => 'Комиссия за БГ (г/у)',
                'avans' => -$komissiyaBG_GU_ServiceAmount,
                'amount' => 0,
                'amount_without_nds' => 0,
                'total_amount' => -$komissiyaBG_GU_ServiceAmount,
            ];
            $info[$objectId]['total']['avans'] += -$komissiyaBG_GU_ServiceAmount;
            $info[$objectId]['total']['amount'] += 0;
            $info[$objectId]['total']['amount_without_nds'] += 0;
            $info[$objectId]['total']['total_amount'] += -$komissiyaBG_GU_ServiceAmount;
        }

        if ($konsaltingServiceAmount != 0) {
            $info[$objectId]['organizations']['Консалтинг'] = [
                'organization_id' => null,
                'organization_name' => 'Консалтинг',
                'amount' => -$konsaltingServiceAmount,
                'amount_without_nds' => -$konsaltingServiceAmount,
                'total_amount' => -$konsaltingServiceAmount,
            ];
            $info[$objectId]['total']['amount'] += -$konsaltingServiceAmount;
            $info[$objectId]['total']['amount_without_nds'] += -$konsaltingServiceAmount;
            $info[$objectId]['total']['total_amount'] += -$konsaltingServiceAmount;
        }
    }

    private function getSortedDetails(array $details): array
    {
        $result = $details;

        $totalAmounts = array_column($result, 'total_amount');
        array_multisort($totalAmounts, SORT_ASC, $result);

        return $result;
    }

    private function getClearedDetails(array $details, bool $forView = false): array
    {
        $result = [];
        $exceptKeys = ['organization_id', 'organization_name', 'details'];

        if ($forView) {
            $exceptKeys[] = 'balance_contract';
        }

        foreach ($details as $organizationId => $organizationData) {
            $valid = false;

            foreach ($organizationData as $key => $amount) {
                if (in_array($key, $exceptKeys)) {
                    continue;
                }

                if (is_valid_amount_in_range($amount)) {
                    $valid = true;
                    break;
                }
            }

            if ($valid) {
                $result[$organizationId] = $organizationData;
            }
        }

        return $result;
    }

    public function getPivotDebtsForOrganizations(array $options): array
    {
        $pivot = [
            'organizations' => [],
            'total' => [
                'total' => 0,
                'contractors' => [
                    'total' => 0,
                    'unwork_avans' => 0,
                    'guarantee' => 0,
                    'guarantee_deadline' => 0,
                    'avans' => 0,
                    'amount' => 0,
                ],
                'providers' => [
                    'total' => 0,
                    'amount_fix' => 0,
                    'amount_float' => 0,
                ],
                'service' => [
                    'total' => 0,
                    'amount' => 0,
                ],
            ],
        ];

        $objects = BObject::active()->get();

        foreach ($objects as $object) {
            if ($object->code === '288') continue;

            $contractorDebts = $this->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_CONTRACTOR);
            $providerDebts = $this->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_PROVIDER);
            $serviceDebts = $this->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_SERVICE);

            $contractorDebtsAmount = $contractorDebts['total']['total_amount'];
            $providerDebtsAmount = $providerDebts['total']['amount'];
            $serviceDebtsAmount = $serviceDebts['total']['total_amount'];

            if (($contractorDebtsAmount + $providerDebtsAmount + $serviceDebtsAmount) == 0) {
                continue;
            }

            foreach ($contractorDebts['organizations'] as $organizationInfo) {

                $organizationName = $organizationInfo['organization_name'];

                if (isset($options['organization_names']) && count($options['organization_names']) > 0) {
                    if (! in_array($organizationName, $options['organization_names'])) {
                        continue;
                    }
                }

                if (isset($options['organization_ids']) && count($options['organization_ids']) > 0) {
                    if (! in_array($organizationInfo['organization_id'], $options['organization_ids'])) {
                        continue;
                    }
                }

                if (!isset($pivot['organizations'][$organizationName])) {
                    $pivot['organizations'][$organizationName] = [
                        'total' => 0,
                        'contractors' => [
                            'total' => 0,
                            'unwork_avans' => 0,
                            'guarantee' => 0,
                            'guarantee_deadline' => 0,
                            'avans' => 0,
                            'amount' => 0,
                        ],
                        'providers' => [
                            'total' => 0,
                            'amount_fix' => 0,
                            'amount_float' => 0,
                        ],
                        'service' => [
                            'total' => 0,
                            'amount' => 0,
                        ],
                    ];
                }

                $pivot['organizations'][$organizationName]['contractors']['unwork_avans'] += $organizationInfo['unwork_avans'];
                $pivot['organizations'][$organizationName]['contractors']['guarantee'] += $organizationInfo['guarantee'];
                $pivot['organizations'][$organizationName]['contractors']['guarantee_deadline'] += $organizationInfo['guarantee_deadline'];
                $pivot['organizations'][$organizationName]['contractors']['avans'] += $organizationInfo['avans'];
                $pivot['organizations'][$organizationName]['contractors']['amount'] += $organizationInfo['amount'];
                $pivot['organizations'][$organizationName]['contractors']['total'] += $organizationInfo['total_amount'];

                $pivot['total']['contractors']['unwork_avans'] += $organizationInfo['unwork_avans'];
                $pivot['total']['contractors']['guarantee'] += $organizationInfo['guarantee'];
                $pivot['total']['contractors']['guarantee_deadline'] += $organizationInfo['guarantee_deadline'];
                $pivot['total']['contractors']['avans'] += $organizationInfo['avans'];
                $pivot['total']['contractors']['amount'] += $organizationInfo['amount'];
                $pivot['total']['contractors']['total'] += $organizationInfo['total_amount'];

                $pivot['total']['total'] += $organizationInfo['total_amount'];
                $pivot['organizations'][$organizationName]['total'] += $organizationInfo['total_amount'];
            }

            foreach ($providerDebts['organizations'] as $organizationInfo) {

                $organizationName = $organizationInfo['organization_name'];

                if (isset($options['organization_names']) && count($options['organization_names']) > 0) {
                    if (! in_array($organizationName, $options['organization_names'])) {
                        continue;
                    }
                }

                if (isset($options['organization_ids']) && count($options['organization_ids']) > 0) {
                    if (! in_array($organizationInfo['organization_id'], $options['organization_ids'])) {
                        continue;
                    }
                }

                if (!isset($pivot['organizations'][$organizationName])) {
                    $pivot['organizations'][$organizationName] = [
                        'total' => 0,
                        'contractors' => [
                            'total' => 0,
                            'unwork_avans' => 0,
                            'guarantee' => 0,
                            'guarantee_deadline' => 0,
                            'avans' => 0,
                            'amount' => 0,
                        ],
                        'providers' => [
                            'total' => 0,
                            'amount_fix' => 0,
                            'amount_float' => 0,
                        ],
                        'service' => [
                            'total' => 0,
                            'amount' => 0,
                        ],
                    ];
                }

                $pivot['organizations'][$organizationName]['providers']['amount_fix'] += $organizationInfo['amount_fix'];
                $pivot['organizations'][$organizationName]['providers']['amount_float'] += $organizationInfo['amount_float'];
                $pivot['organizations'][$organizationName]['providers']['total'] += $organizationInfo['total_amount'];

                $pivot['total']['providers']['amount_fix'] += $organizationInfo['amount_fix'];
                $pivot['total']['providers']['amount_float'] += $organizationInfo['amount_float'];
                $pivot['total']['providers']['total'] += $organizationInfo['total_amount'];

                $pivot['total']['total'] += $organizationInfo['total_amount'];
                $pivot['organizations'][$organizationName]['total'] += $organizationInfo['total_amount'];
            }

            foreach ($serviceDebts['organizations'] as $organizationInfo) {

                $organizationName = $organizationInfo['organization_name'];

                if (isset($options['organization_names']) && count($options['organization_names']) > 0) {
                    if (! in_array($organizationName, $options['organization_names'])) {
                        continue;
                    }
                }

                if (isset($options['organization_ids']) && count($options['organization_ids']) > 0) {
                    if (! in_array($organizationInfo['organization_id'], $options['organization_ids'])) {
                        continue;
                    }
                }

                if (!isset($pivot['organizations'][$organizationName])) {
                    $pivot['organizations'][$organizationName] = [
                        'total' => 0,
                        'contractors' => [
                            'total' => 0,
                            'unwork_avans' => 0,
                            'guarantee' => 0,
                            'guarantee_deadline' => 0,
                            'avans' => 0,
                            'amount' => 0,
                        ],
                        'providers' => [
                            'total' => 0,
                            'amount_fix' => 0,
                            'amount_float' => 0,
                        ],
                        'service' => [
                            'total' => 0,
                            'amount' => 0,
                        ],
                    ];
                }

                $pivot['organizations'][$organizationName]['service']['amount'] += $organizationInfo['amount'];
                $pivot['organizations'][$organizationName]['service']['total'] += $organizationInfo['total_amount'];

                $pivot['total']['service']['amount'] += $organizationInfo['amount'];
                $pivot['total']['service']['total'] += $organizationInfo['total_amount'];

                $pivot['total']['total'] += $organizationInfo['total_amount'];
                $pivot['organizations'][$organizationName]['total'] += $organizationInfo['total_amount'];
            }
        }

        return $pivot;
    }
}