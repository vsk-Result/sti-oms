<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ImportITRListDataFrom1C extends HandledCommand
{
    protected $signature = 'oms:import-itr-list-data-from-one-c';

    protected $description = 'Загружает список ИТР из 1С';

    protected string $period = 'Ежедневно в 21:00';

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        try {
            $response = Http::withBasicAuth('WebService', 'Vi7je7da')->post('https://1c.st-ing.com/prod_STI_ushr/hs/Telebot/Universal', [
                'Method' => 'GetEmployeesITR',
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            $ITRInfo = $data['Result'];
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось загрузить список ИТР из 1С');
            $this->sendErrorMessage($e->getMessage());
            $this->endProcess();
            return 0;
        }

        $names = array_column($ITRInfo, 'Name');
        array_multisort($names, SORT_ASC, SORT_STRING, $ITRInfo);

        Cache::put('itr_list_1c_data', $ITRInfo);

        $this->sendInfoMessage('Список ИТР успешно загружены');

        $this->endProcess();

        return 0;
    }
}
