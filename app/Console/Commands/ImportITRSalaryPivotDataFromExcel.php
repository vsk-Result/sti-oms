<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Imports\ITRSalaryPivotImport;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class ImportITRSalaryPivotDataFromExcel extends HandledCommand
{
    protected $signature = 'oms:import-itr-salary-pivot-data-from-excel';

    protected $description = 'Загружает расходы на зарплаты ИТР из Excel';

    protected string $period = 'Вручную';

    public function handle()
    {
//        if ($this->isProcessRunning()) {
//            return 0;
//        }
//
//        $this->startProcess();

        try {

            $filename = storage_path() . '/app/public/public/objects-debts-manuals/itr_salary_pivot_table.xlsx';
            $importData = Excel::toArray(new ITRSalaryPivotImport(), new UploadedFile($filename, 'itr_salary_pivot_table.xlsx'));
            $importData = $importData['История'];

            $info = [];

            $currentDate = null;

            foreach ($importData as $index => $row) {
                if ($index < 6 || $row[0] === 'Итого') {
                    continue;
                }

                $isDate = substr($row[0], 0, 2) === '01';

                if ($isDate) {
                    $currentDate = Carbon::parse($row[0])->format('Y-m-d');
                    $info[$currentDate] = [
                        'objects' => [],
                        'total' => 0,
                    ];
                } else {
                    $objectCode = $this->getObjectCodeFromName($row[0]);

                    if (is_null($objectCode)) {
                        $this->sendErrorMessage('Не найден объект => ' . $row[0]);
                        continue;
                    }

                    if (! isset($info[$currentDate]['objects'][$objectCode])) {
                        $info[$currentDate]['objects'][$objectCode] = 0;
                    }

                    $info[$currentDate]['objects'][$objectCode] += -$row[3];
                    $info[$currentDate]['total'] += -$row[3];
                }
            }
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось загрузить сводную по зарплатам ИТР из Excel');
            $this->sendErrorMessage($e->getMessage());
            $this->endProcess();
            return 0;
        }

        Cache::put('itr_salary_pivot_data_excel', $info);

        $this->sendInfoMessage('Файл успешно загружен');

//        $this->endProcess();

        return 0;
    }

    public function getObjectCodeFromName(string $name): string | null
    {
        return [
            'Аэрофлот' => '365',
            'Валента' => '366',
            'ГЭС-2' => '288',
            'Завидово Меркури' => '358',
            'Камчатка' => '363',
            'Кемерово' => '361',
            'Малый Сухаревский' => '353',
            'Октафарма' => '346',
            'Офис' => '27.1',
            'Офис Трехпрудный переулок' => '27.1',
            'Тинькофф' => '360',
            'Mono Space' => '369',
            'Веспер' => '372',
            'Склад' => '27.1',
            'Детский центр Магнитогорск' => '373',
            'GRAND LINE' => '374',
            'Гольф-клуб Завидово' => '364',
            'ПСБ-ЗИЛ' => '371',
            'Сбер К32' => '376',
            'Бюро 1440' => '377',
            'Валента СМР' => '370',
            'Веспер Тверская' => '372',
            'Офис Stone Towers' => '379',
            'Офис Велесстрой' => '378',
            'ТМХ' => '375',
            'Левенсон' => '380',
            'Шишкино' => '383',
            'ЦОД' => '382',
        ][$name] ?? null;
    }
}
