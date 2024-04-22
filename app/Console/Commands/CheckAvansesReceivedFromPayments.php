<?php

namespace App\Console\Commands;

use App\Models\Contract\Contract;
use App\Models\Organization;
use App\Models\Payment;
use App\Services\Contract\ContractAvansReceivedService;
use App\Services\CRONProcessService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckAvansesReceivedFromPayments extends Command
{
    protected $signature = 'oms:check-avanses-received-from-payments';

    protected $description = 'Определяет и создает полученные авансы от заказчиков в оплатах';

    public function __construct(ContractAvansReceivedService $avansReceivedService, CRONProcessService $CRONProcessService)
    {
        parent::__construct();
        $this->avansReceivedService = $avansReceivedService;
        $this->CRONProcessService = $CRONProcessService;
        $this->checkItems = [
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
        $this->CRONProcessService->createProcess(
            $this->signature,
            $this->description,
            'Ежедневно в 13:00 и в 18:00'
        );
    }

    public function handle()
    {
        if (count($this->checkItems) === 0) {
            return 0;
        }

        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Определяет и создает полученные авансы от заказчиков в оплатах');

        $createdAvansesCount = 0;
        foreach ($this->checkItems as $checkItem) {
            if (! $checkItem['active']) {
                continue;
            }

            $organization = Organization::where('name', $checkItem['organization_name'])->first();
            if (! $organization) {
                $errorMessage = '[ERROR] [ID: #' . $checkItem['id'] . '] Не удалось найти контрагента: "' . $checkItem['organization_name'];
                Log::channel('custom_imports_log')->debug($errorMessage);
                $this->CRONProcessService->failedProcess($this->signature, $errorMessage);

                return 0;
            }

            $contract = Contract::where('name', $checkItem['contract_name'])->first();
            if (! $contract) {
                $errorMessage = '[ERROR] [ID: #' . $checkItem['id'] . '] Не удалось найти контракт: "' . $checkItem['contract_name'];
                Log::channel('custom_imports_log')->debug($errorMessage);
                $this->CRONProcessService->failedProcess($this->signature, $errorMessage);

                return 0;
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
                    Log::channel('custom_imports_log')->debug('[SUCCESS] [ID: #' . $checkItem['id'] . '] [PAYMENT-ID: #' . $payment->id . '] [RECEIVED-AVANS-ID: #' . $createdAvans->id . '] Аванс успешно создан');
                } catch (\Exception $e) {
                    $errorMessage = '[ERROR] [ID: #' . $checkItem['id'] . '] [PAYMENT-ID: #' . $payment->id . '] Не удалось создать полученный аванс: "' . $e->getMessage();
                    Log::channel('custom_imports_log')->debug($errorMessage);
                    $this->CRONProcessService->failedProcess($this->signature, $errorMessage);

                    return 0;
                }
            }

            $avans = $contract->avanses->first();
            if ($avans) {
                $avans->update([
                    'amount' => $contract->avansesReceived->sum('amount')
                ]);
            }
        }

        Log::channel('custom_imports_log')->debug('[SUCCESS] ' . $createdAvansesCount . ' полученных авансов успешно создано');
        $this->CRONProcessService->successProcess($this->signature);

        return 0;
    }
}
