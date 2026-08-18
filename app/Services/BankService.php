<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\Bank;
use App\Models\Status;

class BankService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function createBank(array $requestData): void
    {
        Bank::create([
            'name' => $this->sanitizer->set($requestData['name'])->get(),
            'logo' => '/images/banks/vtb.png',
            'balance_amount' => $this->sanitizer->set($requestData['balance_amount'])->toAmount()->get(),
            'status_id' => Status::STATUS_ACTIVE
        ]);
    }

    public function updateBank(Bank $bank, array $requestData): void
    {
        $bank->update([
            'name' => $this->sanitizer->set($requestData['name'])->get(),
            'balance_amount' => $this->sanitizer->set($requestData['balance_amount'])->toAmount()->get(),
            'status_id' => $requestData['status_id']
        ]);
    }

    public function destroyBank(Bank $bank): void
    {
        $bank->delete();
    }
}
