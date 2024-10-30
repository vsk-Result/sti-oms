<?php

namespace App\Exports\Object\Report\ActCategory;

use App\Exports\Object\Report\ActCategory\Sheets\PivotSheet;
use App\Models\Object\BObject;
use App\Services\Contract\ActService;
use App\Services\CurrencyExchangeRateService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private ActService $actService;
    private CurrencyExchangeRateService $currencyExchangeService;
    private BObject $object;

    public function __construct(ActService $actService, BObject $object, CurrencyExchangeRateService $currencyExchangeService)
    {
        $this->actService = $actService;
        $this->currencyExchangeService = $currencyExchangeService;
        $this->object = $object;
    }

    public function sheets(): array
    {
        return [
            new PivotSheet($this->actService, $this->object, $this->currencyExchangeService),
        ];
    }
}
