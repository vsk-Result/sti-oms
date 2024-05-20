<?php

namespace App\Http\Controllers\API\Pivot\Object;

use App\Http\Controllers\Controller;
use App\Models\FinanceReportHistory;
use App\Models\Object\BObject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ObjectInfoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (! $request->has('verify_hash')) {
            abort(403);
            return response()->json([], 403);
        }

        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
            abort(403);
            return response()->json([], 403);
        }
        $financeReportHistory = FinanceReportHistory::where('date', now()->format('Y-m-d'))->first();

        $objectsInfo = json_decode($financeReportHistory->objects_new);
        $years = collect($objectsInfo->years)->toArray();
        $total = $objectsInfo->total;

        $info = [
            'debts' => [],
            'total' => [
                'contractors_debts' => 0,
                'contractors_debts_gu' => 0,
                'providers_debts' => 0,
                'service_debts' => 0,
                'salary_debts' => 0,
                'total_debts' => 0,
            ]
        ];
        $objects = BObject::active()->orderBy('code')->get();

        foreach ($objects as $object) {
            $inf = [];

            foreach ($years as $year => $objects) {
                foreach ($objects as $o) {
                    if ($o->id === $object->id) {
                        $inf = (array) $total->{$year}->{$object->code};
                        break;
                    }
                }
            }

            $contractorDebtsAmount = $inf['contractor_debt'];
            $contractorDebtsAmountGU = $inf['contractor_debt_gu'];
            $providerDebtsAmount = $inf['provider_debt'];
            $serviceDebtsAmount = $inf['service_debt'];
            $salaryDebtsAmount = $inf['workers_salary_debt'];
            $totalDebts = $contractorDebtsAmount + $providerDebtsAmount + $serviceDebtsAmount + $contractorDebtsAmountGU + $salaryDebtsAmount;

            $info['debts'][] = [
                'id' => $object->id,
                'object_name' => $object->getName(),
                'contractors_debts' => $contractorDebtsAmount,
                'contractors_debts_gu' => $contractorDebtsAmountGU,
                'providers_debts' => $providerDebtsAmount,
                'service_debts' => $serviceDebtsAmount,
                'salary_debts' => $salaryDebtsAmount,
                'total_debts' => $totalDebts,
            ];

            $info['total']['contractors_debts'] += $contractorDebtsAmount;
            $info['total']['contractors_debts_gu'] += $contractorDebtsAmountGU;
            $info['total']['providers_debts'] += $providerDebtsAmount;
            $info['total']['service_debts'] += $serviceDebtsAmount;
            $info['total']['salary_debts'] += $salaryDebtsAmount;
            $info['total']['total_debts'] += $totalDebts;
        }

        return response()->json(compact('info'));
    }
}