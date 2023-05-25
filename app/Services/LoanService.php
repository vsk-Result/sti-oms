<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\Loan;
use App\Models\LoanNotifyTag;
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

        $total['amount_loan_from_sti'] = (clone $query)->where('type_id', Loan::TYPE_LOAN)->where('organization_type_id', Loan::ORGANIZATION_TYPE_LENDER)->sum('amount');
        $total['amount_credit_from_sti'] = (clone $query)->where('type_id', Loan::TYPE_CREDIT)->where('organization_type_id', Loan::ORGANIZATION_TYPE_LENDER)->sum('amount');

        $total['amount_loan_to_sti'] = (clone $query)->where('type_id', Loan::TYPE_LOAN)->where('organization_type_id', Loan::ORGANIZATION_TYPE_BORROWER)->sum('amount');
        $total['amount_credit_to_sti'] = (clone $query)->where('type_id', Loan::TYPE_CREDIT)->where('organization_type_id', Loan::ORGANIZATION_TYPE_BORROWER)->sum('amount');

        return $query->paginate($perPage)->withQueryString();
    }

    public function createLoan(array $requestData): void
    {
        $loan = Loan::create([
            'company_id' => $requestData['company_id'],
            'bank_id' => $requestData['bank_id'],
            'type_id' => $requestData['type_id'],
            'organization_type_id' => $requestData['organization_type_id'],
            'organization_id' => $requestData['organization_id'],
            'name' => $requestData['name'],
            'search_name' => $requestData['search_name'],
            'description' => $requestData['description'],
            'start_date' => $requestData['start_date'],
            'end_date' => $requestData['end_date'],
            'amount' => 0,
            'paid_amount' => $this->sanitizer->set($requestData['paid_amount'])->toAmount()->get(),
            'total_amount' => $this->sanitizer->set($requestData['total_amount'])->toAmount()->get(),
            'percent' => $this->sanitizer->set($requestData['percent'])->toAmount()->get(),
            'status_id' => Status::STATUS_ACTIVE,
            'is_auto_paid' => $requestData['auto_paid'] === 'auto',
        ]);

        $loan->updateDebtAmount();

        if (! is_null($requestData['tags'])) {
            $tags = json_decode($requestData['tags'], true);
            foreach ($tags as $tag) {
                LoanNotifyTag::create([
                    'loan_id' => $loan->id,
                    'tag' => $tag['value']
                ]);
            }
        }
    }

    public function updateLoan(Loan $loan, array $requestData): void
    {
        $loan->update([
            'company_id' => $requestData['company_id'],
            'bank_id' => $requestData['bank_id'],
            'type_id' => $requestData['type_id'],
            'organization_type_id' => $requestData['organization_type_id'],
            'organization_id' => $requestData['organization_id'],
            'name' => $requestData['name'],
            'search_name' => $requestData['search_name'],
            'description' => $requestData['description'],
            'start_date' => $requestData['start_date'],
            'end_date' => $requestData['end_date'],
            'amount' => 0,
            'paid_amount' => $this->sanitizer->set($requestData['paid_amount'])->toAmount()->get(),
            'total_amount' => $this->sanitizer->set($requestData['total_amount'])->toAmount()->get(),
            'percent' => $this->sanitizer->set($requestData['percent'])->toAmount()->get(),
            'status_id' => $requestData['status_id'],
            'is_auto_paid' => $requestData['auto_paid'] === 'auto',
        ]);

        $loan->updateDebtAmount();
        $loan->notifyTags()->delete();

        if (! is_null($requestData['tags'])) {
            $tags = json_decode($requestData['tags'], true);
            foreach ($tags as $tag) {
                LoanNotifyTag::create([
                    'loan_id' => $loan->id,
                    'tag' => $tag['value']
                ]);
            }
        }
    }

    public function destroyLoan(Loan $loan): void
    {
        $loan->notifyTags()->delete();
        $loan->historyPayments()->delete();
        $loan->payments()->delete();
        $loan->delete();
    }
}
