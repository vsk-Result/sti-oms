<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\FinanceReport;
use App\Models\Object\BObject;
use App\Models\Object\GeneralCost;
use App\Models\Object\PlanPayment;
use App\Models\Payment;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ObjectService
{
    private UploadService $uploadService;
    private Sanitizer $sanitizer;

    public function __construct(UploadService $uploadService, Sanitizer $sanitizer)
    {
        $this->uploadService = $uploadService;
        $this->sanitizer = $sanitizer;
    }

    public function createObject(array $requestData): BObject
    {
        $object = BObject::create([
            'code' => $this->sanitizer->set($requestData['code'])->toCode()->get(),
            'is_without_worktype' => false,
            'name' => $this->sanitizer->set($requestData['name'])->upperCaseFirstWord()->get(),
            'address' => $this->sanitizer->set($requestData['address'])->upperCaseFirstWord()->get(),
            'responsible_name' => $this->sanitizer->set($requestData['responsible_name'])->upperCaseAllFirstWords()->get(),
            'responsible_email' => $this->sanitizer->set($requestData['responsible_email'])->toEmail()->get(),
            'responsible_phone' => $this->sanitizer->set($requestData['responsible_phone'])->toPhone()->get(),
            'closing_date' => $requestData['closing_date'] ?? null,
            'photo' => empty($requestData['photo'])
                ? null
                : $this->uploadService->uploadFile('objects/photo', $requestData['photo']),
            'status_id' => Status::STATUS_ACTIVE
        ]);

        $object->customers()->sync($requestData['customer_id'] ?? []);

        return $object;
    }

    public function updateObject(BObject $object, array $requestData): void
    {
        if (array_key_exists('photo', $requestData)) {
            $photo = $this->uploadService->uploadFile('objects/photo', $requestData['photo']);
        } elseif ($requestData['avatar_remove'] === '1') {
            $photo = null;
        } else {
            $photo = $object->photo;
        }

        $object->update([
            'code' => $this->sanitizer->set($requestData['code'])->toCode()->get(),
            'is_without_worktype' => false,
            'name' => $this->sanitizer->set($requestData['name'])->upperCaseFirstWord()->get(),
            'address' => $this->sanitizer->set($requestData['address'])->upperCaseFirstWord()->get(),
            'responsible_name' => $this->sanitizer->set($requestData['responsible_name'])->upperCaseAllFirstWords()->get(),
            'responsible_email' => $this->sanitizer->set($requestData['responsible_email'])->toEmail()->get(),
            'responsible_phone' => $this->sanitizer->set($requestData['responsible_phone'])->toPhone()->get(),
            'free_limit_amount' => $this->sanitizer->set($requestData['free_limit_amount'] ?? 0)->toAmount()->get(),
            'photo' => $photo,
            'closing_date' => array_key_exists('closing_date', $requestData) ? $requestData['closing_date'] : $object->closing_date,
            'status_id' => $requestData['status_id']
        ]);

        $prognozFields = FinanceReport::getPrognozFields();
        foreach ($prognozFields as $field) {
            $planPayment = $object->planPayments->where('field', $field)->first();
            if (!$planPayment) {
                continue;
            }

            $planPayment->update([
                'amount' => is_null($requestData[$field]) ? 0 : -abs($this->sanitizer->set($requestData[$field])->toAmount()->get()),
                'type_id' => is_null($requestData[$field]) ? PlanPayment::TYPE_AUTO : PlanPayment::TYPE_MANUAL
            ]);
        }

        $object->customers()->sync($requestData['customer_id'] ?? []);
    }

    public static function getGeneralCostsByPeriod(string $startDate, string $endDate, int $bonus = 0): array
    {
        if (str_contains($startDate, '2017-')) {
            return [
                3 => [
                    'cuming_amount' => 745126101.73,
                    'general_amount' => -56201813.5229337
                ],
                4 => [
                    'cuming_amount' => 4054172657,
                    'general_amount' => -305789658,
                ],
                5 => [
                    'cuming_amount' => 9853343,
                    'general_amount' => -743197.35220357,
                ],
                30 => [
                    'cuming_amount' => 49520936,
                    'general_amount' => -3735161,61102303,
                ],
                86 => [
                    'cuming_amount' => 10000000,
                    'general_amount' => -754259.089735911,
                ],
            ];
        }

        if (str_contains($startDate, '2018-')) {
            return [
                3 => [
                    'cuming_amount' => 118487151.64,
                    'general_amount' => -9102449.43224355
                ],
                4 => [
                    'cuming_amount' => 7397515613,
                    'general_amount' => -568293784,
                ],
                5 => [
                    'cuming_amount' => 22477253,
                    'general_amount' => -1726753.12028663,
                ],
                30 => [
                    'cuming_amount' => 176664776,
                    'general_amount' => -13571785.3601923,
                ],
                86 => [
                    'cuming_amount' => 29049413,
                    'general_amount' => -2231641.23036943,
                ],
                28 => [
                    'cuming_amount' => 35743797,
                    'general_amount' => -2745918.86297857,
                ],
                25 => [
                    'cuming_amount' => 0,
                    'general_amount' => 0,
                ],
                6 => [
                    'cuming_amount' => 48335681,
                    'general_amount' => -3713255.70735574,
                ],
                39 => [
                    'cuming_amount' => 83430465,
                    'general_amount' => -6409315.93223221,
                ],
                59 => [
                    'cuming_amount' => 21148199,
                    'general_amount' => -1624652.20334943,
                ],
            ];
        }

        $codes = GeneralCost::getObjectCodesForGeneralCosts();
        $objectsQuery = BObject::query()->whereIn('code', $codes);

        // исключили 349 из 2023 года по письму от Оксаны 30 мая 2023
        if (str_contains($startDate, '2023')) {
            $objectsQuery->where('code', '!=', '349');
        }

        $objects = $objectsQuery->with(['customers', 'payments' => function($q) use ($startDate, $endDate) {
            $q->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)
                ->where('amount', '>=', 0)
                ->whereIn('company_id', [1, 5])
                ->whereBetween('date', [$startDate, $endDate]);
        }])->get();

        $closingDates = [];
        $finalObjects = new Collection();
        foreach ($objects as $object) {
            if (
                $object->payments
                    ->whereIn('organization_sender_id', $object->customers->pluck('id')->toArray())
                    ->sum('amount')
                > 0
            ) {
                if (! empty($object->closing_date)) {
                    if ($object->closing_date < $startDate) {
                        continue;
                    }
                    if ($object->closing_date >= $startDate && $object->closing_date <= $endDate) {
                        $closingDates[] = $object->closing_date;
                    }
                    $finalObjects->push($object);
                } else {
                    $finalObjects->push($object);
                }
            }
        }

        sort($closingDates);
        $closingDates = array_unique($closingDates);

        $periods = [$startDate => $endDate];
        if (! empty($closingDates)) {
            foreach ($closingDates as $date) {
                $periods[$startDate] = $date;
                if ($date === $endDate) {
                    break;
                } else {
                    $startDate = Carbon::parse($date)->addDay()->format('Y-m-d');
                }
            }
            if ($date !== $endDate) {
                $periods[$startDate] = $endDate;
            }
        }

        $object27_1 = BObject::where('code', '27.1')->first();

        $bonus = $bonus / count($periods);

        $result = [];
        foreach ($periods as $startDate => $endDate) {
            $generalTotalAmount = Payment::whereBetween('date', [$startDate, $endDate])
                ->where('code', '!=', '7.15')
                ->where('type_id', Payment::TYPE_GENERAL)
                ->whereIn('company_id', [1, 5])
                ->sum('amount');
            $generalTotalAmount += Payment::whereBetween('date', [$startDate, $endDate])
                ->where('object_id', $object27_1->id)
                ->whereIn('company_id', [1, 5])
                ->sum('amount');
            $generalTotalAmount += $bonus;

            $sumCumings = 0;
            $cumings = [];
            foreach ($finalObjects as $object) {
                $amount = $object->payments
                    ->whereBetween('date', [$startDate, $endDate])
                    ->whereIn('organization_sender_id', $object->customers->pluck('id')->toArray())
                    ->sum('amount');

                if ($amount > 0) {
                    $cumings[$object->id] = [
                        'cuming' => $amount,
                        'general_costs' => 0,
                    ];
                    $sumCumings += $amount;

                    if (! isset($result[$object->id])) {
                        $result[$object->id] = [
                            'cuming_amount' => 0,
                            'general_amount' => 0
                        ];
                    }
                }
            }

            foreach ($cumings as $objectId => $cuming) {
                $result[$objectId]['cuming_amount'] = $result[$objectId]['cuming_amount'] + $cuming['cuming'];
                $result[$objectId]['general_amount'] = $result[$objectId]['general_amount'] + ($cuming['cuming'] / $sumCumings * $generalTotalAmount);
            }
        }

        return $result;
    }

    public static function getDistributionTransferServiceByPeriod(array $datesBetween): array
    {
        [$startDate, $endDate] = $datesBetween;
        $codes = GeneralCost::getObjectCodesForGeneralCosts();
        $objectsQuery = BObject::query()->whereIn('code', $codes);

        // исключили 349 из 2023 года по письму от Оксаны 30 мая 2023
        if (str_contains($startDate, '2023')) {
            $objectsQuery->where('code', '!=', '349');
        }

        $objects = $objectsQuery->with(['payments' => function($q) use ($startDate, $endDate) {
            $q->where('payment_type_id', Payment::PAYMENT_TYPE_CASH)
                ->where('amount', '<=', 0)
                ->whereIn('company_id', [1, 5])
                ->whereBetween('date', [$startDate, $endDate]);
        }])->get();

        $closingDates = [];
        $finalObjects = new Collection();
        foreach ($objects as $object) {
            if ($object->payments->sum('amount') < 0) {
                if (! empty($object->closing_date)) {
                    if ($object->closing_date < $startDate) {
                        continue;
                    }
                    if ($object->closing_date >= $startDate && $object->closing_date <= $endDate) {
                        $closingDates[] = $object->closing_date;
                    }
                    $finalObjects->push($object);
                } else {
                    $finalObjects->push($object);
                }
            }
        }

        sort($closingDates);
        $closingDates = array_unique($closingDates);

        $periods = [$startDate => $endDate];
        if (! empty($closingDates)) {
            foreach ($closingDates as $date) {
                $periods[$startDate] = $date;
                if ($date === $endDate) {
                    break;
                } else {
                    $startDate = Carbon::parse($date)->addDay()->format('Y-m-d');
                }
            }
            if ($date !== $endDate) {
                $periods[$startDate] = $endDate;
            }
        }

        $result = [];
        foreach ($periods as $startDate => $endDate) {
            $transferAmount = Payment::query()
                ->whereBetween('date', [$startDate, $endDate])
                ->whereIn('company_id', [1, 5])
                ->where('code', '7.15')
                ->where('type_id', Payment::TYPE_GENERAL)
                ->sum('amount');

            $sumCash = 0;
            $cashes = [];
            foreach ($finalObjects as $object) {
                $cashAmount = $object->payments
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('payment_type_id', Payment::PAYMENT_TYPE_CASH)
                    ->where('amount', '<=', 0)
                    ->sum('amount');

                $cashes[$object->id] = [
                    'cash' => $cashAmount,
                    'transfer' => 0,
                ];

                $sumCash += $cashAmount;

                if (! isset($result[$object->id])) {
                    $result[$object->id] = [
                        'cash_amount' => 0,
                        'transfer_amount' => 0
                    ];
                }
            }

            if ($sumCash === 0 || $transferAmount === 0) {
                continue;
            }

            foreach ($cashes as $objectId => $cash) {
                $result[$objectId]['cash_amount'] = $result[$objectId]['cash_amount'] + $cash['cash'];
                $result[$objectId]['transfer_amount'] = $result[$objectId]['transfer_amount'] + ($cash['cash'] / $sumCash * $transferAmount);
            }
        }

        return $result;
    }
}
