<?php

namespace App\Console\Commands;

use App\Console\BaseNotifyCommand;
use App\Models\Contract\Contract;
use App\Models\CurrencyExchangeRate;
use App\Models\Guarantee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CreateGuaranteeForContract extends BaseNotifyCommand
{
    protected $signature = 'oms-imports:create-guarantee-for-contract';

    protected $description = 'Создает или обновляет информацию по гарантийным удержаниям для договоров';

    public function __construct()
    {
        parent::__construct();
        $this->commandName = 'Создания/изменение ГУ для договоров';
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Создает или обновляет информацию по гарантийным удержаниям для договоров');

        $createdGuaranteesCount = 0;
        $updatedGuaranteesCount = 0;
        $contracts = Contract::where('type_id', Contract::TYPE_MAIN)->orderBy('object_id')->get();
        $currencies = ['RUB', 'EUR'];

        $message = '';

        foreach ($contracts as $contract) {
            foreach ($currencies as $currency) {
                $guaranteesAmount = $contract->getActsDepositesAmount($currency);
                $formatAmount = CurrencyExchangeRate::format($guaranteesAmount, $currency);

                $guarantee = Guarantee::where('object_id', $contract->object_id)
                    ->where('contract_id', $contract->id)
                    ->where('currency', $currency)->first();

                if (! $guarantee) {
                    if ($guaranteesAmount > 0) {
                        Guarantee::create([
                            'contract_id' => $contract->id,
                            'company_id' => $contract->company_id,
                            'object_id' => $contract->object_id,
                            'fact_amount' => $guaranteesAmount,
                            'currency' => $currency,
                        ]);
                        $createdGuaranteesCount++;

                        $message .= 'ГУ для договора "' . $contract->name . '" успешно создано на сумму ' . $formatAmount . PHP_EOL;
                    }
                } else {
                    if ($guaranteesAmount != $guarantee->fact_amount) {
                        $formatOldAmount = CurrencyExchangeRate::format($guarantee->fact_amount, $currency);
                        $guarantee->update([
                            'fact_amount' => $guaranteesAmount,
                        ]);
                        $updatedGuaranteesCount++;

                        $message .= 'ГУ для договора "' . $contract->name . '" успешно обновлено, новая сумма ' . $formatAmount . ', предыдущая сумма: ' . $formatOldAmount . PHP_EOL;
                    }
                }
            }
        }

        $message.= '-------' . PHP_EOL;
        $message.= $createdGuaranteesCount . ' ГУ успешно создано' . PHP_EOL;
        $message.= $updatedGuaranteesCount . ' ГУ успешно обновлено' . PHP_EOL;

        if ($createdGuaranteesCount + $updatedGuaranteesCount === 0) {
            $message = 'Обработка прошла без изменений';
        }

        $this->sendSuccessNotification($message);
        return 0;
    }
}
