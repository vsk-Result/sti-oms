<?php

namespace App\Http\Controllers\Pivot\CalculateWorkersCost;

use App\Http\Controllers\Controller;
use App\Imports\Pivot\CalculateWorkersCost\NDFL\Import;
use App\Models\Object\BObject;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class NDFLController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $file = $request->file('file');

        // Закинем в папку для ручных загрузок
        Storage::putFileAs('public/objects-debts-manuals', $file, 'workers_costs_ndfl.xlsx');

        try {
            $filename = storage_path() . '/app/public/public/objects-debts-manuals/workers_costs_ndfl.xlsx';
            $importData = Excel::toArray(new Import(), new UploadedFile($filename, 'workers_costs_ndfl.xlsx'));
            $importData = $importData['Лист_1'];

            $date = mb_substr($importData[1][0], mb_strpos($importData[1][0], ': ') + 2);
            $date = str_replace(' г.', '', $date);
            $date = get_date_and_month_from_string($date);
            $date = Carbon::parse('01-' . $date)->format('Y-m');

            $info[$date] = [
                'objects' => [],
                'total' => 0,
            ];

            $failedObjects = [];

            foreach ($importData as $index => $row) {
                if ($index < 4 || $row[1] === 'Итого:') {
                    continue;
                }

                $objectCode = $row[1];
                $amount = -$row[2];

                if ($objectCode == '27') {
                    $objectCode = '27.1';
                }

                $obj = BObject::where('code', $objectCode)->first();

                if (! $obj) {
                    $failedObjects[] = $objectCode;
                    continue;
                }

                if (! isset($info[$date]['objects'][$objectCode])) {
                    $info[$date]['objects'][$objectCode] = 0;
                }

                $info[$date]['objects'][$objectCode] += $amount;
                $info[$date]['total'] += $amount;
            }
        } catch (\Exception $e) {
            session()->flash('import_itr_salary_status_color', 'danger');
            session()->flash('import_itr_salary_status', 'Не удалось загрузить расходы по ндфл или взносам из Excel');
            return redirect()->back();
        }

        if (count($failedObjects) > 0) {
            session()->flash('import_itr_salary_status_color', 'danger');
            session()->flash('import_itr_salary_status', 'Объекты не найдены: ' . implode(', ', $failedObjects));
            return redirect()->back();
        }

        $currentInfo = Cache::get('ndfl_pivot_data_excel', []);

        $currentInfo['ndfl'][$date] = $info[$date];

        Cache::put('ndfl_pivot_data_excel', $currentInfo);

        session()->flash('import_itr_salary_status_color', 'success');
        session()->flash('import_itr_salary_status', 'Файл успешно загружен');

        return redirect()->back();
    }
}
