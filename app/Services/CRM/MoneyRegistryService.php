<?php

namespace App\Services\CRM;

use App\Imports\CRM\MoneyRegistry\Import;
use App\Models\CRM\Avans;
use App\Models\CRM\AvansImport;
use App\Models\CRM\AvansImportItem;
use App\Models\CRM\Employee;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;

class MoneyRegistryService
{
    public function isRegistryWasImported(UploadedFile | string $file): bool
    {
        $importData = Excel::toArray(new Import(), $file);
        $data = $this->getDataFromImport($importData['Лист_1']);
        $isImported = false;
        $avansImports = AvansImport::where('date', $data['date'])->with('items', 'items.avans')->get();
        foreach ($avansImports as $avansImport) {
            if ($data['total'] == $avansImport->getTotal() && $data['importType'] == $avansImport->type) {
                $isImported = true;
                break;
            }
        }

        return $isImported;
    }

    public function importRegistry(UploadedFile | string $file): array
    {
        $errors = [];

        $importData = Excel::toArray(new Import(), $file);
        $data = $this->getDataFromImport($importData['Лист_1']);
        $paydate = $this->getPaydate($data['payMonth']);

        foreach ($data['employees'] as $employeeInfo) {
            $uid = substr($employeeInfo['uid'], -5);
            $employee = Employee::where('unique', $uid)->first();

            if (! $employee) {
                $errors[] = [
                    'uid' => $employeeInfo['uid'],
                    'name' => $employeeInfo['name']
                ];
            }
        }

        if (count($errors) > 0) {
            return $errors;
        }

        $import = new AvansImport();
        $import->user_id = 1;
        $import->date = $data['date'];
        $import->paydate = $paydate;
        $import->file = '';
        $import->type = $data['importType'];
        $import->company_id = 1;
        $import->description = $data['payMonth'];
        $import->save();

        foreach ($data['employees'] as $employeeInfo) {
            $uid = substr($employeeInfo['uid'], -5);
            $employee = Employee::where('unique', $uid)->first();

            $code = $employeeInfo['code'];
            if (is_null($code)) {
                $code = '27.1';
                if ($employee->object) {
                    $code = $employee->object->code;
                } else {
                    $workhour = $employee->workhours()->orderBy('date', 'DESC')->first();
                    if ($workhour) {
                        $code = $workhour->object->code;
                    }
                }
            }

            $avans = new Avans();
            $avans->e_id = $employee->id;
            $avans->u_id = 1;
            $avans->type = 'Карты';
            $avans->date = $paydate;
            $avans->code = $code;
            $avans->issue_date = Carbon::now();
            $avans->value = $employeeInfo['amount'];
            $avans->is_read = false;
            $avans->finance_flag = false;
            $avans->is_engeneer = $employeeInfo['type'] !== 'Рабочий';
            $avans->save();

            $item = new AvansImportItem();
            $item->import_id = $import->id;
            $item->employee_id = $employee->id;
            $item->avans_id = $avans->id;
//            $item->description = $employeeInfo['type'];
            $item->save();
        }

        return [];
    }

    public function getDataFromImport(array $importData): array
    {
        $date = Carbon::parse(mb_substr($importData[3][0], 3, 10))->format('Y-m-d');
        $payMonth = $importData[4][2];
        $importType = $importData[5][2];

        $total = 0;
        $employees = [];
        foreach ($importData as $data) {
            if (!str_contains($data[3], '#')) {
                continue;
            }

            $employees[] = [
                'uid' => trim($data[3]),
                'name' => $data[4],
                'amount' => $data[11],
                'type' => $data[13],
                'code' => $data[15],
            ];

            $total += $data[11];
        }

        return compact('date', 'payMonth', 'importType', 'employees', 'total');
    }

    public function getPaydate(string $payMonth): string
    {
        $months = [
            'Январь' => '01',
            'Февраль' => '02',
            'Март' => '03',
            'Апрель' => '04',
            'Май' => '05',
            'Июнь' => '06',
            'Июль' => '07',
            'Август' => '08',
            'Сентябрь' => '09',
            'Октябрь' => '10',
            'Ноябрь' => '11',
            'Декабрь' => '12',
        ];

        $paydate = mb_substr($payMonth, -4);
        $mon = mb_substr($payMonth, 0, mb_strpos($payMonth, ' '));
        foreach ($months as $month => $monthNum) {
            if ($mon === $month) {
                $paydate = $paydate . '-' . $monthNum;
                break;
            }
        }

        return $paydate;
    }
}
