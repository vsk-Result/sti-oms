<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\CashFlow\Notification;
use App\Models\CurrencyExchangeRate;
use App\Models\Object\BObject;
use App\Models\Object\CashFlowPayment;
use App\Models\Object\ReceivePlan;
use App\Models\Status;
use App\Services\CashFlow\NotificationService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ReceivePlanService
{
    private Sanitizer $sanitizer;
    private NotificationService $notificationService;
    private CurrencyExchangeRateService $currencyExchangeService;

    public function __construct(Sanitizer $sanitizer, NotificationService $notificationService, CurrencyExchangeRateService $currencyExchangeService)
    {
        $this->sanitizer = $sanitizer;
        $this->notificationService = $notificationService;
        $this->currencyExchangeService = $currencyExchangeService;
    }

    public function getPeriods(?int $objectId = null, ?string $initialPeriod = null): array
    {
        $periods = [];

        $start = Carbon::now();
        $end = Carbon::now()->addMonthsNoOverflow(3)->format('Y-m-d');

        if ($initialPeriod) {
            [$startInitial, $endInitial] = explode(' - ', $initialPeriod);

            $start = Carbon::parse($startInitial);
            $end = Carbon::parse($endInitial)->format('Y-m-d');
        }

        $periodId = 0;
        $periods[] = [
            'id' => $periodId++,
            'start' => $start->startOfWeek()->format('Y-m-d'),
            'end' => $start->endOfWeek()->format('Y-m-d'),
            'format' => $start->startOfWeek()->format('d.m.Y') . ' - ' . $start->endOfWeek()->format('d.m.Y')
        ];

        for ($i = 1; $i < 16; $i++) {
            $newDate = Carbon::now()->addDays($i * 7);

            if ($newDate->format('Y-m-d') > $end) {
                break;
            }

            $periods[] = [
                'id' => $periodId++,
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

    public function createReceivePlan(array $requestData): ReceivePlan
    {
        return ReceivePlan::create([
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
        $period = Carbon::parse($requestData['date'])->startOfWeek()->format('d.m.Y') . ' - ' . Carbon::parse($requestData['date'])->endOfWeek()->format('d.m.Y');

        if (!$plan) {
            $plan = $this->createReceivePlan([
                'object_id' => $requestData['object_id'],
                'reason_id' => $requestData['reason_id'],
                'date' => $requestData['date'],
                'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
                'status_id' => Status::STATUS_ACTIVE
            ]);

            $this->notificationService->createNotification(
                Notification::TYPE_RECEIVE,
                Notification::EVENT_TYPE_UPDATE,
                'Сумма прихода "' . $plan->getReason() . '" изменилась с "0 ₽' . '" на "' . CurrencyExchangeRate::format($this->sanitizer->set($requestData['amount'])->toAmount()->get(), 'RUB') . '" за период "' . $period . '"'
            );

            return;
        }


        $this->notificationService->createNotification(
            Notification::TYPE_RECEIVE,
            Notification::EVENT_TYPE_UPDATE,
            'Сумма прихода "' . $plan->getReason() . '" изменилась с "' . CurrencyExchangeRate::format($plan->amount, 'RUB') . '" на "' . CurrencyExchangeRate::format($this->sanitizer->set($requestData['amount'])->toAmount()->get(), 'RUB') . '" за период "' . $period . '"'
        );

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
            'Магнитогорск комиссия БГ (абсолют)',
            'Магнитогорск комиссия БГ (куб)',
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
            'З/П бухгалтерия',
            'Аванс (карты)',
            'З/П (карты)',
            'З/П ИТР',
            'З/П рабочие',
            'Трансфер %',
            'Стройинлок долг трансфер',
            'ТЕХНО Интерьер ГУ',
            'ДТ Термо Таможня'
        ];
    }

    public function getGroupedPlanPaymentTypes(): array
    {
        return [
            'Банковские затраты' => [
                'Магнитогорск комиссия БГ (абсолют)',
                'Магнитогорск комиссия БГ (куб)',
                'Кемерово комиссия БГ',
                'Тинькофф комиссия БГ',
                'Аэрофлот комиссия БГ',
                'Камчатка комиссия БГ',
                'Тинькофф комиссия БГ (ГУ)',
                'Сухаревская комиссия БГ 3% (ГУ)',
                '% по кредиту',
                'Погашение ВКЛ',
            ],
            'Налоги' => [
                'НДС',
                'Налог на прибыль',
                'НДФЛ',
                'Страховые взносы',
                'Пени',
            ],
        ];
    }

    public function getCFPayments(int | null $objectId, array $periods): array
    {
        $payments = [
            'total' => [],
            'contractors' => [],
            'providers_fix' => [],
            'providers_float' => [],
            'service' => [],
            'details' => [
                'contractors' => [],
                'providers_fix' => [],
                'providers_float' => [],
                'service' => [],
            ]
        ];

        $info = Cache::get('cash_flow_1c_data', []);
        $manualPayments = CashFlowPayment::where('object_id', $objectId)->get();

        if (! isset($info[$objectId]) && $manualPayments->count() === 0) {
            return $payments;
        }

        $payments['total']['no_paid'] = 0;
        $payments['contractors']['no_paid'] = 0;
        $payments['providers_fix']['no_paid'] = 0;
        $payments['providers_float']['no_paid'] = 0;
        $payments['service']['no_paid'] = 0;

        foreach ($periods as $index => $period) {
            $payments['total'][$period['start']] = 0;
            $payments['contractors'][$period['start']] = 0;
            $payments['providers_fix'][$period['start']] = 0;
            $payments['providers_float'][$period['start']] = 0;
            $payments['service'][$period['start']] = 0;
        }

        if ($manualPayments->count() > 0) {
            $contractors = $manualPayments->where('category_id', CashFlowPayment::CATEGORY_RAD);
            $providersFix = $manualPayments->where('category_id', CashFlowPayment::CATEGORY_MATERIAL_FIX);
            $providersFloat = $manualPayments->where('category_id', CashFlowPayment::CATEGORY_MATERIAL_FLOAT);
            $services = $manualPayments->where('category_id', CashFlowPayment::CATEGORY_SERVICE);

            foreach ($periods as $index => $period) {
                foreach ($contractors as $contractor) {
                    if (! isset($payments['details']['contractors'][$contractor->organization->name][$period['start']])) {
                        $payments['details']['contractors'][$contractor->organization->name][$period['start']] = 0;
                    }

                    if (! isset($payments['details']['contractors'][$contractor->organization->name]['no_paid'])) {
                        $payments['details']['contractors'][$contractor->organization->name]['no_paid'] = 0;
                    }

                    if ($index === 0) {
                        if ($contractor->date < $period['start']) {
                            $payments['contractors']['no_paid'] += $contractor['amount'];
                            $payments['total']['no_paid'] += $contractor['amount'];

                            $payments['details']['contractors'][$contractor->organization->name]['no_paid'] += $contractor['amount'];
                        } else if ($contractor->date <= $period['end']) {
                            $payments['contractors'][$period['start']] += $contractor['amount'];
                            $payments['total'][$period['start']] += $contractor['amount'];

                            $payments['details']['contractors'][$contractor->organization->name][$period['start']] += $contractor['amount'];
                        }
                    } else {
                        if ($contractor->date >= $period['start'] && $contractor->date <= $period['end']) {
                            $payments['contractors'][$period['start']] += $contractor['amount'];
                            $payments['total'][$period['start']] += $contractor['amount'];

                            $payments['details']['contractors'][$contractor->organization->name][$period['start']] += $contractor['amount'];
                        }
                    }
                }

                foreach ($providersFix as $fix) {
                    if (! isset($payments['details']['providers_fix'][$fix->organization->name][$period['start']])) {
                        $payments['details']['providers_fix'][$fix->organization->name][$period['start']] = 0;
                    }

                    if (! isset($payments['details']['providers_fix'][$fix->organization->name]['no_paid'])) {
                        $payments['details']['providers_fix'][$fix->organization->name]['no_paid'] = 0;
                    }

                    if ($index === 0) {
                        if ($fix->date < $period['start']) {
                            $payments['providers_fix']['no_paid'] += $fix['amount'];
                            $payments['total']['no_paid'] += $fix['amount'];

                            $payments['details']['providers_fix'][$fix->organization->name]['no_paid'] += $fix['amount'];
                        } else if ($fix->date <= $period['end']) {
                            $payments['providers_fix'][$period['start']] += $fix['amount'];
                            $payments['total'][$period['start']] += $fix['amount'];

                            $payments['details']['providers_fix'][$fix->organization->name][$period['start']] += $fix['amount'];
                        }
                    } else {
                        if ($fix->date >= $period['start'] && $fix->date <= $period['end']) {
                            $payments['providers_fix'][$period['start']] += $fix['amount'];
                            $payments['total'][$period['start']] += $fix['amount'];

                            $payments['details']['providers_fix'][$fix->organization->name][$period['start']] += $fix['amount'];
                        }
                    }
                }

                foreach ($providersFloat as $float) {
                    if (! isset($payments['details']['providers_float'][$float->organization->name][$period['start']])) {
                        $payments['details']['providers_float'][$float->organization->name][$period['start']] = 0;
                    }

                    if (! isset($payments['details']['providers_float'][$float->organization->name]['no_paid'])) {
                        $payments['details']['providers_float'][$float->organization->name]['no_paid'] = 0;
                    }

                    if ($index === 0) {
                        if ($float->date < $period['start']) {
                            $payments['providers_float']['no_paid'] += $float['amount'];
                            $payments['total']['no_paid'] += $float['amount'];

                            $payments['details']['providers_float'][$float->organization->name]['no_paid'] += $float['amount'];
                        } else if ($float->date <= $period['end']) {
                            $payments['providers_float'][$period['start']] += $float['amount'];
                            $payments['total'][$period['start']] += $float['amount'];

                            $payments['details']['providers_float'][$float->organization->name][$period['start']] += $float['amount'];
                        }
                    } else {
                        if ($float->date >= $period['start'] && $float->date <= $period['end']) {
                            $payments['providers_float'][$period['start']] += $float['amount'];
                            $payments['total'][$period['start']] += $float['amount'];

                            $payments['details']['providers_float'][$float->organization->name][$period['start']] += $float['amount'];
                        }
                    }
                }

                foreach ($services as $service) {
                    if (! isset($payments['details']['service'][$service->organization->name][$period['start']])) {
                        $payments['details']['service'][$service->organization->name][$period['start']] = 0;
                    }

                    if (! isset($payments['details']['service'][$service->organization->name]['no_paid'])) {
                        $payments['details']['service'][$service->organization->name]['no_paid'] = 0;
                    }

                    if ($index === 0) {
                        if ($service->date < $period['start']) {
                            $payments['service']['no_paid'] += $service['amount'];
                            $payments['total']['no_paid'] += $service['amount'];

                            $payments['details']['service'][$service->organization->name]['no_paid'] += $service['amount'];
                        } else if ($service->date <= $period['end']) {
                            $payments['service'][$period['start']] += $service['amount'];
                            $payments['total'][$period['start']] += $service['amount'];

                            $payments['details']['service'][$service->organization->name][$period['start']] += $service['amount'];
                        }
                    } else {
                        if ($service->date >= $period['start'] && $service->date <= $period['end']) {
                            $payments['service'][$period['start']] += $service['amount'];
                            $payments['total'][$period['start']] += $service['amount'];

                            $payments['details']['service'][$service->organization->name][$period['start']] += $service['amount'];
                        }
                    }
                }
            }
        }

        if (! isset($info[$objectId])) {
            return $payments;
        }

        $contractors = $info[$objectId]['contractors'];
        $providersFix = $info[$objectId]['providers_fix'];
        $providersFloat = $info[$objectId]['providers_float'];
        $service = $info[$objectId]['service'];

        $USDExchangeRate = $this->currencyExchangeService->getExchangeRate(Carbon::now()->format('Y-m-d'), 'USD');
        $EURExchangeRate = $this->currencyExchangeService->getExchangeRate(Carbon::now()->format('Y-m-d'), 'EUR');

        foreach ($periods as $index => $period) {

            foreach ($contractors as $contractor) {
                $date = Carbon::parse($contractor['date'])->format('Y-m-d');

                if (! isset($payments['details']['contractors'][$contractor['organization']][$period['start']])) {
                    $payments['details']['contractors'][$contractor['organization']][$period['start']] = 0;
                }

                if (! isset($payments['details']['contractors'][$contractor['organization']]['no_paid'])) {
                    $payments['details']['contractors'][$contractor['organization']]['no_paid'] = 0;
                }

                $amount = $contractor['amount'];

                if ($contractor['currency'] === 'USD') {
                    $amount = $amount * ($USDExchangeRate->rate ?? 0);
                } elseif ($contractor['currency'] === 'EUR') {
                    $amount = $amount * ($EURExchangeRate->rate ?? 0);
                }

                if ($index === 0) {
                    if ($date < $period['start']) {
                        $payments['contractors']['no_paid'] += $amount;
                        $payments['total']['no_paid'] += $amount;

                        $payments['details']['contractors'][$contractor['organization']]['no_paid'] += $amount;
                    } else if ($date <= $period['end']) {
                        $payments['contractors'][$period['start']] += $amount;
                        $payments['total'][$period['start']] += $amount;

                        $payments['details']['contractors'][$contractor['organization']][$period['start']] += $amount;
                    }
                } else {
                    if ($date >= $period['start'] && $date <= $period['end']) {
                        $payments['contractors'][$period['start']] += $amount;
                        $payments['total'][$period['start']] += $amount;

                        $payments['details']['contractors'][$contractor['organization']][$period['start']] += $amount;
                    }
                }
            }

            foreach ($providersFix as $fix) {
                $date = Carbon::parse($fix['date'])->format('Y-m-d');

                if (! isset($payments['details']['providers_fix'][$fix['organization']][$period['start']])) {
                    $payments['details']['providers_fix'][$fix['organization']][$period['start']] = 0;
                }

                if (! isset($payments['details']['providers_fix'][$fix['organization']]['no_paid'])) {
                    $payments['details']['providers_fix'][$fix['organization']]['no_paid'] = 0;
                }

                $amount = $fix['amount'];

                if ($fix['currency'] === 'USD') {
                    $amount = $amount * ($USDExchangeRate->rate ?? 0);
                } elseif ($fix['currency'] === 'EUR') {
                    $amount = $amount * ($EURExchangeRate->rate ?? 0);
                }

                if ($index === 0) {
                    if ($date < $period['start']) {
                        $payments['providers_fix']['no_paid'] += $amount;
                        $payments['total']['no_paid'] += $amount;

                        $payments['details']['providers_fix'][$fix['organization']]['no_paid'] += $amount;
                    } else if ($date <= $period['end']) {
                        $payments['providers_fix'][$period['start']] += $amount;
                        $payments['total'][$period['start']] += $amount;

                        $payments['details']['providers_fix'][$fix['organization']][$period['start']] += $amount;
                    }
                } else {
                    if ($date >= $period['start'] && $date <= $period['end']) {
                        $payments['providers_fix'][$period['start']] += $amount;
                        $payments['total'][$period['start']] += $amount;

                        $payments['details']['providers_fix'][$fix['organization']][$period['start']] += $amount;
                    }
                }
            }

            foreach ($providersFloat as $float) {
                $date = Carbon::parse($float['date'])->format('Y-m-d');

                if (! isset($payments['details']['providers_float'][$float['organization']][$period['start']])) {
                    $payments['details']['providers_float'][$float['organization']][$period['start']] = 0;
                }

                if (! isset($payments['details']['providers_float'][$float['organization']]['no_paid'])) {
                    $payments['details']['providers_float'][$float['organization']]['no_paid'] = 0;
                }

                $amount = $float['amount'];

                if ($float['currency'] === 'USD') {
                    $amount = $amount * ($USDExchangeRate->rate ?? 0);
                } elseif ($float['currency'] === 'EUR') {
                    $amount = $amount * ($EURExchangeRate->rate ?? 0);
                }

                if ($index === 0) {
                    if ($date < $period['start']) {
                        $payments['providers_float']['no_paid'] += $amount;
                        $payments['total']['no_paid'] += $amount;

                        $payments['details']['providers_float'][$float['organization']]['no_paid'] += $amount;
                    } else if ($date <= $period['end']) {
                        $payments['providers_float'][$period['start']] += $amount;
                        $payments['total'][$period['start']] += $amount;

                        $payments['details']['providers_float'][$float['organization']][$period['start']] += $amount;
                    }
                } else {
                    if ($date >= $period['start'] && $date <= $period['end']) {
                        $payments['providers_float'][$period['start']] += $amount;
                        $payments['total'][$period['start']] += $amount;

                        $payments['details']['providers_float'][$float['organization']][$period['start']] += $amount;
                    }
                }
            }

            foreach ($service as $ser) {
                $date = Carbon::parse($ser['date'])->format('Y-m-d');

                if (! isset($payments['details']['service'][$ser['organization']][$period['start']])) {
                    $payments['details']['service'][$ser['organization']][$period['start']] = 0;
                }

                if (! isset($payments['details']['service'][$ser['organization']]['no_paid'])) {
                    $payments['details']['service'][$ser['organization']]['no_paid'] = 0;
                }

                $amount = $ser['amount'];

                if ($ser['currency'] === 'USD') {
                    $amount = $amount * ($USDExchangeRate->rate ?? 0);
                } elseif ($ser['currency'] === 'EUR') {
                    $amount = $amount * ($EURExchangeRate->rate ?? 0);
                }

                if ($index === 0) {
                    if ($date < $period['start']) {
                        $payments['service']['no_paid'] += $amount;
                        $payments['total']['no_paid'] += $amount;

                        $payments['details']['service'][$ser['organization']]['no_paid'] += $amount;
                    } else if ($date <= $period['end']) {
                        $payments['service'][$period['start']] += $amount;
                        $payments['total'][$period['start']] += $amount;

                        $payments['details']['service'][$ser['organization']][$period['start']] += $amount;
                    }
                } else {
                    if ($date >= $period['start'] && $date <= $period['end']) {
                        $payments['service'][$period['start']] += $amount;
                        $payments['total'][$period['start']] += $amount;

                        $payments['details']['service'][$ser['organization']][$period['start']] += $amount;
                    }
                }
            }
        }

        return $payments;
    }

    public function getCFPaymentsForAll(array $periods): array
    {
        $payments = [
            'total' => [
                'all' => [],
                'contractors' => [],
                'providers_fix' => [],
                'providers_float' => [],
                'service' => [],
            ],
            'objects' => [],
            'objects_details' => [],
        ];

        $payments['total']['all']['no_paid'] = 0;
        $payments['total']['contractors']['no_paid'] = 0;
        $payments['total']['providers_fix']['no_paid'] = 0;
        $payments['total']['providers_float']['no_paid'] = 0;
        $payments['total']['service']['no_paid'] = 0;

        foreach ($periods as $period) {
            $payments['total']['all'][$period['start']] = 0;
            $payments['total']['contractors'][$period['start']] = 0;
            $payments['total']['providers_fix'][$period['start']] = 0;
            $payments['total']['providers_float'][$period['start']] = 0;
            $payments['total']['service'][$period['start']] = 0;
        }

        $info = Cache::get('cash_flow_1c_data', []);

        $USDExchangeRate = $this->currencyExchangeService->getExchangeRate(Carbon::now()->format('Y-m-d'), 'USD');
        $EURExchangeRate = $this->currencyExchangeService->getExchangeRate(Carbon::now()->format('Y-m-d'), 'EUR');

        foreach ($info as $objectId => $value) {
            $payments['objects_details'][$objectId] = [
                'contractors' => [],
                'providers' => [],
                'providers_fix' => [],
                'providers_float' => [],
                'service' => [],
            ];

            $payments['objects'][$objectId]['no_paid'] = [
                'total' => 0,
                'contractors' => 0,
                'providers_fix' => 0,
                'providers_float' => 0,
                'service' => 0,
            ];

            foreach ($periods as $index => $period) {
                $payments['objects'][$objectId][$period['start']] = [
                    'total' => 0,
                    'contractors' => 0,
                    'providers_fix' => 0,
                    'providers_float' => 0,
                    'service' => 0,
                ];

                foreach ($value['contractors'] as $contractor) {
                    $date = Carbon::parse($contractor['date'])->format('Y-m-d');

                    if (! isset($payments['objects_details'][$objectId]['contractors'][$contractor['organization']][$period['start']])) {
                        $payments['objects_details'][$objectId]['contractors'][$contractor['organization']][$period['start']] = 0;
                    }

                    if (! isset($payments['objects_details'][$objectId]['contractors'][$contractor['organization']]['no_paid'])) {
                        $payments['objects_details'][$objectId]['contractors'][$contractor['organization']]['no_paid'] = 0;
                    }

                    $amount = $contractor['amount'];

                    if ($contractor['currency'] === 'USD') {
                        $amount = $amount * ($USDExchangeRate->rate ?? 0);
                    } elseif ($contractor['currency'] === 'EUR') {
                        $amount = $amount * ($EURExchangeRate->rate ?? 0);
                    }

                    if ($index === 0) {
                        if ($date < $period['start']) {
                            $payments['objects'][$objectId]['no_paid']['contractors'] += $amount;
                            $payments['objects'][$objectId]['no_paid']['total'] += $amount;
                            $payments['total']['all']['no_paid'] += $amount;
                            $payments['total']['contractors']['no_paid'] += $amount;

                            $payments['objects_details'][$objectId]['contractors'][$contractor['organization']]['no_paid'] += $amount;
                        } else if ($date <= $period['end']) {
                            $payments['objects'][$objectId][$period['start']]['contractors'] += $amount;
                            $payments['objects'][$objectId][$period['start']]['total'] += $amount;
                            $payments['total']['all'][$period['start']] += $amount;
                            $payments['total']['contractors'][$period['start']] += $amount;

                            $payments['objects_details'][$objectId]['contractors'][$contractor['organization']][$period['start']] += $amount;
                            }
                    } else {
                        if ($date >= $period['start'] && $date <= $period['end']) {
                            $payments['objects'][$objectId][$period['start']]['contractors'] += $amount;
                            $payments['objects'][$objectId][$period['start']]['total'] += $amount;
                            $payments['total']['all'][$period['start']] += $amount;
                            $payments['total']['contractors'][$period['start']] += $amount;

                            $payments['objects_details'][$objectId]['contractors'][$contractor['organization']][$period['start']] += $amount;
                        }
                    }
                }

                foreach ($value['providers_fix'] as $fix) {
                    $date = Carbon::parse($fix['date'])->format('Y-m-d');

                    if (! isset($payments['objects_details'][$objectId]['providers'][$fix['organization']][$period['start']])) {
                        $payments['objects_details'][$objectId]['providers'][$fix['organization']][$period['start']] = 0;
                    }

                    if (! isset($payments['objects_details'][$objectId]['providers_fix'][$fix['organization']][$period['start']])) {
                        $payments['objects_details'][$objectId]['providers_fix'][$fix['organization']][$period['start']] = 0;
                    }

                    if (! isset($payments['objects_details'][$objectId]['providers'][$fix['organization']]['no_paid'])) {
                        $payments['objects_details'][$objectId]['providers'][$fix['organization']]['no_paid'] = 0;
                    }

                    if (! isset($payments['objects_details'][$objectId]['providers_fix'][$fix['organization']]['no_paid'])) {
                        $payments['objects_details'][$objectId]['providers_fix'][$fix['organization']]['no_paid'] = 0;
                    }

                    $amount = $fix['amount'];

                    if ($fix['currency'] === 'USD') {
                        $amount = $amount * ($USDExchangeRate->rate ?? 0);
                    } elseif ($fix['currency'] === 'EUR') {
                        $amount = $amount * ($EURExchangeRate->rate ?? 0);
                    }

                    if ($index === 0) {
                        if ($date < $period['start']) {
                            $payments['objects'][$objectId]['no_paid']['providers_fix'] += $amount;
                            $payments['objects'][$objectId]['no_paid']['total'] += $amount;
                            $payments['total']['all']['no_paid'] += $amount;
                            $payments['total']['providers_fix']['no_paid'] += $amount;

                            $payments['objects_details'][$objectId]['providers'][$fix['organization']]['no_paid'] += $amount;
                            $payments['objects_details'][$objectId]['providers_fix'][$fix['organization']]['no_paid'] += $amount;
                        } else if ($date <= $period['end']) {
                            $payments['objects'][$objectId][$period['start']]['providers_fix'] += $amount;
                            $payments['objects'][$objectId][$period['start']]['total'] += $amount;
                            $payments['total']['all'][$period['start']] += $amount;
                            $payments['total']['providers_fix'][$period['start']] += $amount;

                            $payments['objects_details'][$objectId]['providers'][$fix['organization']][$period['start']] += $amount;
                            $payments['objects_details'][$objectId]['providers_fix'][$fix['organization']][$period['start']] += $amount;
                        }
                    } else {
                        if ($date >= $period['start'] && $date <= $period['end']) {
                            $payments['objects'][$objectId][$period['start']]['providers_fix'] += $amount;
                            $payments['objects'][$objectId][$period['start']]['total'] += $amount;
                            $payments['total']['all'][$period['start']] += $amount;
                            $payments['total']['providers_fix'][$period['start']] += $amount;

                            $payments['objects_details'][$objectId]['providers'][$fix['organization']][$period['start']] += $amount;
                            $payments['objects_details'][$objectId]['providers_fix'][$fix['organization']][$period['start']] += $amount;
                        }
                    }
                }

                foreach ($value['providers_float'] as $float) {
                    $date = Carbon::parse($float['date'])->format('Y-m-d');

                    if (! isset($payments['objects_details'][$objectId]['providers'][$float['organization']][$period['start']])) {
                        $payments['objects_details'][$objectId]['providers'][$float['organization']][$period['start']] = 0;
                    }

                    if (! isset($payments['objects_details'][$objectId]['providers_float'][$float['organization']][$period['start']])) {
                        $payments['objects_details'][$objectId]['providers_float'][$float['organization']][$period['start']] = 0;
                    }

                    if (! isset($payments['objects_details'][$objectId]['providers'][$float['organization']]['no_paid'])) {
                        $payments['objects_details'][$objectId]['providers'][$float['organization']]['no_paid'] = 0;
                    }

                    if (! isset($payments['objects_details'][$objectId]['providers_float'][$float['organization']]['no_paid'])) {
                        $payments['objects_details'][$objectId]['providers_float'][$float['organization']]['no_paid'] = 0;
                    }

                    $amount = $float['amount'];

                    if ($float['currency'] === 'USD') {
                        $amount = $amount * ($USDExchangeRate->rate ?? 0);
                    } elseif ($float['currency'] === 'EUR') {
                        $amount = $amount * ($EURExchangeRate->rate ?? 0);
                    }

                    if ($index === 0) {
                        if ($date < $period['start']) {
                            $payments['objects'][$objectId]['no_paid']['providers_float'] += $amount;
                            $payments['objects'][$objectId]['no_paid']['total'] += $amount;
                            $payments['total']['all']['no_paid'] += $amount;
                            $payments['total']['providers_float']['no_paid'] += $amount;

                            $payments['objects_details'][$objectId]['providers'][$float['organization']]['no_paid'] += $amount;
                            $payments['objects_details'][$objectId]['providers_float'][$float['organization']]['no_paid'] += $amount;
                        } else if ($date <= $period['end']) {
                            $payments['objects'][$objectId][$period['start']]['providers_float'] += $amount;
                            $payments['objects'][$objectId][$period['start']]['total'] += $amount;
                            $payments['total']['all'][$period['start']] += $amount;
                            $payments['total']['providers_float'][$period['start']] += $amount;

                            $payments['objects_details'][$objectId]['providers'][$float['organization']][$period['start']] += $amount;
                            $payments['objects_details'][$objectId]['providers_float'][$float['organization']][$period['start']] += $amount;
                        }
                    } else {
                        if ($date >= $period['start'] && $date <= $period['end']) {
                            $payments['objects'][$objectId][$period['start']]['providers_float'] += $amount;
                            $payments['objects'][$objectId][$period['start']]['total'] += $amount;
                            $payments['total']['all'][$period['start']] += $amount;
                            $payments['total']['providers_float'][$period['start']] += $amount;

                            $payments['objects_details'][$objectId]['providers'][$float['organization']][$period['start']] += $amount;
                            $payments['objects_details'][$objectId]['providers_float'][$float['organization']][$period['start']] += $amount;
                        }
                    }
                }

                foreach ($value['service'] as $ser) {
                    $date = Carbon::parse($ser['date'])->format('Y-m-d');

                    if (! isset($payments['objects_details'][$objectId]['service'][$ser['organization']][$period['start']])) {
                        $payments['objects_details'][$objectId]['service'][$ser['organization']][$period['start']] = 0;
                    }

                    if (! isset($payments['objects_details'][$objectId]['service'][$ser['organization']]['no_paid'])) {
                        $payments['objects_details'][$objectId]['service'][$ser['organization']]['no_paid'] = 0;
                    }

                    $amount = $ser['amount'];

                    if ($ser['currency'] === 'USD') {
                        $amount = $amount * ($USDExchangeRate->rate ?? 0);
                    } elseif ($ser['currency'] === 'EUR') {
                        $amount = $amount * ($EURExchangeRate->rate ?? 0);
                    }

                    if ($index === 0) {
                        if ($date < $period['start']) {
                            $payments['objects'][$objectId]['no_paid']['service'] += $amount;
                            $payments['objects'][$objectId]['no_paid']['total'] += $amount;
                            $payments['total']['all']['no_paid'] += $amount;
                            $payments['total']['service']['no_paid'] += $amount;

                            $payments['objects_details'][$objectId]['service'][$ser['organization']]['no_paid'] += $amount;
                        } else if ($date <= $period['end']) {
                            $payments['objects'][$objectId][$period['start']]['service'] += $amount;
                            $payments['objects'][$objectId][$period['start']]['total'] += $amount;
                            $payments['total']['all'][$period['start']] += $amount;
                            $payments['total']['service'][$period['start']] += $amount;

                            $payments['objects_details'][$objectId]['service'][$ser['organization']][$period['start']] += $amount;
                        }
                    } else {
                        if ($date >= $period['start'] && $date <= $period['end']) {
                            $payments['objects'][$objectId][$period['start']]['service'] += $amount;
                            $payments['objects'][$objectId][$period['start']]['total'] += $amount;
                            $payments['total']['all'][$period['start']] += $amount;
                            $payments['total']['service'][$period['start']] += $amount;

                            $payments['objects_details'][$objectId]['service'][$ser['organization']][$period['start']] += $amount;
                        }
                    }
                }
            }
        }

        $manualPayments = CashFlowPayment::get();

        $contractors = $manualPayments->where('category_id', CashFlowPayment::CATEGORY_RAD);
        $providersFix = $manualPayments->where('category_id', CashFlowPayment::CATEGORY_MATERIAL_FIX);
        $providersFloat = $manualPayments->where('category_id', CashFlowPayment::CATEGORY_MATERIAL_FLOAT);
        $services = $manualPayments->where('category_id', CashFlowPayment::CATEGORY_SERVICE);

        foreach ($contractors as $contractor) {
            $objectId = $contractor->object_id;

            if (! isset($payments['objects_details'][$objectId])) {
                $payments['objects_details'][$objectId] = [
                    'contractors' => [],
                    'providers' => [],
                    'providers_fix' => [],
                    'providers_float' => [],
                    'service' => [],
                ];
            }

            foreach ($periods as $index => $period) {
                if (!isset($payments['objects'][$objectId][$period['start']])) {
                    $payments['objects'][$objectId][$period['start']] = [
                        'total' => 0,
                        'contractors' => 0,
                        'providers_fix' => 0,
                        'providers_float' => 0,
                        'service' => 0,
                    ];
                }

                if (! isset($payments['objects_details'][$objectId]['contractors'][$contractor->organization->name][$period['start']])) {
                    $payments['objects_details'][$objectId]['contractors'][$contractor->organization->name][$period['start']] = 0;
                }

                if (! isset($payments['objects_details'][$objectId]['contractors'][$contractor->organization->name]['no_paid'])) {
                    $payments['objects_details'][$objectId]['contractors'][$contractor->organization->name]['no_paid'] = 0;
                }

                if ($index === 0) {
                    if ($contractor->date < $period['start']) {
                        $payments['objects'][$objectId]['no_paid']['contractors'] += $contractor->amount;
                        $payments['objects'][$objectId]['no_paid']['total'] += $contractor->amount;
                        $payments['total']['all']['no_paid'] += $contractor->amount;
                        $payments['total']['contractors']['no_paid'] += $contractor->amount;

                        $payments['objects_details'][$objectId]['contractors'][$contractor->organization->name]['no_paid'] += $contractor->amount;
                    } else if ($contractor->date <= $period['end']) {
                        $payments['objects'][$objectId][$period['start']]['contractors'] += $contractor->amount;
                        $payments['objects'][$objectId][$period['start']]['total'] += $contractor->amount;
                        $payments['total']['all'][$period['start']] += $contractor->amount;
                        $payments['total']['contractors'][$period['start']] += $contractor->amount;

                        $payments['objects_details'][$objectId]['contractors'][$contractor->organization->name][$period['start']] += $contractor->amount;
                    }
                } else {
                    if ($contractor->date >= $period['start'] && $contractor->date <= $period['end']) {
                        $payments['objects'][$objectId][$period['start']]['contractors'] += $contractor->amount;
                        $payments['objects'][$objectId][$period['start']]['total'] += $contractor->amount;
                        $payments['total']['all'][$period['start']] += $contractor->amount;
                        $payments['total']['contractors'][$period['start']] += $contractor->amount;

                        $payments['objects_details'][$objectId]['contractors'][$contractor->organization->name][$period['start']] += $contractor->amount;
                    }
                }
            }
        }

        foreach ($providersFix as $contractor) {
            $objectId = $contractor->object_id;

            if (! isset($payments['objects_details'][$objectId])) {
                $payments['objects_details'][$objectId] = [
                    'contractors' => [],
                    'providers' => [],
                    'providers_fix' => [],
                    'providers_float' => [],
                    'service' => [],
                ];
            }

            foreach ($periods as $index => $period) {
                if (!isset($payments['objects'][$objectId][$period['start']])) {
                    $payments['objects'][$objectId][$period['start']] = [
                        'total' => 0,
                        'contractors' => 0,
                        'providers_fix' => 0,
                        'providers_float' => 0,
                        'service' => 0,
                    ];
                }

                if (! isset($payments['objects_details'][$objectId]['providers_fix'][$contractor->organization->name]['no_paid'])) {
                    $payments['objects_details'][$objectId]['providers_fix'][$contractor->organization->name]['no_paid'] = 0;
                }

                if (! isset($payments['objects_details'][$objectId]['providers_fix'][$contractor->organization->name]['no_paid'])) {
                    $payments['objects_details'][$objectId]['providers_fix'][$contractor->organization->name]['no_paid'] = 0;
                }

                if ($index === 0) {
                    if ($contractor->date < $period['start']) {
                        $payments['objects'][$objectId]['no_paid']['providers_fix'] += $contractor->amount;
                        $payments['objects'][$objectId]['no_paid']['total'] += $contractor->amount;
                        $payments['total']['all']['no_paid'] += $contractor->amount;
                        $payments['total']['providers_fix']['no_paid'] += $contractor->amount;

                        $payments['objects_details'][$objectId]['providers_fix'][$contractor->organization->name]['no_paid'] += $contractor->amount;
                    } else if ($contractor->date <= $period['end']) {
                        $payments['objects'][$objectId][$period['start']]['providers_fix'] += $contractor->amount;
                        $payments['objects'][$objectId][$period['start']]['total'] += $contractor->amount;
                        $payments['total']['all'][$period['start']] += $contractor->amount;
                        $payments['total']['providers_fix'][$period['start']] += $contractor->amount;

                        $payments['objects_details'][$objectId]['providers_fix'][$contractor->organization->name][$period['start']] += $contractor->amount;
                    }
                } else {
                    if ($contractor->date >= $period['start'] && $contractor->date <= $period['end']) {
                        $payments['objects'][$objectId][$period['start']]['providers_fix'] += $contractor->amount;
                        $payments['objects'][$objectId][$period['start']]['total'] += $contractor->amount;
                        $payments['total']['all'][$period['start']] += $contractor->amount;
                        $payments['total']['providers_fix'][$period['start']] += $contractor->amount;

                        $payments['objects_details'][$objectId]['providers_fix'][$contractor->organization->name][$period['start']] += $contractor->amount;
                    }
                }
            }
        }

        foreach ($providersFloat as $contractor) {
            $objectId = $contractor->object_id;

            if (! isset($payments['objects_details'][$objectId])) {
                $payments['objects_details'][$objectId] = [
                    'contractors' => [],
                    'providers' => [],
                    'providers_fix' => [],
                    'providers_float' => [],
                    'service' => [],
                ];
            }

            foreach ($periods as $index => $period) {
                if (!isset($payments['objects'][$objectId][$period['start']])) {
                    $payments['objects'][$objectId][$period['start']] = [
                        'total' => 0,
                        'contractors' => 0,
                        'providers_fix' => 0,
                        'providers_float' => 0,
                        'service' => 0,
                    ];
                }

                if (! isset($payments['objects_details'][$objectId]['providers_float'][$contractor->organization->name][$period['start']])) {
                    $payments['objects_details'][$objectId]['providers_float'][$contractor->organization->name][$period['start']] = 0;
                }

                if (! isset($payments['objects_details'][$objectId]['providers_float'][$contractor->organization->name]['no_paid'])) {
                    $payments['objects_details'][$objectId]['providers_float'][$contractor->organization->name]['no_paid'] = 0;
                }

                if ($index === 0) {
                    if ($contractor->date < $period['start']) {
                        $payments['objects'][$objectId]['no_paid']['providers_float'] += $contractor->amount;
                        $payments['objects'][$objectId]['no_paid']['total'] += $contractor->amount;
                        $payments['total']['all']['no_paid'] += $contractor->amount;
                        $payments['total']['providers_float']['no_paid'] += $contractor->amount;

                        $payments['objects_details'][$objectId]['providers_float'][$contractor->organization->name]['no_paid'] += $contractor->amount;
                    } else if ($contractor->date <= $period['end']) {
                        $payments['objects'][$objectId][$period['start']]['providers_float'] += $contractor->amount;
                        $payments['objects'][$objectId][$period['start']]['total'] += $contractor->amount;
                        $payments['total']['all'][$period['start']] += $contractor->amount;
                        $payments['total']['providers_float'][$period['start']] += $contractor->amount;

                        $payments['objects_details'][$objectId]['providers_float'][$contractor->organization->name][$period['start']] += $contractor->amount;
                    }
                } else {
                    if ($contractor->date >= $period['start'] && $contractor->date <= $period['end']) {
                        $payments['objects'][$objectId][$period['start']]['providers_float'] += $contractor->amount;
                        $payments['objects'][$objectId][$period['start']]['total'] += $contractor->amount;
                        $payments['total']['all'][$period['start']] += $contractor->amount;
                        $payments['total']['providers_float'][$period['start']] += $contractor->amount;

                        $payments['objects_details'][$objectId]['providers_float'][$contractor->organization->name][$period['start']] += $contractor->amount;
                    }
                }
            }
        }

        foreach ($services as $contractor) {
            $objectId = $contractor->object_id;

            if (! isset($payments['objects_details'][$objectId])) {
                $payments['objects_details'][$objectId] = [
                    'contractors' => [],
                    'providers' => [],
                    'providers_fix' => [],
                    'providers_float' => [],
                    'service' => [],
                ];
            }

            foreach ($periods as $index => $period) {
                if (!isset($payments['objects'][$objectId][$period['start']])) {
                    $payments['objects'][$objectId][$period['start']] = [
                        'total' => 0,
                        'contractors' => 0,
                        'providers_fix' => 0,
                        'providers_float' => 0,
                        'service' => 0,
                    ];
                }

                if (! isset($payments['objects_details'][$objectId]['service'][$contractor->organization->name][$period['start']])) {
                    $payments['objects_details'][$objectId]['service'][$contractor->organization->name][$period['start']] = 0;
                }

                if (! isset($payments['objects_details'][$objectId]['service'][$contractor->organization->name]['no_paid'])) {
                    $payments['objects_details'][$objectId]['service'][$contractor->organization->name]['no_paid'] = 0;
                }

                if ($index === 0) {
                    if ($contractor->date < $period['start']) {
                        $payments['objects'][$objectId]['no_paid']['service'] += $contractor->amount;
                        $payments['objects'][$objectId]['no_paid']['total'] += $contractor->amount;
                        $payments['total']['all']['no_paid'] += $contractor->amount;
                        $payments['total']['service']['no_paid'] += $contractor->amount;

                        $payments['objects_details'][$objectId]['service'][$contractor->organization->name]['no_paid'] += $contractor->amount;
                    } else if ($contractor->date <= $period['end']) {
                        $payments['objects'][$objectId][$period['start']]['service'] += $contractor->amount;
                        $payments['objects'][$objectId][$period['start']]['total'] += $contractor->amount;
                        $payments['total']['all'][$period['start']] += $contractor->amount;
                        $payments['total']['service'][$period['start']] += $contractor->amount;

                        $payments['objects_details'][$objectId]['service'][$contractor->organization->name][$period['start']] += $contractor->amount;
                    }
                } else {
                    if ($contractor->date >= $period['start'] && $contractor->date <= $period['end']) {
                        $payments['objects'][$objectId][$period['start']]['service'] += $contractor->amount;
                        $payments['objects'][$objectId][$period['start']]['total'] += $contractor->amount;
                        $payments['total']['all'][$period['start']] += $contractor->amount;
                        $payments['total']['service'][$period['start']] += $contractor->amount;

                        $payments['objects_details'][$objectId]['service'][$contractor->organization->name][$period['start']] += $contractor->amount;
                    }
                }
            }
        }

        return $payments;
    }
}
