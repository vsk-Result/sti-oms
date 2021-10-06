<?php

namespace App\Imports\Bank;

use App\Services\PaymentService;

abstract class BankImport
{
    protected PaymentService $paymentService;

    abstract public function processImportData(array $importData): array;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    protected function cleanValue($value): string
    {
        return str_replace("\n", '', (string) $value);
    }
}
