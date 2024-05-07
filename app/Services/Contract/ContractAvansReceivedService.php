<?php

namespace App\Services\Contract;

use App\Helpers\Sanitizer;
use App\Models\Contract\Contract;
use App\Models\Contract\ContractAvans;
use App\Models\Contract\ContractReceivedAvans;
use App\Models\Status;
use App\Services\CurrencyExchangeRateService;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Currency;

class ContractAvansReceivedService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function createReceivedAvans(Contract $contract, array $requestData): ContractReceivedAvans
    {
        $avans = ContractReceivedAvans::create([
            'contract_id' => $contract->id,
            'company_id' => $contract->company_id,
            'object_id' => $contract->object_id,
            'date' => $requestData['date'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'description' => $requestData['description'],
            'status_id' => Status::STATUS_ACTIVE,
            'currency' => $contract->currency,
            'currency_rate' => 1,
        ]);

        return $avans;
    }
}
