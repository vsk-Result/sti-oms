<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\Bank;
use App\Models\PaymentImport;
use App\Models\Status;

class FixBanksBalanceToDate extends HandledCommand
{
    protected $signature = 'oms:fix-banks-balance-to-date';

    protected $description = 'Зафиксировать баланс счетов на определенную дату';

    protected string $period = 'Вручную';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $dateToFix = '2026-08-17';

        $banks = Bank::where('status_id', Status::STATUS_ACTIVE)->get();
        foreach ($banks as $bank) {
            $balance = PaymentImport::where('date', '<=', $dateToFix)
                ->where('bank_id', $bank->id)
                ->orderBy('date', 'desc')
                ->first();

            $bank->update([
                'balance_amount' => $balance->outgoing_balance ?? 0,
                'balance_date' => $dateToFix,
            ]);
        }

        $this->sendInfoMessage('Балансы успешно зафиксированы');

        return 0;
    }
}
