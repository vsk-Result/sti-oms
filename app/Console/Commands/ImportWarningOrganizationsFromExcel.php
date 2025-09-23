<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Helpers\Sanitizer;
use App\Imports\Organization\WarningOrganizationImport;
use App\Models\Object\BObject;
use App\Services\OrganizationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class ImportWarningOrganizationsFromExcel extends HandledCommand
{
    protected $signature = 'oms:import-warning-organizations-data-from-1c-excel';

    protected $description = 'Загружает организации с проблемами из Excel';

    protected string $period = 'Вручную';

    public function __construct(Sanitizer $sanitizer)
    {
        parent::__construct();
        $this->sanitizer = $sanitizer;
    }

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        try {

            $info = [];

            $filename = storage_path() . '/app/public/public/objects-debts-manuals/warning_organizations.xls';
            $importData = Excel::toArray(new WarningOrganizationImport(), new UploadedFile($filename, 'warning_organizations.xls'));
            $sudebnieData = $importData['судебные'];
            unset($sudebnieData[0], $sudebnieData[1]);
            foreach ($sudebnieData as $row) {
                if (empty($row[0])) {
                    continue;
                }

                $type = 'Судебные разбирательства';
                $organizationName = trim($row[4] ?? '');
                $inn = trim($row[5] ?? '');
                $amount = $this->sanitizer->set($row[7] ?? 0)->toAmount()->get();

                $info[] = compact('type', 'organizationName', 'inn', 'amount');
            }


            $bankrotData = $importData['банкротные '];
            unset($bankrotData[0]);

            foreach ($bankrotData as $row) {
                if (empty($row[0])) {
                    continue;
                }

                $type = 'Банкротные разбирательства';
                $organizationName = trim($row[4] ?? '');
                $explodeInn = explode(' ', trim($row[5] ?? ''));
                $inn = $explodeInn[count($explodeInn) - 1] ?? '';
                $amount = $this->sanitizer->set($row[7] ?? 0)->toAmount()->get();

                $info[] = compact('type', 'organizationName', 'inn', 'amount');
            }


            $debitorkData = $importData['претензии по дебиторке'];
            unset($debitorkData[0]);

            foreach ($debitorkData as $row) {
                if (empty($row[0])) {
                    continue;
                }

                $type = 'Претензии по дебиторке';
                $organizationName = trim($row[1] ?? '');
                $inn = trim($row[4] ?? '');
                $amount = $this->sanitizer->set($row[3] ?? 0)->toAmount()->get();

                $info[] = compact('type', 'organizationName', 'inn', 'amount');
            }


            $isplolnData = $importData['исп.листы'];
            unset($isplolnData[0], $isplolnData[1]);

            foreach ($isplolnData as $row) {
                if (empty($row[0])) {
                    continue;
                }

                $type = 'Исполнительный лист';
                $organizationName = trim($row[4] ?? '');

                $explodeInn = explode(' ', trim($row[5] ?? ''));
                $inn = $explodeInn[count($explodeInn) - 1] ?? '';
                $amount = $this->sanitizer->set($row[7] ?? 0)->toAmount()->get();

                $info[] = compact('type', 'organizationName', 'inn', 'amount');
            }
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось загрузить организации с проблемами');
            $this->sendErrorMessage($e->getMessage());
            $this->endProcess();
            return 0;
        }

        Cache::put('warning_organizations_data', $info);

        $this->sendInfoMessage('Файл успешно загружен');

        $this->endProcess();

        return 0;
    }
}
