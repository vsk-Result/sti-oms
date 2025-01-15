<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class GetDebtsFilesFromOneC extends HandledCommand
{
    protected $signature = 'oms:get-debts-files-from-one-c';

    protected $description = 'Скачивает файлы по долгам из 1С в папку';

    protected string $period = 'Каждый час';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        try {
            $response = Http::withBasicAuth('WebService', 'Vi7je7da')->post('https://1c.st-ing.com/prod_STI_usp/hs/USPServices/Universal', [
                'Method' => 'GetFilesOMS',
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            foreach ($data as $fileInfo) {
                $this->sendInfoMessage('Попытка скачать файл ' . $fileInfo['Name']);

                $path = storage_path('app/public/public/objects-debts-manuals/');
                file_put_contents(($path . $fileInfo['Name'] . '.' . $fileInfo['Extension']), base64_decode($fileInfo['Data']));

                $this->sendInfoMessage('Файл ' . $fileInfo['Name'] . ' успешно скачан');
            }
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось скачать файлы: "' . $e->getMessage());
            $this->endProcess();
            return 0;
        }

        $this->sendInfoMessage('Файлы успешно скачены');

        $this->endProcess();

        Artisan::call('oms:import-crm-money-registry-from-excel');

        Artisan::call('oms:import-contractor-debts-from-1c-excel');
        Artisan::call('oms:import-provider-debts-from-1c-excel');
        Artisan::call('oms:import-service-debts-from-1c-excel');

        return 0;
    }
}
