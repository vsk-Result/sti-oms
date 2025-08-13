<?php

namespace App\Http\Controllers\Pivot\CalculateWorkersCost;

use App\Http\Controllers\Controller;
use App\Imports\Pivot\CalculateWorkersCost\NDFL\Import;
use App\Models\CurrencyExchangeRate;
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

        Storage::putFileAs('public/objects-debts-manuals', $file, 'workers_costs_ndfl.xlsx');

        try {
            $filename = storage_path() . '/app/public/public/objects-debts-manuals/workers_costs_ndfl.xlsx';
            $importData = Excel::toArray(new Import(), new UploadedFile($filename, 'workers_costs_ndfl.xlsx'));
            $importData = $importData['Лист_1'];

            $isNdfl = str_contains($importData[0][0], 'НДФЛ');

            if ($isNdfl) {
                $info = $this->ndflLoader($importData);
            } else {
                $info = $this->vznosLoader($importData);
            }
        } catch (\Exception $e) {
            session()->flash('import_itr_salary_status_color', 'danger');
            session()->flash('import_itr_salary_status', 'Не удалось загрузить расходы по ндфл или взносам из Excel');
            return redirect()->back();
        }

        if (count($info['failedObjects']) > 0) {
            session()->flash('import_itr_salary_status_color', 'danger');
            session()->flash('import_itr_salary_status', 'Объекты не найдены: ' . implode(', ', $info['failedObjects']));
            return redirect()->back();
        }

        $date = $info['date'];
        $currentInfo = Cache::get('ndfl_pivot_data_excel', []);

        $key = $isNdfl ? 'ndfl' : 'strah';

        $currentInfo[$key][$date] = $info['info'][$date];

        Cache::put('ndfl_pivot_data_excel', $currentInfo);

        session()->flash('import_itr_salary_status_color', 'success');
        session()->flash('import_itr_salary_status', 'Файл успешно загружен на сумму ' . CurrencyExchangeRate::format($info['info'][$date]['total']));

        return redirect()->back();
    }

    public function ndflLoader(array $data): array
    {
        $date = mb_substr($data[1][0], mb_strpos($data[1][0], ': ') + 2);
        $date = str_replace(' г.', '', $date);
        $date = get_date_and_month_from_string($date);
        $date = Carbon::parse('01-' . $date)->format('Y-m');

        $info[$date] = [
            'objects' => [],
            'total' => 0,
        ];

        $failedObjects = [];
        foreach ($data as $index => $row) {
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

        return [
            'failedObjects' => $failedObjects,
            'info' => $info,
            'date' => $date,
        ];
    }

    public function vznosLoader(array $data): array
    {
        $preparedInfo = $this->prepareVznosInfo($data);
        $groupedInfo = $this->groupVznosInfo($preparedInfo);

        $date = mb_substr($data[4][0], mb_strpos($data[4][0], ': ') + 2);
        $date = str_replace(' г.', '', $date);
        $date = get_date_and_month_from_string($date);
        $date = Carbon::parse('01-' . $date)->format('Y-m');

        $info[$date] = [
            'objects' => [],
            'total' => 0,
        ];

        $failedObjects = [];
        foreach ($groupedInfo as $objectCode => $am) {
            $amount = -$am;

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

        return [
            'failedObjects' => $failedObjects,
            'info' => $info,
            'date' => $date,
        ];
    }

    public function prepareVznosInfo(array $data): array
    {
        $result = [];

        foreach ($data as $index => $info) {
            if ($index < 13 || $info[0] === 'Итого') {
                continue;
            }

            $objectCode = empty($info[1]) ? '27.1' : $info[1];
            $amount = ($info[9] ?? 0) + ($info[10] ?? 0) + ($info[11] ?? 0) + ($info[12] ?? 0) + ($info[13] ?? 0) + ($info[14] ?? 0) + ($info[15] ?? 0) + ($info[16] ?? 0) + ($info[17] ?? 0) + ($info[18] ?? 0) + ($info[19] ?? 0) + ($info[20] ?? 0);

            if (! isset($result[$objectCode])) {
                $result[$objectCode] = 0;
            }

            $result[$objectCode] += $amount;
        }

        ksort($result);

        return $result;
    }

    public function groupVznosInfo(array $data): array
    {
        $result = [];

        foreach ($data as $code => $amount) {
            if (isset($codesWithoutWorktype[$code])) {
                $objectCode = $codesWithoutWorktype[$code];
            } else {
                $objectCode = explode('.', $code)[0];
            }

            if ($objectCode == 27) {
                $objectCode = '27.1';
            }

            if (! isset($result[$objectCode])) {
                $result[$objectCode] = 0;
            }

            $result[$objectCode] += $amount;
        }

        return $result;
    }
}
