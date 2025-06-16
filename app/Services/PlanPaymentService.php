<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\CashFlow\Notification;
use App\Models\CashFlow\PlanPayment;
use App\Models\Object\BObject;
use App\Models\Status;
use App\Services\CashFlow\NotificationService;

class PlanPaymentService
{
    private Sanitizer $sanitizer;
    private NotificationService $notificationService;
    private PlanPaymentEntryService $paymentEntryService;

    public function __construct(Sanitizer $sanitizer, NotificationService $notificationService, PlanPaymentEntryService $paymentEntryService)
    {
        $this->sanitizer = $sanitizer;
        $this->notificationService = $notificationService;
        $this->paymentEntryService = $paymentEntryService;
    }

    public function createPlanPayment(array $requestData): PlanPayment
    {
        if (! isset($requestData['without_notify'])) {
            $this->notificationService->createNotification(
                Notification::TYPE_PAYMENT,
                Notification::EVENT_TYPE_CREATE,
                'Новая запись расхода "' . $requestData['name'] . '"'
            );
        }

        return PlanPayment::create([
            'group_id' => $requestData['group_id'] ?? null,
            'object_id' => $requestData['object_id'] ?? null,
            'from_tax_plan' => $requestData['from_tax_plan'] ?? false,
            'name' => $this->sanitizer->set($requestData['name'])->upperCaseFirstWord()->get(),
            'status_id' => Status::STATUS_ACTIVE,
        ]);
    }

    public function updatePlanPayment(array $requestData): PlanPayment
    {
        $payment = $this->findPlanPayment($requestData['payment_id']);
        $objectId = $payment->object_id;

        if (isset($requestData['object_id'])) {
            $objectId = $requestData['object_id'] === 'null' ? null : $requestData['object_id'];

            if ($payment->object_id != $objectId) {
                $this->notificationService->createNotification(
                    Notification::TYPE_PAYMENT,
                    Notification::EVENT_TYPE_UPDATE,
                    'Объект расхода "' . $payment->name . '" изменился с "' . ($payment->object->code ?? '-') . '" на "' . (is_null($objectId) ? '-' : BObject::find($objectId)->code) . '"'
                );
            }
        }

        if (isset($requestData['name'])) {
            $name = $this->sanitizer->set($requestData['name'])->upperCaseFirstWord()->get();
            if ($payment->name !== $name) {
                $this->notificationService->createNotification(
                    Notification::TYPE_PAYMENT,
                    Notification::EVENT_TYPE_UPDATE,
                    'Расход "' . $payment->name . '" переименован в "' . $name . '"'
                );
            }
        }

        $payment->update([
            'group_id' => $requestData['group_id'] ?? $payment->group_id,
            'object_id' => $objectId,
            'name' => isset($requestData['name'])
                ? $this->sanitizer->set($requestData['name'])->upperCaseFirstWord()->get()
                : $payment->name,
        ]);

        return $payment;
    }

    public function destroyPlanPayment(array $requestData): void
    {
        $payment = $this->findPlanPayment($requestData['payment_id']);

        if (! $payment) {
            return;
        }

        if (! isset($requestData['without_notify'])) {
            $this->notificationService->createNotification(
                Notification::TYPE_PAYMENT,
                Notification::EVENT_TYPE_DELETE,
                'Расход "' . $payment->name . '" удален полностью'
            );
        }

        $payment->entries()->delete();
        $payment->delete();
    }

    public function destroyPlanPaymentFromTaxPLan(): void
    {
        $payments = PlanPayment::where('from_tax_plan', true)->get();

        foreach ($payments as $payment) {
            $this->destroyPlanPayment(['payment_id' => $payment->id, 'without_notify' => true]);
        }
    }

    public function findPlanPayment($id): PlanPayment | null
    {
        return PlanPayment::find($id);
    }

    public function updatePaymentsFromTaxPlan(array $paymentsInfo, array $periods): void
    {
        $groupedPayments = [];

        foreach ($paymentsInfo as $info) {
            $name = $info['name'];
            $groupId = $info['group_id'];

            if (!isset($groupedPayments[$groupId])) {
                $groupedPayments[$groupId] = [];
            }

            foreach ($periods as $period) {
                if ($info['date'] >= $period['start'] && $info['date'] <= $period['end']) {
                    if (!isset($groupedPayments[$groupId][$name][$period['start']])) {
                        $groupedPayments[$groupId][$name][$period['start']] = 0;
                    }

                    $groupedPayments[$groupId][$name][$period['start']] += $info['amount'];
                }
            }
        }

        $this->destroyPlanPaymentFromTaxPLan();

        foreach ($groupedPayments as $groupId => $payments) {
            foreach ($payments as $paymentName => $dates) {
                $planPayment = $this->createPlanPayment([
                    'group_id' => $groupId,
                    'name' => $paymentName,
                    'from_tax_plan' => true,
                    'without_notify' => true
                ]);

                foreach ($dates as $date => $amount) {
                    $this->paymentEntryService->createPlanPaymentEntry([
                        'payment_id' => $planPayment->id,
                        'amount' => $amount,
                        'date' => $date,
                        'without_notify' => true
                    ]);
                }
            }
        }
    }
}
