<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\Loan;
use App\Models\Status;
use Illuminate\Pagination\LengthAwarePaginator;

class LoanService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function filterLoans(array $requestData, array &$total): LengthAwarePaginator
    {
        $query = Loan::query();

        if (! empty($requestData['bank_id'])) {
            $query->whereIn('bank_id', $requestData['bank_id']);
        }

        if (! empty($requestData['type_id'])) {
            $query->whereIn('type_id', $requestData['type_id']);
        }

        if (! empty($requestData['company_id'])) {
            $query->whereIn('company_id', $requestData['company_id']);
        }

        if (! empty($requestData['organization_id'])) {
            $query->whereIn('organization_id', $requestData['organization_id']);
        }

        $perPage = 30;
        if (! empty($requestData['count_per_page'])) {
            $perPage = (int) preg_replace("/[^0-9]/", '', $requestData['count_per_page']);
        }

        $query->with('company', 'organization');
        $query->orderByDesc('amount');

        $total['amount_loan'] = (clone $query)->where('type_id', Loan::TYPE_LOAN)->sum('amount');
        $total['amount_credit'] = (clone $query)->where('type_id', Loan::TYPE_CREDIT)->sum('amount');

        return $query->paginate($perPage)->withQueryString();
    }

    public function createLoan(array $requestData): void
    {
        Loan::create([
            'company_id' => $requestData['company_id'],
            'bank_id' => $requestData['bank_id'],
            'type_id' => $requestData['type_id'],
            'organization_id' => $requestData['organization_id'],
            'name' => $requestData['name'],
            'description' => $requestData['description'],
            'start_date' => $requestData['start_date'],
            'end_date' => $requestData['end_date'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'percent' => $this->sanitizer->set($requestData['percent'])->toAmount()->get(),
            'status_id' => Status::STATUS_ACTIVE,
        ]);
    }

    public function updateLoan(Loan $loan, array $requestData): void
    {
        $loan->update([
            'company_id' => $requestData['company_id'],
            'bank_id' => $requestData['bank_id'],
            'type_id' => $requestData['type_id'],
            'organization_id' => $requestData['organization_id'],
            'name' => $requestData['name'],
            'description' => $requestData['description'],
            'start_date' => $requestData['start_date'],
            'end_date' => $requestData['end_date'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'percent' => $this->sanitizer->set($requestData['percent'])->toAmount()->get(),
            'status_id' => $requestData['status_id']
        ]);
    }

    public function destroyLoan(Loan $loan): void
    {
        $loan->delete();
    }
}
