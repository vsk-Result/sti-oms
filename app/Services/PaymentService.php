<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\Payment;
use App\Models\Status;

class PaymentService
{
    private Sanitizer $sanitizer;
    private array $opsteList;
    private array $radList;
    private array $materialList;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function loadCategoriesList(): void
    {
        $this->opsteList = include base_path('resources/categories/opste.php');
        $this->radList = include base_path('resources/categories/rad.php');
        $this->materialList = include base_path('resources/categories/material.php');
    }

    public function createPayment(array $requestData): Payment
    {
        if (array_key_exists('base_payment_id', $requestData)) {
            $basePayment = Payment::find($requestData['base_payment_id']);
            $requestData = $basePayment->attributesToArray();
        }

        $payment = Payment::create([
            'statement_id' => $requestData['statement_id'],
            'company_id' => $requestData['company_id'],
            'bank_id' => $requestData['bank_id'],
            'object_id' => $requestData['object_id'],
            'object_worktype_id' => $requestData['object_worktype_id'],
            'organization_sender_id' => $requestData['organization_sender_id'],
            'organization_receiver_id' => $requestData['organization_receiver_id'],
            'type_id' => $requestData['type_id'],
            'payment_type_id' => $requestData['payment_type_id'],
            'category' => $requestData['category'],
            'code' => $this->sanitizer->set($requestData['code'])->toCode()->get(),
            'description' => $this->sanitizer->set($requestData['description'])->upperCaseFirstWord()->get(),
            'date' => $requestData['date'],
            'amount' => $requestData['amount'],
            'amount_without_nds' => $requestData['amount_without_nds'],
            'is_need_split' => $requestData['is_need_split'],
            'status_id' => $requestData['status_id']
        ]);

        return $payment;
    }

    public function updatePayment(Payment $payment, array $requestData): Payment
    {
        if (array_key_exists('amount', $requestData)) {
            $description = array_key_exists('description', $requestData) ? $requestData['description'] : $payment->description;
            $requestData['amount'] = $this->sanitizer->set($requestData['amount'])->toAmount()->get();
            $nds = $this->checkHasNDSFromDescription($description) ? round($requestData['amount'] / 6, 2) : 0;
            $requestData['amount_without_nds'] = $requestData['amount'] - $nds;
        } elseif (array_key_exists('object_id', $requestData)) {
            if (str_contains($requestData['object_id'], '::')) {
                $objectData = explode('::', $requestData['object_id']);
                $requestData['type_id'] = Payment::TYPE_OBJECT;
                $requestData['object_id'] = (int) $objectData[0];
                $requestData['object_worktype_id'] = (int) $objectData[1];
            } else {
                $requestData['type_id'] = (int) $requestData['object_id'];
                $requestData['object_id'] = null;
                $requestData['object_worktype_id'] = null;
            }
        } elseif (array_key_exists('code', $requestData)) {
            $requestData['code'] = $this->sanitizer->set($requestData['code'])->toCode()->get();
        }

        $payment->update($requestData);

        if (
            $payment->type_id !== Payment::TYPE_NONE
            && ! empty($payment->code)
            && ! empty($payment->description)
            && ! is_null($payment->category)
            && ! is_null($payment->amount)
        ) {
            if (! $payment->isActive()) {
                $payment->setActive();
            }
        } else {
            if (! $payment->isBlocked()) {
                $payment->setBlocked();
            }
        }

        return $payment;
    }

    public function destroyPayment(Payment $payment): Payment
    {
        $payment->delete();

        return $payment;
    }

    public function checkHasNDSFromDescription(string $description): bool
    {
        $description = $this->sanitizer->set($description)->noSpaces()->lowerCase()->get();

        if (
            ! str_contains($description, 'вт.ч.ндс')
            && ! str_contains($description, 'втомчислендс')
        ) {
            return false;
        }

        return true;
    }

    public function checkIsNeedSplitFromDescription(string $description): bool
    {
        $description = $this->sanitizer->set($description)->lowerCase()->get();

        if (
            str_contains($description, 'перечисление заработной платы')
            || str_contains($description, 'перечисление отпускных согласно реестру')
            || str_contains($description, 'оплата больничного')
        ) {
            return true;
        }

        return false;
    }

    public function findCategoryFromDescription(string $description): null|string
    {
        $description = $this->sanitizer->set($description)->lowerCase()->get();

        foreach ($this->opsteList as $opsteValue) {
            if (str_contains($description, $opsteValue)) {
                return Payment::CATEGORY_OPSTE;
            }
        }

        foreach ($this->radList as $radValue) {
            if (str_contains($description, $radValue)) {
                return Payment::CATEGORY_RAD;
            }
        }

        foreach ($this->materialList as $materialValue) {
            if (str_contains($description, $materialValue)) {
                return Payment::CATEGORY_MATERIAL;
            }
        }

        return null;
    }
}
