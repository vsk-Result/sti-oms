<?php

namespace App\Services\PaymentImport;

use App\Models\Company;
use App\Models\PaymentImport;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentImportService
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService) {
        $this->paymentService = $paymentService;
    }

    public function filterImport(array $requestData): LengthAwarePaginator
    {
        $query = PaymentImport::query();

        if (! auth()->user()->hasRole('super-admin')) {
            $query->where('type_id', '!=', PaymentImport::TYPE_HISTORY);
        }

        if (! empty($requestData['period'])) {
            $period = explode(' - ', $requestData['period']);
            $query->whereBetween('date', [Carbon::parse($period[0]), Carbon::parse($period[1])]);
        }

        if (! empty($requestData['company_id'])) {
            $query->whereIn('company_id', $requestData['company_id']);
        }

        if (! empty($requestData['currency'])) {
            $query->whereIn('currency', $requestData['currency']);
        }

        if (! empty($requestData['bank_id'])) {
            $query->whereIn('bank_id', $requestData['bank_id']);
        }

        if (! empty($requestData['type_id'])) {
            $query->whereIn('type_id', $requestData['type_id']);
        }

        if (! empty($requestData['status_id'])) {
            $query->whereIn('status_id', $requestData['status_id']);
        }

        if (! empty($requestData['import_id'])) {
            $query->where('id', $requestData['import_id']);
        }

        $perPage = 30;
        if (! empty($requestData['count_per_page'])) {
            $perPage = (int) preg_replace("/[^0-9]/", '', $requestData['count_per_page']);
        }

        $query->with('company', 'createdBy');
        $query->orderByDesc('date')->orderByDesc('id');

        return $query->paginate($perPage)->withQueryString();
    }

    public function destroyImport(PaymentImport $import): PaymentImport
    {
        foreach ($import->payments as $payment) {
            $this->paymentService->destroyPayment($payment);
        }

        $import->delete();

        return $import;
    }

    public function getInvalidBalanceStatement(): PaymentImport | null
    {
        foreach (PaymentImport::whereNotIn('company_id', Company::getDT()->id)->where('type_id', PaymentImport::TYPE_STATEMENT)->latest('date')->get() as $import) {
            if ($import->hasInvalidBalance()) {
                return $import;
            }
        }

        return null;
    }
}
