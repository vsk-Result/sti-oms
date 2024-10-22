<?php

namespace App\Console\Commands;

use App\Services\CRONProcessService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetDebtsFilesFromOneC extends Command
{
    protected $signature = 'oms:get-debts-files-from-one-c';

    protected $description = 'Скачивает файлы по долгам из 1С в папку';

    public function __construct(CRONProcessService $CRONProcessService)
    {
        parent::__construct();
        $this->CRONProcessService = $CRONProcessService;
        $this->CRONProcessService->createProcess(
            $this->signature,
            $this->description,
            'Каждый час'
        );
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Скачивание файлов по долгам из 1С в папку');

        try {
            $response = Http::withBasicAuth('WebService', 'Vi7je7da')->post('https://1c.st-ing.com/prod_STI_usp/hs/USPServices/Universal', [
                'Method' => 'GetFilesOMS',
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            foreach ($data as $fileInfo) {
                Log::channel('custom_imports_log')->debug('Попытка скачать файл ' . $fileInfo['Name']);

                $path = storage_path('app/public/public/objects-debts-manuals/');
                file_put_contents(($path . $fileInfo['Name'] . '.' . $fileInfo['Extension']), base64_decode($fileInfo['Data']));

                Log::channel('custom_imports_log')->debug('Файл ' . $fileInfo['Name'] . ' успешно скачан');
            }
        } catch (\Exception $e) {
            $errorMessage = '[ERROR] Не удалось скачать файлы: "' . $e->getMessage();
            Log::channel('custom_imports_log')->debug($errorMessage);
            $this->CRONProcessService->failedProcess($this->signature, $errorMessage);
            return 0;
        }

        Log::channel('custom_imports_log')->debug('[SUCCESS] Файлы успешно скачены');
        $this->CRONProcessService->successProcess($this->signature);

        Artisan::call('oms:contractor-debts-from-excel');
        Artisan::call('oms:service-debts-from-excel');
        Artisan::call('oms:import-crm-money-registry-from-excel');

        return 0;
    }
}
