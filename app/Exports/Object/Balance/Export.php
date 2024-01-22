<?php

namespace App\Exports\Object\Balance;

use App\Models\Object\BObject;
use App\Services\Contract\ContractService;
use App\Services\CurrencyExchangeRateService;
use App\Services\PivotObjectDebtService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Object\Balance\Sheets\BalanceSheet;

class Export  implements WithMultipleSheets
{
    private BObject $object;
    private PivotObjectDebtService $pivotObjectDebtService;
    private CurrencyExchangeRateService $currencyExchangeRateService;
    private ContractService $contractService;

    public function __construct(
        BObject $object,
        PivotObjectDebtService $pivotObjectDebtService,
        CurrencyExchangeRateService $currencyExchangeRateService,
        ContractService $contractService,
    ) {
        $this->object = $object;
        $this->pivotObjectDebtService = $pivotObjectDebtService;
        $this->currencyExchangeRateService = $currencyExchangeRateService;
        $this->contractService = $contractService;
    }

    public function sheets(): array
    {
        return [
            new BalanceSheet(
                $this->object,
                $this->pivotObjectDebtService,
                $this->currencyExchangeRateService,
                $this->contractService
            ),
        ];
    }
}
