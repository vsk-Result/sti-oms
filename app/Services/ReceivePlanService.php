<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\Object\BObject;
use App\Models\Object\ReceivePlan;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReceivePlanService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function getPeriods(?int $objectId = null): array
    {
        $periods = [];

        $now = Carbon::now();

        $periods[] = [
            'start' => $now->startOfWeek()->format('Y-m-d'),
            'end' => $now->endOfWeek()->format('Y-m-d'),
            'format' => $now->startOfWeek()->format('d.m.Y') . ' - ' . $now->endOfWeek()->format('d.m.Y')
        ];

        $end = Carbon::now()->addMonthsNoOverflow(3)->format('Y-m-d');
        for ($i = 1; $i < 16; $i++) {
            $newDate = Carbon::now()->addDays($i * 7);

            if ($newDate->format('Y-m-d') > $end) {
                break;
            }

            $periods[] = [
                'start' => $newDate->startOfWeek()->format('Y-m-d'),
                'end' => $newDate->endOfWeek()->format('Y-m-d'),
                'format' => $newDate->startOfWeek()->format('d.m.Y') . ' - ' . $newDate->endOfWeek()->format('d.m.Y')
            ];
        }

        if ($objectId) {
            $reasons = ReceivePlan::getReasons();

            foreach ($periods as $period) {
                foreach ($reasons as $reasonId => $reasonName) {
                    if ($this->isPlanExist($objectId, $reasonId, $period['start'])) {
                        continue;
                    }

                    $this->createReceivePlan([
                        'object_id' => $objectId,
                        'reason_id' => $reasonId,
                        'date' => $period['start'],
                        'amount' => 0,
                        'status_id' => Status::STATUS_ACTIVE
                    ]);
                }
            }

            $earlyPlans = ReceivePlan::where('object_id', $objectId)->where('date', '<', $periods[0]['start'])->get();
            foreach ($earlyPlans as $plan) {
                $issetPlan = $this->findPlan($objectId, $plan->reason_id, $periods[0]['start']);

                if ($issetPlan) {
                    $issetPlan->update([
                        'amount' => $issetPlan->amount + $plan->amount
                    ]);

                    $plan->delete();
                }
            }
        }

        return $periods;
    }

    public function getPlans(?int $objectId, string $startDate, string $endDate): Collection
    {
        if ($objectId) {
            return ReceivePlan::where('object_id', $objectId)->whereBetween('date', [$startDate, $endDate])->get();
        }

        return ReceivePlan::whereBetween('date', [$startDate, $endDate])->get();
    }

    public function isPlanExist(int $objectId, int $reasonId, string $date): bool
    {
        return (bool) $this->findPlan($objectId, $reasonId, $date);
    }

    public function createReceivePlan(array $requestData): void
    {
        ReceivePlan::create([
            'object_id' => $requestData['object_id'],
            'reason_id' => $requestData['reason_id'],
            'date' => $requestData['date'],
            'amount' => $requestData['amount'],
            'status_id' => $requestData['status_id'],
        ]);
    }

    public function updatePlan(array $requestData): void
    {
        $plan = $this->findPlan($requestData['object_id'], $requestData['reason_id'], $requestData['date']);

        if (!$plan) {
            $this->createReceivePlan([
                'object_id' => $requestData['object_id'],
                'reason_id' => $requestData['reason_id'],
                'date' => $requestData['date'],
                'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
                'status_id' => Status::STATUS_ACTIVE
            ]);

            return;
        }

        $plan->update([
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get()
        ]);
    }

    public function findPlan(int $objectId, int $reasonId, string $date): ReceivePlan | null
    {
       return ReceivePlan::where('object_id', $objectId)->where('reason_id', $reasonId)->where('date', $date)->first();
    }

    public function getAllPeriods(): array
    {
        $activeObjectIds = BObject::active()->pluck('id')->toArray();
        $closedObjectIds = ReceivePlan::groupBy('object_id')->pluck('object_id')->toArray();

        $objectIds = array_merge($activeObjectIds, $closedObjectIds);
        $data = [];


        dd($this->getPeriods());
    }

    public function getPlanPaymentTypes(): array
    {
        return [
            'НДС',
            'Налог на прибыль',
            'НДФЛ',
            'Страховые взносы',
            'Магнитогорск комиссия БГ',
            'Кемерово комиссия БГ',
            'Тинькофф комиссия БГ',
            'Аэрофлот комиссия БГ',
            'Камчатка комиссия БГ',
            'Тинькофф комиссия БГ (ГУ)',
            'Сухаревская комиссия БГ 3% (ГУ)',
            '% по кредиту',
            'Погашение ВКЛ',
            'Возврат Займа (Завидово)',
            'Возврат Займа (Камчатка)',
            'Доплата целевого аванса подрядчикам (Кемерово)',
            'Консалтинг',
            'Лизинг СТИ на ПТИ',
            'Лизинг СТИ Ресо',
            'Комиссия по кредиту за склад БАМС',
            'АХО',
            'З/П бухгалтерия',
            'Аванс (карты)',
            'З/П (карты)',
            'З/П ИТР',
            'З/П рабочие',
            'Трансфер %',
            'Стройинлок долг трансфер'
        ];
    }
}
