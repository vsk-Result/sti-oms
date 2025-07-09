<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Imports\DebtImport\ContractorImportFrom1C as ContractorExcelImport;
use App\Models\Organization;
use App\Models\Status;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;

class ImportAndSyncOrganizationDataFrom1CExcel extends HandledCommand
{
    protected $signature = 'oms:import-and-sync-organization-data-from-1c-excel';

    protected $description = 'Сихнронизирует контрагентов из 1С из Excel';

    protected string $period = 'Вручную';

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        $organizationsBefore = Organization::count();
        $syncByInn = 0;
        $syncByInn2 = 0;
        $syncByShortName = 0;
        $syncByFullName = 0;
        $created = 0;
        $exist = 0;

        try {
            $filename = storage_path() . '/app/public/public/objects-debts-manuals/sync_organizations.xlsx';
            $importData = Excel::toArray(new ContractorExcelImport(), new UploadedFile($filename, 'sync_organizations.xlsx'));
            $importData = $importData['TDSheet'];

            unset($importData[0]);

            foreach (array_chunk($importData, 500) as $organizations) {
                foreach ($organizations as $row) {
                    if (empty($row[0])) {
                        continue;
                    }

                    $shortName = $row[0];
                    $fullName = $row[2];
                    $inn = $row[3];
                    $kpp = $row[4];

                    $organizationExist = Organization::where('name', $shortName)->where('inn', $inn)->first();

                    if ($organizationExist) {
                        $exist++;
                        continue;
                    }

                    $organizationByShortName = Organization::where('name', $shortName)->first();
                    $organizationByFullName = Organization::where('name', $fullName)->first();
                    $organizationByINN = Organization::where('inn', $inn)->first();
                    $organizationByINN2 = Organization::where('inn', '0' . $inn)->first();

                    $newName = empty($shortName) ? $fullName : $shortName;

                    if (empty($newName)) {
                        continue;
                    }

                    if ($organizationByINN) {
                        $organizationByINN->update(['name' => $newName]);
                        $syncByInn++;
                        continue;
                    }

                    if ($organizationByINN2) {
                        $organizationByINN2->update(['name' => $newName, 'inn' => '0' . $inn]);
                        $syncByInn2++;
                        continue;
                    }

                    if ($organizationByShortName) {
                        $organizationByShortName->update(['name' => $newName, 'inn' => $inn, 'kpp' => $kpp]);
                        $syncByShortName++;
                        continue;
                    }

                    if ($organizationByFullName) {
                        $organizationByFullName->update(['name' => $newName, 'inn' => $inn, 'kpp' => $kpp]);
                        $syncByFullName++;
                        continue;
                    }

                    Organization::create([
                        'company_id' => null,
                        'category' => '',
                        'name' => $newName,
                        'inn' => $inn,
                        'kpp' => $kpp,
                        'status_id' => Status::STATUS_ACTIVE,
                        'nds_status_id' => Organization::NDS_STATUS_AUTO,
                    ]);
                    $created++;
                }
            }
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось синхронизировать контрагентов из 1С');
            $this->sendErrorMessage($e->getMessage());
            $this->endProcess();

            $organizationsAfter = Organization::count();

            dd($organizationsBefore, $syncByInn, $syncByInn2, $syncByShortName, $syncByFullName, $created, $exist, $organizationsAfter, $e->getMessage());
            return 0;
        }

        $this->sendInfoMessage('Файл успешно загружен');

        $this->endProcess();

        $organizationsAfter = Organization::count();

        dd($organizationsBefore, $syncByInn, $syncByInn2, $syncByShortName, $syncByFullName, $created, $exist, $organizationsAfter);

        return 0;
    }
}
