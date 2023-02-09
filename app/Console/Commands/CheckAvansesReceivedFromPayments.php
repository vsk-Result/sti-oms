<?php

namespace App\Console\Commands;

use App\Models\Contract\Contract;
use App\Models\Organization;
use App\Models\Payment;
use App\Services\Contract\ContractAvansReceivedService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckAvansesReceivedFromPayments extends Command
{
    protected $signature = 'oms-imports:check-avanses-received-from-payments';

    protected $description = 'Определяет и создает полученные авансы от заказчиков в оплатах';

    public function __construct(ContractAvansReceivedService $avansReceivedService)
    {
        parent::__construct();
        $this->avansReceivedService = $avansReceivedService;
        $this->checkItems = [
            [
                'id' => 1,
                'organization_name' => 'АО "ТИНЬКОФФ БАНК"',
                'contract_name' => '08/2022-ТКФ от 20.09.22 (переменная часть)',
                'payment_templates' => ['по COR №', 'поCOR№', 'по COR№', 'поCOR №'],
                'service' => $this->avansReceivedService,
                'create_method' => 'createReceivedAvans',
                'active' => true
            ]
        ];
    }

    public function handle()
    {
        if (count($this->checkItems) === 0) {
            return 0;
        }

        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Определяет и создает полученные авансы от заказчиков в оплатах');

        foreach ($this->checkItems as $checkItem) {
            if (! $checkItem['active']) {
                continue;
            }

            $organization = Organization::where('name', $checkItem['organization_name'])->first();
            if (! $organization) {
                Log::channel('custom_imports_log')->debug('[ERROR] [ID: #' . $checkItem['id'] . '] Не удалось найти контрагента: "' . $checkItem['organization_name']);
                continue;
            }

            $contract = Contract::where('name', $checkItem['contract_name'])->first();
            if (! $contract) {
                Log::channel('custom_imports_log')->debug('[ERROR] [ID: #' . $checkItem['id'] . '] Не удалось найти контракт: "' . $checkItem['contract_name']);
                continue;
            }

            $paymentQuery = Payment::query();

            $paymentQuery->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH);
            $paymentQuery->where('organization_sender_id', $organization->id);

            $paymentQuery->where(function($q) use($checkItem) {
                foreach ($checkItem['payment_templates'] as $template) {
                    $q->orWhere('description', 'LIKE', '%' . $template . '%');
                }
            });

            $createdAvansesCount = 0;
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
                        'description' => 'Создать автоматически из оплаты #' . $payment->id,
                    ]);
                    $createdAvansesCount++;
                    Log::channel('custom_imports_log')->debug('[SUCCESS] [ID: #' . $checkItem['id'] . '] [PAYMENT-ID: #' . $payment->id . '] [RECEIVED-AVANS-ID: #' . $createdAvans->id . '] Аванс супешно создан');
                } catch (\Exception $e) {
                    Log::channel('custom_imports_log')->debug('[ERROR] [ID: #' . $checkItem['id'] . '] [PAYMENT-ID: #' . $payment->id . '] Не удалось создать полученный аванс: "' . $e->getMessage());
                    return 0;
                }
            }
        }

        Log::channel('custom_imports_log')->debug('[SUCCESS] ' . $createdAvansesCount . ' полученных авансов успешно создано');

        return 0;
    }
}
