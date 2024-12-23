<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Services\CRM\MoneyRegistryService;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class ImportCRMMoneyRegistryFromExcel extends HandledCommand
{
    protected $signature = 'oms:import-crm-money-registry-from-excel';

    protected $description = 'Загружает зарплатные реестры в CRM из Excel';

    protected string $period = 'Каждый час';

    public function __construct(private MoneyRegistryService $moneyRegistryService)
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        $this->sendInfoMessage('Попытка разорхивировать файл с реестрами Payments.zip');

        $zip = new ZipArchive();
        $status = $zip->open(storage_path('app/public/public/objects-debts-manuals/Payments.zip'));
        if ($status !== true) {
            $this->sendErrorMessage('Не удалось разорхивировать файл Payments.zip: ' . $status);
        }

        try {
            $zip->extractTo(storage_path('app/public/public/crm-registry/'));
            $zip->close();
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось разорхивировать файл Payments.zip: ' . $e->getMessage());
        }

        $importedCount = 0;
        $files = Storage::files('public/crm-registry');

        $errors = [];

        foreach ($files as $file) {
            $fileName = File::name($file);
            $extension = File::extension($file);

            if (str_contains($fileName, 'imported')) {
                $this->sendInfoMessage('Файл "' . $fileName . '.' . $extension . '" содержит imported. Файл перемещен в imported.');
                Storage::move('public/crm-registry/' . $fileName . '.' . $extension, 'public/crm-registry/imported/' . $fileName . '__' . Str::random(5) . '.' . $extension);
                continue;
            }

            try {
                if ($this->moneyRegistryService->isRegistryWasImported($file)) {
                    $this->sendInfoMessage('Файл "' . $fileName . '.' . $extension . '" ранее уже был подгружен. Файл перемещен в imported.');
                    Storage::move('public/crm-registry/' . $fileName . '.' . $extension, 'public/crm-registry/imported/' . $fileName . '__' . Str::random(5) . '_imported.' . $extension);
                    continue;
                }

                $importErrors = $this->moneyRegistryService->importRegistry($file);

                if (count($importErrors) > 0) {
                    $errors[] = [
                        'file' => $fileName . '.' . $extension,
                        'employees' => $importErrors
                    ];

                    $this->sendErrorMessage('Файл ' . $fileName . '.' . $extension . ' не был загружен из за проблем с сотрудниками');
                    continue;
                }

                $importedCount++;
                Storage::move('public/crm-registry/' . $fileName . '.' . $extension, 'public/crm-registry/imported/' . $fileName . '__' . Str::random(5) . '_imported.' . $extension);

                $this->sendInfoMessage('Файл ' . $fileName . '.' . $extension . ' успешно загружен');
            } catch (\Exception $e) {
                $this->sendErrorMessage('Не удалось загрузить файл ' . $fileName . ' - ' . $e->getMessage());

                continue;
            }
        }

        if (count($errors) > 0) {
            try {
                Mail::send('emails.crm-registry.employees', compact('errors'), function ($m) {
                    $m->from('support@st-ing.com', 'OMS Support');
                    $m->subject('OMS. Ошибки в загрузке реестров для CRM');
                    $m->to('dmitry.samsonov@dttermo.ru');
                });
            } catch(Exception $e){
                $this->sendErrorMessage('Не удалось отправить уведомление об ошибке загрузки на email: "' . $e->getMessage());
            }

            $this->sendErrorMessage('Не все файлы были загружены успешно');
            $this->endProcess();

            return 0;
        }

        if ($importedCount === 0) {
            $this->sendInfoMessage('Файлы для загрузки не обнаружены');
        } else {
            $this->sendInfoMessage('Файлы успешно загружены');
        }

        $this->endProcess();

        return 0;
    }
}
