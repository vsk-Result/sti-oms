<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Helpers\Sanitizer;
use App\Imports\Organization\WarningOrganizationFineImport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class ImportWarningOrganizationsFinesFromExcel extends HandledCommand
{
    protected $signature = 'oms:import-warning-organizations-fines-data-from-excel';

    protected $description = 'Загружает организации с проблемами по штрафам из Excel';

    protected string $period = 'Ежедневно в 21:00';

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

        $objectCodesToImport = ['380', '376', '363', '361'];
        $info = [];

        foreach ($objectCodesToImport as $objectCode) {
            try {

                $filename = storage_path() . '/app/public/public/objects-debts-manuals/' . $objectCode . '_fine.xls';
                $importData = Excel::toArray(new WarningOrganizationFineImport(), new UploadedFile($filename, $objectCode . '_fine.xls'));

                $data = $importData['Реестр штрафных санкций'];

                unset($data[0], $data[1]);

                foreach ($data as $row) {
                    if (empty($row[2])) {
                        continue;
                    }

                    $type = 'Штрафные санкции';
                    $organizationName = trim($row[2] ?? '');
                    $inn = '';
                    $amount = $this->sanitizer->set($row[5] ?? 0)->toAmount()->get();
                    $filename = $objectCode . '_fine.xls';

                    $info[] = compact('type', 'organizationName', 'inn', 'amount', 'filename');
                }
            } catch (\Exception $e) {
                $this->sendErrorMessage('Не удалось загрузить организации с проблемами по штрафам');
                $this->sendErrorMessage($e->getMessage());
                continue;
            }
        }

        Cache::put('warning_organizations_fine_data', $info);
        $this->sendInfoMessage('Файл успешно загружен');

        $this->endProcess();

        return 0;
    }
}
