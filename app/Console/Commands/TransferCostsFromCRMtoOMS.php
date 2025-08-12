<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\CashAccount\CashAccount;
use App\Models\KostCode;
use App\Models\Payment;
use App\Models\User;
use App\Services\CashAccount\CashAccountService;

class TransferCostsFromCRMtoOMS extends HandledCommand
{
    protected $signature = 'oms:transfer-costs-from-crm-to-oms';

    protected $description = 'Перемещение касс из ОМС в CRM';

    protected string $period = 'Вручную';

    public function __construct(private CashAccountService $cashAccountService)
    {
        parent::__construct();
    }

    public function handle()
    {
//        if ($this->isProcessRunning()) {
//            return 0;
//        }
//
//        $this->startProcess();

        $costs = [
//            [
//                'id' => 64,
//                'name' => 'А.А. - Офис Веспер',
//                'object_codes' => ['372'],
//                'crm_user_id' => 229,
//                'oms_user_id' => 48,
//                'balance' => 0,
//            ],
            [
                'id' => 65,
                'name' => 'А.А. - GRAND LINE',
                'object_codes' => ['374'],
                'crm_user_id' => 229,
                'oms_user_id' => 48,
                'balance' => 0,
            ],
//            [
//                'id' => 68,
//                'name' => 'А.А. - Велесстрой',
//                'object_codes' => ['378'],
//                'crm_user_id' => 229,
//                'oms_user_id' => 48,
//                'balance' => 49189,
//            ],
            [
                'id' => 66,
                'name' => 'В.В. - Stone Towers',
                'object_codes' => ['379'],
                'crm_user_id' => 106,
                'oms_user_id' => 61,
                'balance' => 8139,
            ],
            // В В - vladimir123!
            [
                'id' => 50,
                'name' => 'Г.М. - Завидово',
                'object_codes' => ['358'],
                'crm_user_id' => 154,
                'oms_user_id' => 11,
                'balance' => 1023787.34,
            ],
            [
                'id' => 67,
                'name' => 'М.С. - Валента СМР',
                'object_codes' => ['370'],
                'crm_user_id' => 241,
                'oms_user_id' => 32,
                'balance' => 7599.89,
            ],
            [
                'id' => 47,
                'name' => 'К.О. - Аэрофлот',
                'object_codes' => ['380'],
                'crm_user_id' => 129,
                'oms_user_id' => 14,
                'balance' => 119498.38,
            ],
            [
                'id' => 46,
                'name' => 'М.М. - Склад',
                'object_codes' => ['27.1'],
                'crm_user_id' => 30,
                'oms_user_id' => 62,
                'balance' => 174816.47,
            ],
            // М М - milan123!
            [
                'id' => 55,
                'name' => 'С.О. - Кемерово',
                'object_codes' => ['361'],
                'crm_user_id' => 220,
                'oms_user_id' => 63,
                'balance' => 135246.53,
            ],
            // С С - sergei123!
            [
                'id' => 62,
                'name' => 'Е.О. - Магнитогорск',
                'object_codes' => ['373'],
                'crm_user_id' => 238,
                'oms_user_id' => 53,
                'balance' => 24810.13,
            ],
            [
                'id' => 51,
                'name' => 'Е.Д. - Камчатка',
                'object_codes' => ['363'],
                'crm_user_id' => 208,
                'oms_user_id' => 45,
                'balance' => 275488.21,
            ],
        ];

        foreach ($costs as $cost) {
            $this->cashAccountService->createCashAccount([
                'start_balance_amount' => $cost['balance'],
                'name' => $cost['name'],
                'responsible_user_id' => $cost['oms_user_id'],
                'balance_amount' => $cost['balance'],
                'status_id' => CashAccount::STATUS_ACTIVE
            ]);

            $user = User::find($cost['oms_user_id']);
            $user->update([
                'crm_user_id' => $cost['crm_user_id']
            ]);
        }

        $this->sendInfoMessage('Перенос прошел успешно');

        $this->endProcess();

        return 0;
    }
}
