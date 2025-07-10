<?php

namespace App\Http\Controllers\Pivot\CalculateWorkersCost;

use App\Http\Controllers\Controller;
use App\Imports\ITRSalaryPivotImport;
use App\Models\Object\BObject;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ITRSalaryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $file = $request->file('file');

        // Закинем в папку для ручных загрузок
        Storage::putFileAs('public/objects-debts-manuals', $file, 'itr_salary_pivot_table.xlsx');

        // Запустим крон задачу на импорт долгов по подрядчикам
//        Artisan::call('oms:import-itr-salary-pivot-data-from-excel');

        try {

            $filename = storage_path() . '/app/public/public/objects-debts-manuals/itr_salary_pivot_table.xlsx';
            $importData = Excel::toArray(new ITRSalaryPivotImport(), new UploadedFile($filename, 'itr_salary_pivot_table.xlsx'));
            $importData = $importData['История'];

            $info = [];

            $currentDate = null;
            $failedObjects = [];

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
                        $obj = BObject::where('code', $objectCode)->first();

                        if (!$obj) {
                            $failedObjects[] = $row[0];
                            continue;
                        }

                        $objectCode = $obj->code;
                    }

                    if (! isset($info[$currentDate]['objects'][$objectCode])) {
                        $info[$currentDate]['objects'][$objectCode] = 0;
                    }

                    $info[$currentDate]['objects'][$objectCode] += -$row[3];
                    $info[$currentDate]['total'] += -$row[3];
                }
            }
        } catch (\Exception $e) {
            session()->flash('import_itr_salary_status_color', 'danger');
            session()->flash('import_itr_salary_status', 'Не удалось загрузить сводную по зарплатам ИТР из Excel');
            return redirect()->back();
        }

        if (count($failedObjects) > 0) {
            session()->flash('import_itr_salary_status_color', 'danger');
            session()->flash('import_itr_salary_status', 'Объекты не найдены: ' . implode(', ', $failedObjects));
            return redirect()->back();
        }

        Cache::put('itr_salary_pivot_data_excel', $info);

        session()->flash('import_itr_salary_status_color', 'success');
        session()->flash('import_itr_salary_status', 'Файл успешно загружен');

        return redirect()->back();
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
        ][$name] ?? null;
    }
}
