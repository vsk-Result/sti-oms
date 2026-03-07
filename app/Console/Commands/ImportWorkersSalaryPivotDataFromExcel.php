<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Imports\ITRSalaryPivotImport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class ImportWorkersSalaryPivotDataFromExcel extends HandledCommand
{
    protected $signature = 'oms:import-workers-salary-pivot-data-from-excel';

    protected $description = 'Загружает расходы на зарплаты рабочих из Excel';

    protected string $period = 'Каждый час';

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        try {

            $filename = storage_path() . '/app/public/public/objects-debts-manuals/Отчет по начисленным зп для рабочих.xlsx';
            $importData = Excel::toArray(new ITRSalaryPivotImport(), new UploadedFile($filename, 'Отчет по начисленным зп для рабочих.xlsx'));
            $importData = $importData['Лист_1'];
            $info = [];

            $currentDate = null;
            $currentObject = null;

            foreach ($importData as $index => $row) {
                if ($index < 5) {
                    continue;
                }

                if ($row[0] === 'Итого') {
                    $info['total'] = [
                        'employees' => empty($row[5]) ? 0 : $row[5],
                        'hours' => empty($row[6]) ? 0 : $row[6],
                        'amount' => empty($row[7]) ? 0 : $row[7],
                        'rate' => $row[9] === 'Деление на 0' ? 0 : $row[9],
                    ];

                    continue;
                }

                if (! empty($row[0])) {
                    $currentDate = get_date_and_month_from_string($row[0], true);

                    $info[$currentDate] = [
                        'objects' => [],
                        'total' => [
                            'employees' => empty($row[5]) ? 0 : $row[5],
                            'hours' => empty($row[6]) ? 0 : $row[6],
                            'amount' => empty($row[7]) ? 0 : $row[7],
                            'rate' => $row[9] === 'Деление на 0' ? 0 : $row[9],
                        ],
                    ];

                } else {
                    if (!empty($row[3])) {
                        $currentObject = $row[3] == 27 ? '27.1' : $row[3];

                        $info[$currentDate]['objects'][$currentObject] = [
                            'worktypes' => [],
                            'total' => [
                                'employees' => empty($row[5]) ? 0 : $row[5],
                                'hours' => empty($row[6]) ? 0 : $row[6],
                                'amount' => empty($row[7]) ? 0 : $row[7],
                                'rate' => $row[9] === 'Деление на 0' ? 0 : $row[9],
                            ]
                        ];

                    } else {
                        $info[$currentDate]['objects'][$currentObject]['worktypes'][$row[4]] = [
                            'employees' => empty($row[5]) ? 0 : $row[5],
                            'hours' => empty($row[6]) ? 0 : $row[6],
                            'amount' => empty($row[7]) ? 0 : $row[7],
                            'rate' => $row[9] === 'Деление на 0' ? 0 : $row[9],
                        ];
                    }
                }

            }
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось загрузить сводную по зарплатам рабочих из Excel');
            $this->sendErrorMessage($e->getMessage());
            $this->endProcess();
            return 0;
        }

        Cache::put('workers_salary_pivot_data_excel', $info);

        $this->sendInfoMessage('Файл успешно загружен');

        $this->endProcess();

        return 0;
    }
}
