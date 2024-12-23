<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\Contract\Contract;
use App\Models\Organization;
use App\Models\Payment;
use App\Services\Contract\ContractAvansReceivedService;

class CheckAvansesReceivedFromPayments extends HandledCommand
{
    protected $signature = 'oms:check-avanses-received-from-payments';

    protected $description = 'Определяет и создает полученные авансы от заказчиков в оплатах';

    protected string $period = 'Ежедневно в 13:00 и в 18:00';

    public function __construct(private ContractAvansReceivedService $avansReceivedService)
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        $checkItems = [
            [
                'id' => 1,
                'organization_name' => 'АО "ТИНЬКОФФ БАНК"',
                'contract_name' => '08/2022-ТКФ от 20.09.22 (переменная часть)',
                'payment_templates' => ['по COR №', 'поCOR№', 'по COR№', 'поCOR №'],
                'service' => $this->avansReceivedService,
                'create_method' => 'createReceivedAvans',
                'active' => true
            ],
            [
                'id' => 2,
                'organization_name' => 'АО "МАПК(Е)"',
                'contract_name' => 'МАПКЕ-337-23-Р38 от 09.06.2023',
                'payment_templates' => ['авансового платежа', 'Аванс (частично)№', 'целевой аванс', 'целевого авансового'],
                'service' => $this->avansReceivedService,
                'create_method' => 'createReceivedAvans',
                'start_date' => '2024-04-10',
                'active' => true
            ],
//            [
//                'id' => 3,
//                'organization_name' => 'Общество с ограниченной ответственностью "БИЗНЕС АКТИВ"',
//                'contract_name' => 'Кем-007-2023 от 01.08.23 (работы Группы 2)',
//                'payment_templates' => ['Обеспечительный платеж по ДС №1', 'Оплата авансового платежа согласно'],
//                'service' => $this->avansReceivedService,
//                'create_method' => 'createReceivedAvans',
//                'start_date' => '2024-04-10',
//                'active' => true
//            ],
            [
                'id' => 4,
                'organization_name' => 'АО "ВАЛЕНТА ФАРМ"',
                'contract_name' => '7207462',
                'payment_templates' => ['ЗА РАБОТЫ ПО СТАДИИ'],
                'service' => $this->avansReceivedService,
                'create_method' => 'createReceivedAvans',
                'start_date' => '2024-04-10',
                'active' => true
            ]
        ];

        $createdAvansesCount = 0;
        foreach ($checkItems as $checkItem) {
            if (! $checkItem['active']) {
                continue;
            }

            $organization = Organization::where('name', $checkItem['organization_name'])->first();
            if (! $organization) {
                $this->sendErrorMessage('[ID: #' . $checkItem['id'] . '] Не удалось найти контрагента: "' . $checkItem['organization_name']);
                continue;
            }

            $contract = Contract::where('name', $checkItem['contract_name'])->first();
            if (! $contract) {
                $this->sendErrorMessage('[ID: #' . $checkItem['id'] . '] Не удалось найти контракт: "' . $checkItem['contract_name']);
                continue;
            }

            $paymentQuery = Payment::query();

            if (isset($checkItem['start_date'])) {
                $paymentQuery->where('date', '>=', $checkItem['start_date']);
            }

            $paymentQuery->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH);
            $paymentQuery->where('organization_sender_id', $organization->id);

            $paymentQuery->where(function($q) use($checkItem) {
                foreach ($checkItem['payment_templates'] as $template) {
                    $q->orWhere('description', 'LIKE', '%' . $template . '%');
                }
            });

            $payments = $paymentQuery->get();
            $existReceivedAvanses = $contract->avansesReceived;

            foreach ($payments as $payment) {
                $avansAlreadyExist = $existReceivedAvanses->where('date', $payment->date)
                    ->where('amount', $payment->amount)
                    ->first();

                if ($avansAlreadyExist) {
                    continue;
                }

                try {
                    $createdAvans = $checkItem['service']->{$checkItem['create_method']}($contract, [
                        'date' => $payment->date,
                        'amount' => $payment->amount,
                        'description' => 'Создано автоматически из оплаты #' . $payment->id,
                    ]);
                    $createdAvansesCount++;
                    $this->sendInfoMessage('[ID: #' . $checkItem['id'] . '] [PAYMENT-ID: #' . $payment->id . '] [RECEIVED-AVANS-ID: #' . $createdAvans->id . '] Аванс успешно создан');
                } catch (\Exception $e) {
                    $this->sendErrorMessage('[ID: #' . $checkItem['id'] . '] [PAYMENT-ID: #' . $payment->id . '] Не удалось создать полученный аванс: "' . $e->getMessage());
                    continue;
                }
            }

            $avans = $contract->avanses->first();
            if ($avans) {
                $avans->update([
                    'amount' => $contract->avansesReceived->sum('amount')
                ]);
            }
        }

        $this->sendInfoMessage($createdAvansesCount . ' полученных авансов успешно создано');

        $this->endProcess();

        return 0;
    }
}
