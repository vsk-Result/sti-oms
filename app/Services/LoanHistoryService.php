<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\Loan;
use App\Models\LoanHistory;
use App\Models\LoanNotifyTag;
use App\Models\Payment;
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

    public function reloadLoanHistory(Loan $loan): void
    {
        $loan->historyPayments()->delete();

        $searchNames = explode(';', $loan->search_name);

        $payments = Payment::where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)
            ->where(function($q) use ($searchNames) {
                foreach ($searchNames as $searchName) {
                    $q->orWhere('description', 'LIKE', '%' . $searchName . '%');
                }
            })
            ->where('description', 'NOT LIKE', '%процент%')
            ->where('description', 'NOT LIKE', '%комиссии%')
            ->get();

        foreach($payments as $payment) {
            $this->createLoanHistory($loan, [
                'loan_id' => $loan->id,
                'date' => $payment->date,
                'refund_date' => null,
                'planned_refund_date' => null,
                'amount' => $payment->amount,
                'percent' => 0,
                'description' => $payment->description,
                'status_id' => Status::STATUS_ACTIVE,
            ]);
        }
    }
}