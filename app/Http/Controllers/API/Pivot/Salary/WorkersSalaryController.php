<?php

namespace App\Http\Controllers\API\Pivot\Salary;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WorkersSalaryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
//        if (! $request->has('verify_hash')) {
//            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
//        }
//
//        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
//            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
//        }

        $objectCodes = $request->get('object_code');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $worktypeNames = [1 => 'stroitelstvo', 2 => 'injeneriya', 4 => 'elektrika', 7 => 'sklad'];

        $info = [
            "workhours" => [
                'objects' => [],
                'total' => [
                    "average_hours_month" => 0,
                    "average_employees" => 0,
                    "hours_count" => 0,
                    "sum" => 0,
                    "average_rate" => 0,
                    "rates" => [],
                    "hours_month" => [],
                    "employees" => [],
                ],
            ]
        ];
        $workersSalaryPivot = Cache::get('workers_salary_pivot_data_excel', []);

        foreach ($workersSalaryPivot as $date => $a) {
            if ($date === 'total') {
                continue;
            }

            if (!is_null($startDate) && !is_null($endDate)) {
                if ($date < $startDate || $date > $endDate) {
                    continue;
                }
            }

            foreach ($a['objects'] as $code => $b) {
                if ($code === 'total') {
                    continue;
                }

                if (!is_null($objectCodes)) {
                    if (!in_array($code, $objectCodes)) {
                        continue;
                    }
                }

                foreach ($b['worktypes'] as $worktype => $c) {
                    if ($worktype === 'total') {
                        continue;
                    }

                    if (! isset($info[$worktypeNames[$worktype]])) {
                        $info[$worktypeNames[$worktype]] = [
                            'objects' => [],
                            'total' => [
                                "average_hours_month" => 0,
                                "average_employees" => 0,
                                "hours_count" => 0,
                                "sum" => 0,
                                "average_rate" => 0,
                                "rates" => [],
                                "hours_month" => [],
                                "employees" => [],
                            ],
                        ];
                    }

                    if (! isset($info[$worktypeNames[$worktype]]['objects'][$code])) {
                        $object = BObject::where('code', $code)->first();

                        if (!$object) {
                            continue;
                        }

                        $info[$worktypeNames[$worktype]]['objects'][$code] = [
                            "id" => $object->id,
                            "title" => $object->getName(),
                            "icon" => "",
                            "hours_count" => 0,
                            "sum" => 0,
                            "rates" => [],
                            "average_rate" => 0,
                            "employees" => [],
                            "average_employees" => 0,
                            "hours_month" => [],
                            "average_hours_month" => 0
                        ];
                    }

                    if (! isset($info['workhours']['objects'][$code])) {
                        $object = BObject::where('code', $code)->first();

                        if (!$object) {
                            continue;
                        }

                        $info['workhours']['objects'][$code] = [
                            "id" => $object->id,
                            "title" => $object->getName(),
                            "icon" => "",
                            "hours_count" => 0,
                            "sum" => 0,
                            "rates" => [],
                            "average_rate" => 0,
                            "employees" => [],
                            "average_employees" => 0,
                            "hours_month" => [],
                            "average_hours_month" => 0
                        ];
                    }

                    $info[$worktypeNames[$worktype]]['objects'][$code]['hours_count'] += $c['hours'];
                    $info[$worktypeNames[$worktype]]['objects'][$code]['sum'] += $c['amount'];
                    $info[$worktypeNames[$worktype]]['objects'][$code]['rates'][] = $c['rate'];
                    $info[$worktypeNames[$worktype]]['objects'][$code]['hours_month'][] = $c['hours'];
                    $info[$worktypeNames[$worktype]]['objects'][$code]['employees'][] = $c['employees'];

                    $info[$worktypeNames[$worktype]]['total']['hours_count'] += $c['hours'];
                    $info[$worktypeNames[$worktype]]['total']['sum'] += $c['amount'];
                    $info[$worktypeNames[$worktype]]['total']['rates'][] = $c['rate'];
                    $info[$worktypeNames[$worktype]]['total']['hours_month'][] = $c['hours'];
                    $info[$worktypeNames[$worktype]]['total']['employees'][] = $c['employees'];




                    $info['workhours']['objects'][$code]['hours_count'] += $c['hours'];
                    $info['workhours']['objects'][$code]['sum'] += $c['amount'];
                    $info['workhours']['objects'][$code]['rates'][] = $c['rate'];
                    $info['workhours']['objects'][$code]['hours_month'][] = $c['hours'];
                    $info['workhours']['objects'][$code]['employees'][] = $c['employees'];

                    $info['workhours']['total']['hours_count'] += $c['hours'];
                    $info['workhours']['total']['sum'] += $c['amount'];
                    $info['workhours']['total']['rates'][] = $c['rate'];
                    $info['workhours']['total']['hours_month'][] = $c['hours'];
                    $info['workhours']['total']['employees'][] = $c['employees'];
                }
            }
        }

        foreach ($info as $wk => $a) {
            $info[$wk]['total']['average_hours_month'] = count($a['total']['hours_month']) === 0 ? 0 : array_sum($a['total']['hours_month']) / count($a['total']['hours_month']);
            $info[$wk]['total']['average_employees'] = count($a['total']['employees']) === 0 ? 0 : array_sum($a['total']['employees']) / count($a['total']['employees']);
            $info[$wk]['total']['average_rate'] = count($a['total']['rates']) === 0 ? 0 : array_sum($a['total']['rates']) / count($a['total']['rates']);

            unset($info[$wk]['total']['hours_month'], $info[$wk]['total']['employees'], $info[$wk]['total']['rates']);

            foreach ($a['objects'] as $code => $b) {
                $info[$wk]['objects'][$code]['average_hours_month'] = count($b['hours_month']) === 0 ? 0 : array_sum($b['hours_month']) / count($b['hours_month']);
                $info[$wk]['objects'][$code]['average_employees'] = count($b['employees']) === 0 ? 0 : array_sum($b['employees']) / count($b['employees']);
                $info[$wk]['objects'][$code]['average_rate'] = count($b['rates']) === 0 ? 0 : array_sum($b['rates']) / count($b['rates']);

                unset($info[$wk]['objects'][$code]['hours_month'], $info[$wk]['objects'][$code]['employees'], $info[$wk]['objects'][$code]['rates']);
            }
        }

        return response()->json(compact('info'));
    }
}
