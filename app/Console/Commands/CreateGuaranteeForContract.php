<?php

namespace App\Console\Commands;

use App\Models\Contract\Contract;
use App\Models\CurrencyExchangeRate;
use App\Models\Guarantee;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateGuaranteeForContract extends Command
{
    protected $signature = 'oms-imports:create-guarantee-for-contract';

    protected $description = 'Создает или обновляет информацию по гарантийным удержаниям для договоров';

    public function __construct()
    {
        parent::__construct();
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

        foreach ($contracts as $contract) {
            foreach ($currencies as $currency) {
                $guaranteesAmount = $contract->getActsDepositesAmount($currency);
                $formatAmount = CurrencyExchangeRate::format($guaranteesAmount, $currency);

                $guarantee = Guarantee::where('object_id', $contract->object_id)
                    ->where('contract_id', $contract->id)
                    ->where('currency', $currency)->first();

                if (! $guarantee) {
                    if ($guaranteesAmount > 0) {
                        $guarantee = Guarantee::create([
                            'contract_id' => $contract->id,
                            'company_id' => $contract->company_id,
                            'object_id' => $contract->object_id,
                            'fact_amount' => $guaranteesAmount,
                            'currency' => $currency,
                        ]);
                        $createdGuaranteesCount++;

                        Log::channel('custom_imports_log')->debug('[SUCCESS] [ID: #' . $guarantee->id . '] [CONTRACT-ID: #' . $contract->id . '] Гарантийное удержание успешно создано на сумму ' . $formatAmount);
                    }
                } else {
                    if ($guaranteesAmount != $guarantee->fact_amount) {
                        $formatOldAmount = CurrencyExchangeRate::format($guarantee->fact_amount, $currency);
                        $guarantee->update([
                            'fact_amount' => $guaranteesAmount,
                        ]);
                        $updatedGuaranteesCount++;

                        Log::channel('custom_imports_log')->debug('[SUCCESS] [ID: #' . $guarantee->id . '] [CONTRACT-ID: #' . $contract->id . '] Гарантийное удержание успешно обновлено на сумму ' . $formatAmount . ', предыдущая сумма: ' . $formatOldAmount);
                    }
                }
            }
        }

        Log::channel('custom_imports_log')->debug('[SUCCESS] ' . $createdGuaranteesCount . ' гарантийных удержаний успешно создано');
        Log::channel('custom_imports_log')->debug('[SUCCESS] ' . $updatedGuaranteesCount . ' гарантийных удержаний успешно обновлено');

        return 0;
    }
}
