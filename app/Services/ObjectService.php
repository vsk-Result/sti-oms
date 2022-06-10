<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\Object\BObject;
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
            'photo' => $photo,
            'closing_date' => array_key_exists('closing_date', $requestData) ? $requestData['closing_date'] : $object->closing_date,
            'status_id' => $requestData['status_id']
        ]);

        $object->customers()->sync($requestData['customer_id'] ?? []);
    }

    public static function getGeneralCostsByPeriod(string $startDate, string $endDate, int $bonus = 0): array
    {
        $objects = BObject::with(['customers', 'payments' => function($q) use ($startDate, $endDate) {
            $q->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)
                ->where('amount', '>=', 0)
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
        $object27_8 = BObject::where('code', '27.8')->first();

        $bonus = $bonus / count($periods);

        $result = [];
        foreach ($periods as $startDate => $endDate) {
            $generalTotalAmount = Payment::whereBetween('date', [$startDate, $endDate])->where('type_id', Payment::TYPE_GENERAL)->sum('amount');
            $generalTotalAmount += Payment::whereBetween('date', [$startDate, $endDate])->where('object_id', $object27_1->id)->sum('amount');
            $generalTotalAmount += (Payment::whereBetween('date', [$startDate, $endDate])->where('object_id', $object27_8->id)->sum('amount') * 0.7);
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
}
