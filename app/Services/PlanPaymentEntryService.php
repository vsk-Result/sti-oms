<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\CashFlow\Notification;
use App\Models\CashFlow\PlanPayment;
use App\Models\CashFlow\PlanPaymentEntry;
use App\Models\CurrencyExchangeRate;
use App\Models\Status;
use App\Services\CashFlow\NotificationService;
use Carbon\Carbon;

class PlanPaymentEntryService
{
    private Sanitizer $sanitizer;
    private NotificationService $notificationService;

    public function __construct(Sanitizer $sanitizer, NotificationService $notificationService)
    {
        $this->sanitizer = $sanitizer;
        $this->notificationService = $notificationService;
    }

    public function createOrUpdatePlanPaymentEntry(array $requestData, ?array $periods): PlanPaymentEntry
    {
        $entry = $this->findEntry($requestData['payment_id'], $requestData['date']);

        if ($entry) {
            return $this->updatePlanPaymentEntry($entry, $requestData);
        }

        $entry = $this->createPlanPaymentEntry($requestData);

        if ($periods) {
            if ($entry->date < $periods[0]['start']) {
                PlanPaymentEntry::where('id', '!=', $entry->id)->where('payment_id', $requestData['payment_id'])->where('date', '<', $periods[0]['start'])->delete();
            }
        }

        return $entry;
    }

    public function createPlanPaymentEntry(array $requestData): PlanPaymentEntry
    {
        $amount = isset($requestData['amount']) ? $this->sanitizer->set($requestData['amount'])->toAmount()->get() : 0;
        $period = Carbon::parse($requestData['date'])->startOfWeek()->format('d.m.Y') . ' - ' . Carbon::parse($requestData['date'])->endOfWeek()->format('d.m.Y');

        if (! isset($requestData['without_notify'])) {
            $this->notificationService->createNotification(
                Notification::TYPE_PAYMENT,
                Notification::EVENT_TYPE_CREATE,
                'Сумма расхода "' . PlanPayment::find($requestData['payment_id'])->name . '" изменилась с "0 ₽" на "' . CurrencyExchangeRate::format($amount, 'RUB') . '" за период "' . $period . '"'
            );
        }

        return PlanPaymentEntry::create([
            'payment_id' => $requestData['payment_id'],
            'date' => $requestData['date'],
            'amount' => -abs($amount),
            'status_id' => Status::STATUS_ACTIVE,
        ]);
    }

    public function updatePlanPaymentEntry(PlanPaymentEntry $entry, array $requestData): PlanPaymentEntry
    {
        $newAmount = $this->sanitizer->set($requestData['amount'])->toAmount()->get();
        $period = Carbon::parse($entry->date)->startOfWeek()->format('d.m.Y') . ' - ' . Carbon::parse($entry->date)->endOfWeek()->format('d.m.Y');

        $this->notificationService->createNotification(
            Notification::TYPE_PAYMENT,
            Notification::EVENT_TYPE_UPDATE,
            'Сумма расхода "' . $entry->planPayment->name . '" изменилась с "' . CurrencyExchangeRate::format($entry->amount, 'RUB') . '" на "' . CurrencyExchangeRate::format($newAmount, 'RUB') . '" за период "' . $period . '"'
        );

        $entry->update([
            'amount' => -abs($newAmount)
        ]);

        return $entry;
    }

    public function findEntry(int $paymentId, string $date): PlanPaymentEntry | null
    {
        return PlanPaymentEntry::where('payment_id', $paymentId)->where('date', $date)->first();
    }
}
