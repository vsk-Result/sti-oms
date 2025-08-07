<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ImportPeriodPaymentsDataFrom1C extends HandledCommand
{
    protected $signature = 'oms:import-period-payments-data-from-one-c';

    protected $description = 'Загружает сводные данные счетов по отчетному периоду из 1С';

    protected string $period = 'Ежедневно в 21:30';

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        try {
            $response = Http::withBasicAuth('WebService', 'Vi7je7da')->post('https://1c.st-ing.com/prod_STI_doc/hs/Telebot/Universal', [
                'Method' => 'GetBillSumByReportingPeriod',
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось загрузить сводные данные счетов по отчетному периоду из 1С');
            $this->sendErrorMessage($e->getMessage());
            $this->endProcess();
            return 0;
        }

        Cache::put('p_and_l_payments_period_1c_data', $data);

        $this->sendInfoMessage('Сводные данные счетов по отчетному периоду успешно загружены');

        $this->endProcess();

        return 0;
    }
}
