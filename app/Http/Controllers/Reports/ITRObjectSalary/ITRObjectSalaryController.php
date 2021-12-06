<?php

namespace App\Http\Controllers\Reports\ITRObjectSalary;

use App\Exports\ITRObjectSalaryExport;
use App\Http\Controllers\Controller;
use App\Imports\Reports\ITRObjectSalary\ITRObjectSalaryImport;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ITRObjectSalaryController extends Controller
{
    public function create(): View
    {
        return view('reports.itr-salary-object.create');
    }

    public function store(Request $request): BinaryFileResponse
    {
        $data = [];
        $objects = [];
        $importData = Excel::toArray(new ITRObjectSalaryImport(), $request->file('file'));

        $engineers = [];
        foreach ($importData['Инженеры'] as $engineer) {
            $engineers[trim($engineer[0])] = $engineer[1];
        }

        $projects = [];
        foreach ($importData['Проекты'] as $project) {
            $projects[trim($project[0])] = trim($project[1]);
        }

        $importData = $importData['Данные'];

        unset($importData[0]);
        unset($importData[1]);

        $dates = [];
        foreach ($importData[2] as $index => $date) {
            if ($index === 0 || is_null($date)) {
                continue;
            }
            $dates[$index] = $date;
        }

        unset($importData[2]);

        $currentEngineer = null;
        foreach ($importData as $rowIndex => $row) {

            if ($row[0] === 'Общий итог') {
                break;
            }

            if (array_key_exists($row[0], $engineers)) {
                $currentEngineer = $row[0];
                continue;
            }

            $object = $projects[$row[0]];

            if (! in_array($object, $objects)) {
                $objects[] = $object;
            }

            foreach ($row as $index => $percent) {
                if (isset($dates[$index])) {
                    $salary = $percent * $engineers[$currentEngineer];
                    $tax = $salary * 0.43;
                    $nds = ($tax + $salary) * 0.2;

                    if (!isset($data[$dates[$index]][$object]['percent'])) {
                        $data[$dates[$index]][$object]['percent'] = 0;
                        $data[$dates[$index]][$object]['salary'] = 0;
                        $data[$dates[$index]][$object]['tax'] = 0;
                        $data[$dates[$index]][$object]['nds'] = 0;
                    }

                    $data[$dates[$index]][$object]['percent'] += $percent * 100;
                    $data[$dates[$index]][$object]['salary'] += $salary;
                    $data[$dates[$index]][$object]['tax'] += $tax;
                    $data[$dates[$index]][$object]['nds'] += $nds;
                }
            }
        }

        return Excel::download(new ITRObjectSalaryExport($objects, $data), 'Сводная ИТР зарплат по объектам.xlsx');
    }
}
