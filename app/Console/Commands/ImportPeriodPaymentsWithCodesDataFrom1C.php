<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ImportPeriodPaymentsWithCodesDataFrom1C extends HandledCommand
{
    protected $signature = 'oms:import-period-payments-with-codes-data-from-one-c';

    protected $description = 'Загружает сводные данные счетов по отчетному периоду из 1С со статьями затрат';

    protected string $period = 'Ежедневно в 22:00';

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        $currentInfo = Cache::get('GetBillSumByCodeReportingPeriod', []);

        $months = [
            '2025-10',
            '2025-11',
            '2025-12',
            '2026-01',
            '2026-02',
        ];

        $months1C = [
            '10-2025',
            '11-2025',
            '12-2025',
            '01-2026',
            '02-2026',
        ];

        foreach ($months as $index => $month) {
            try {
                $response = Http::withBasicAuth('WebService', 'Vi7je7da')->post('https://1c.st-ing.com/prod_STI_doc/hs/Telebot/Universal', [
                    'Method' => 'GetBillSumByReportingPeriod_CashFlowItem',
                    'start_date' => $months1C[$index],
                    'end_date' => $months1C[$index],
                ]);

                $data = json_decode($response->getBody()->getContents(), true);

                $currentInfo[$month] = $this->getFormattedInfo($data);
            } catch (\Exception $e) {
                $this->sendErrorMessage('Не удалось загрузить сводные данные счетов по отчетному периоду из 1С');
                $this->sendErrorMessage($e->getMessage());
                $this->endProcess();
                return 0;
            }
        }

        Cache::put('GetBillSumByCodeReportingPeriod', $currentInfo);

        $this->sendInfoMessage('Сводные данные счетов по отчетному периоду успешно загружены');

        $this->endProcess();

        return 0;
    }

    public function getFormattedInfo($info): array
    {
        $formattedInfo = [];

        foreach ($info as $item) {
            foreach ($item['Details'] as $detail) {
                $code = $detail['CashFlowItem']['Code'];

                if (! isset($formattedInfo['codes'][$code])) {
                    $formattedInfo['codes'][$code] = 0;
                }

                if (! isset($formattedInfo['objects'][$item['Object']['Code']][$code])) {
                    $formattedInfo['objects'][$item['Object']['Code']][$code] = 0;
                }

                $formattedInfo['codes'][$code] += -$detail['Sum'];
                $formattedInfo['objects'][$item['Object']['Code']][$code] += -$detail['Sum'];
            }
        }

        return $formattedInfo;
    }
}
