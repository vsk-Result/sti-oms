<?php

namespace App\Services\Contract;

use App\Helpers\Sanitizer;
use App\Models\Contract\Act;
use App\Models\Contract\ActPayment;
use App\Models\Contract\Contract;
use App\Models\Status;

class ActService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function createAct(array $requestData): void
    {
        $contract = Contract::find($requestData['contract_id']);
        $act = Act::create([
            'contract_id' => $contract->id,
            'company_id' => $contract->company_id,
            'object_id' => $contract->object_id,
            'date' => $requestData['date'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'amount_avans' => $this->sanitizer->set($requestData['amount_avans'])->toAmount()->get(),
            'amount_deposit' => $this->sanitizer->set($requestData['amount_deposit'])->toAmount()->get(),
            'description' => $this->sanitizer->set($requestData['description'])->get(),
            'status_id' => Status::STATUS_ACTIVE,
        ]);

        $act->update([
            'amount_need_paid' => $act->amount - $act->amount_avans - $act->amount_deposit
        ]);

        if (! empty($requestData['payments_date'])) {
            foreach ($requestData['payments_date'] as $index => $paymentDate) {
                $paymentAmount = (float) $requestData['payments_amount'][$index];
                if ($paymentAmount > 0) {
                    ActPayment::create([
                        'contract_id' => $contract->id,
                        'act_id' => $act->id,
                        'company_id' => $act->company_id,
                        'object_id' => $act->object_id,
                        'date' => $paymentDate,
                        'amount' => $this->sanitizer->set($paymentAmount)->toAmount()->get(),
                        'status_id' => Status::STATUS_ACTIVE,
                    ]);
                }
            }
        }
    }

    public function updateAct(Act $act, array $requestData): void
    {
        $contract = Contract::find($requestData['contract_id']);
        $act->update([
            'contract_id' => $contract->id,
            'company_id' => $contract->company_id,
            'object_id' => $contract->object_id,
            'date' => $requestData['date'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'amount_avans' => $this->sanitizer->set($requestData['amount_avans'])->toAmount()->get(),
            'amount_deposit' => $this->sanitizer->set($requestData['amount_deposit'])->toAmount()->get(),
            'description' => $this->sanitizer->set($requestData['description'])->get(),
            'status_id' => $requestData['status_id'],
        ]);

        $act->update([
            'amount_need_paid' => $act->amount - $act->amount_avans - $act->amount_deposit
        ]);

        if (! empty($requestData['isset_payments_date'])) {
            foreach ($requestData['isset_payments_date'] as $paymentId => $paymentDate) {
                $payment = ActPayment::find($paymentId);
                $paymentAmount = (float) $requestData['isset_payments_amount'][$paymentId];

                if ($paymentAmount > 0) {
                    $payment->update([
                        'date' => $paymentDate,
                        'amount' => $this->sanitizer->set($paymentAmount)->toAmount()->get(),
                    ]);
                } else {
                    $payment->delete();
                }
            }
        }

        if (! empty($requestData['payments_date'])) {
            foreach ($requestData['payments_date'] as $index => $paymentDate) {
                $paymentAmount = (float) $requestData['payments_amount'][$index];
                if ($paymentAmount > 0) {
                    ActPayment::create([
                        'contract_id' => $contract->id,
                        'act_id' => $act->id,
                        'company_id' => $act->company_id,
                        'object_id' => $act->object_id,
                        'date' => $paymentDate,
                        'amount' => $this->sanitizer->set($paymentAmount)->toAmount()->get(),
                        'status_id' => Status::STATUS_ACTIVE,
                    ]);
                }
            }
        }
    }

    public function destroyAct(Act $act): void
    {
        $act->payments()->delete();
        $act->delete();
    }
}
