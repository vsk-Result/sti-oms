<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\Loan;
use App\Models\LoanHistory;
use App\Models\LoanNotifyTag;
use App\Models\Status;

class LoanHistoryService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function createLoanHistory(Loan $loan, array $requestData): void
    {
        LoanHistory::create([
            'loan_id' => $loan->id,
            'date' => $requestData['date'],
            'refund_date' => $requestData['refund_date'],
            'planned_refund_date' => $requestData['planned_refund_date'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'percent' => $this->sanitizer->set($requestData['percent'])->toAmount()->get(),
            'description' => $this->sanitizer->set($requestData['description'])->get(),
            'status_id' => Status::STATUS_ACTIVE,
        ]);
    }

    public function updateLoanHistory(Loan $loan, LoanHistory $loanHistory, array $requestData): void
    {
        $loanHistory->update([
            'date' => $requestData['date'],
            'refund_date' => $requestData['refund_date'],
            'planned_refund_date' => $requestData['planned_refund_date'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'percent' => $this->sanitizer->set($requestData['percent'])->toAmount()->get(),
            'description' => $this->sanitizer->set($requestData['description'])->get(),
            'status_id' => $requestData['status_id'],
        ]);
    }

    public function destroyLoanHistory(Loan $loan, LoanHistory $loanHistory): void
    {
        $loanHistory->delete();
    }
}