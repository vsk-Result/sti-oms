<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Imports\DebtImport\ContractorImportFrom1C as ContractorExcelImport;
use App\Models\Object\BObject;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class ImportCFDataFrom1CExcel extends HandledCommand
{
    protected $signature = 'oms:import-cf-data-from-1c-excel';

    protected $description = 'Загружает неоплаченные счета для CF из 1С из Excel';

    protected string $period = 'Вручную';

    public function handle()
    {
//        if ($this->isProcessRunning()) {
//            return 0;
//        }
//
//        $this->startProcess();

        try {

            $filename = storage_path() . '/app/public/public/objects-debts-manuals/Cash flow(XLSX).xlsx';
            $importData = Excel::toArray(new ContractorExcelImport(), new UploadedFile($filename, 'Cash flow(XLSX).xlsx'));
            $importData = $importData['TDSheet'];

            unset($importData[0]);

            $info = [];

            foreach ($importData as $row) {
                if (empty($row[0])) {
                    continue;
                }

                $group = trim($row[4] ?? '');
                $date = trim($row[14] ?? '');
                $amount = $row[9] ?? 0;
                $objectCode = trim($row[10] ?? '');
                $materialType = trim($row[19] ?? '');

                if (empty($amount) || empty($objectCode) || ! is_valid_amount_in_range($amount)) {
                    continue;
                }

                $object = BObject::where('code', $objectCode)->first();

                if (! $object) {
                    continue;
                }

                if (! isset($info[$object->id])) {
                    $info[$object->id] = [
                        'contractors' => [],
                        'providers_fix' => [],
                        'providers_float' => [],
                        'service' => [],
                    ];
                }

                if ($group === 'РАБОТЫ') {
                    $info[$object->id]['contractors'][] = [
                        'date' => $date,
                        'amount' => -$amount,
                    ];
                }

                if ($group === 'МАТЕРИАЛЫ') {
                    if ($materialType === 'Изменяемая часть') {
                        $info[$object->id]['providers_float'][] = [
                            'date' => $date,
                            'amount' => -$amount,
                        ];
                    } else {
                        $info[$object->id]['providers_fix'][] = [
                            'date' => $date,
                            'amount' => -$amount,
                        ];
                    }
                }

                if ($group === 'НАКЛАДНЫЕ/УСЛУГИ') {
                    $info[$object->id]['service'][] = [
                        'date' => $date,
                        'amount' => -$amount,
                    ];
                }
            }
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось загрузить неоплаченные счета CF из 1С');
            $this->sendErrorMessage($e->getMessage());
            $this->endProcess();
            return 0;
        }

        Cache::put('cash_flow_1c_data', $info);

        $this->sendInfoMessage('Файл успешно загружен');

        $this->endProcess();

        return 0;
    }
}
