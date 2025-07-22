<?php

namespace App\Services\CashAccount;

use App\Helpers\Sanitizer;
use App\Models\CashAccount\CashAccount;
use Illuminate\Database\Eloquent\Collection;

class CashAccountService
{
    public function __construct(private Sanitizer $sanitizer) {}

    public function getResponsibleCashAccounts(): Collection
    {
        if (auth()->user()->hasRole('super-admin')) {
            return CashAccount::active()->get();
        }

        return CashAccount::active()->where('responsible_user_id', auth()->id())->get();
    }

    public function getSharedCashAccounts(): Collection
    {
        return auth()->user()->sharedCashAccounts()->get();
    }

    public function createCashAccount(array $requestData): CashAccount
    {
        $balance = $this->sanitizer->set($requestData['start_balance_amount'])->toAmount()->get();
        $cashAccount = CashAccount::create([
            'name' => $requestData['name'],
            'responsible_user_id' => $requestData['responsible_user_id'],
            'start_balance_amount' => $balance,
            'balance_amount' => $balance,
            'status_id' => CashAccount::STATUS_ACTIVE
        ]);

        $cashAccount->objects()->sync($requestData['object_id']);

        return $cashAccount;
    }

    public function updateCashAccount(CashAccount $cashAccount, array $requestData): CashAccount
    {
        $cashAccount->update([
            'name' => $requestData['name'],
        ]);

        $cashAccount->objects()->sync($requestData['object_id']);

        return $cashAccount;
    }

    public function destroyCashAccount(CashAccount $cashAccount): CashAccount
    {
        $cashAccount->update([
            'status_id' => CashAccount::STATUS_DELETED,
        ]);

        return $cashAccount;
    }

    public function updateBalance(CashAccount $cashAccount): void
    {
        $cashAccount->update([
            'balance_amount' => $cashAccount->start_balance_amount + $cashAccount->payments()->sum('amount')
        ]);
    }
}
