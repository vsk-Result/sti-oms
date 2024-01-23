<?php

namespace App\Services;

use App\Exports\Object\Balance\Export;
use App\Models\Object\BObject;
use App\Services\Contract\ContractService;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ObjectBalanceExportService
{
    private PivotObjectDebtService $pivotObjectDebtService;
    private CurrencyExchangeRateService $currencyExchangeRateService;
    private ContractService $contractService;

    public function __construct(
        PivotObjectDebtService $pivotObjectDebtService,
        CurrencyExchangeRateService $currencyExchangeRateService,
        ContractService $contractService,
    ) {
        $this->pivotObjectDebtService = $pivotObjectDebtService;
        $this->currencyExchangeRateService = $currencyExchangeRateService;
        $this->contractService = $contractService;
    }
    public function store(BObject $object): string
    {
        $filename = $this->getFileName($object->getName());

        Excel::store(new Export(
            $object,
            $this->pivotObjectDebtService,
            $this->currencyExchangeRateService,
            $this->contractService
        ), 'object-balance/' . $filename);

        return storage_path('') . '/app/public/object-balance/' . $filename;
    }

    public function download(BObject $object): BinaryFileResponse
    {
        $filename = $this->getFileName($object->getName());

        return Excel::download(new Export(
            $object,
            $this->pivotObjectDebtService,
            $this->currencyExchangeRateService,
            $this->contractService
        ), $filename);
    }

    private function getFileName(string $objectName): string
    {
        $now = Carbon::now()->format('d-m-Y');

        return sprintf('Баланс объекта %s на %s.xlsx', $objectName, $now);
    }
}