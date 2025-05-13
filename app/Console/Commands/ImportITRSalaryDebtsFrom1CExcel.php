<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ImportITRSalaryDebtsFrom1CExcel extends HandledCommand
{
    protected $signature = 'oms:import-itr-salary-debts-from-1c-excel';

    protected $description = 'Загружает долги по зарплатам ИТР из 1С из Excel';

    protected string $period = 'Раз в час';

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        try {
            $response = Http::withBasicAuth('WebService', 'Vi7je7da')->post('https://1c.st-ing.com/prod_STI_ushr/hs/Telebot/Universal', [
                'Method' => 'GetPayDebtITR',
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            $ITRSalaryInfo = $data['Result'];
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось загрузить долги по зарплатам ИТР из 1С');
            $this->sendErrorMessage($e->getMessage());
            $this->endProcess();
            return 0;
        }

        $formattedInfo = [];

        foreach ($ITRSalaryInfo as $info) {
            $objectCode = $info['ObjectCode'];

            if ($objectCode == '27') {
                $objectCode = '27.1';
            }

            $formattedInfo[$objectCode] = [
                'details' => [],
                'total_amount' => 0
            ];

            foreach ($info['Debt'] as $period) {
                $date = substr($period['Period'], 0, strpos($period['Period'], 'T'));

                $formattedInfo[$objectCode]['details'][] = [
                    'date' => $date,
                    'formatted_date' => Carbon::parse($date)->format('F Y'),
                    'amount' => -$period['Sum']
                ];

                $formattedInfo[$objectCode]['total_amount'] += -$period['Sum'];
            }

            $details = $formattedInfo[$objectCode]['details'];
            $dates = array_column($details, 'date');
            array_multisort($dates, SORT_ASC, SORT_STRING, $details);

            $formattedInfo[$objectCode]['details'] = $details;
        }

        Cache::put('itr_salary_1c_data', $formattedInfo);

        $this->sendInfoMessage('Долги по зарплатам ИТР успешно загружены');

        $this->endProcess();

        return 0;
    }
}
