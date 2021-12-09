<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\BankGuarantee;
use App\Models\Status;
use Carbon\Carbon;

class BankGuaranteeService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function createBankGuarantee(array $requestData): void
    {
        $guarantee = BankGuarantee::create([
            'company_id' => $requestData['company_id'],
            'bank_id' => $requestData['bank_id'],
            'object_id' => $requestData['object_id'],
            'start_date' => $requestData['start_date'],
            'end_date' => $requestData['end_date'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'start_date_deposit' => $requestData['start_date_deposit'],
            'end_date_deposit' => $requestData['end_date_deposit'],
            'amount_deposit' => $this->sanitizer->set($requestData['amount_deposit'])->toAmount()->get(),
            'target' => $this->sanitizer->set($requestData['target'])->get(),
            'status_id' => Status::STATUS_ACTIVE,
        ]);

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                $guarantee->addMedia($file)->toMediaCollection();
            }
        }
    }

    public function updateBankGuarantee(BankGuarantee $guarantee, array $requestData): void
    {
        $guarantee->update([
            'company_id' => $requestData['company_id'],
            'bank_id' => $requestData['bank_id'],
            'object_id' => $requestData['object_id'],
            'start_date' => $requestData['start_date'],
            'end_date' => $requestData['end_date'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'start_date_deposit' => $requestData['start_date_deposit'],
            'end_date_deposit' => $requestData['end_date_deposit'],
            'amount_deposit' => $this->sanitizer->set($requestData['amount_deposit'])->toAmount()->get(),
            'target' => $this->sanitizer->set($requestData['target'])->get(),
            'status_id' => $requestData['status_id']
        ]);
    }

    public function destroyBankGuarantee(BankGuarantee $guarantee): void
    {
        $guarantee->delete();
    }
}
