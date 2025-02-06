<?php

namespace App\Http\Controllers\API\Pivot\Object;

use App\Http\Controllers\Controller;
use App\Models\FinanceReportHistory;
use App\Models\Loan;
use App\Models\Object\BObject;
use App\Models\TaxPlanItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ObjectInfoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (! $request->has('verify_hash')) {
            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
        }

        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
        }

        if (! $request->has('access_objects')) {
            return response()->json(['error' => 'Отсутствует access_objects'], 403);
        }

        $financeReportHistory = FinanceReportHistory::where('date', now()->format('Y-m-d'))->first();

        $creditsInfo = json_decode($financeReportHistory->credits);
        $loansInfo = json_decode($financeReportHistory->loans);
        $objectsInfo = json_decode($financeReportHistory->objects_new);
        $years = collect($objectsInfo->years)->toArray();
        $total = $objectsInfo->total;

        $info = [
            'credits' => [],
            'loans' => [],
            'debts' => [],
            'total' => [
                'contractors_debts' => 0,
                'contractors_debts_gu' => 0,
                'providers_debts' => 0,
                'provider_debt_fix' => 0,
                'provider_debt_float' => 0,
                'service_debts' => 0,
                'salary_debts' => 0,
                'tax_debts' => 0,
                'credits_debts' => 0,
                'loans_debts' => 0,
                'total_debts' => 0,
            ]
        ];

        foreach ($creditsInfo->credits as $credit) {
            if (isset($credit->credit_type_id) && $credit->credit_type_id === Loan::CREDIT_TYPE_DEFAULT) {
                $info['credits'][] = [
                    'title' => $credit->bank,
                    'description' => $credit->contract,
                    'amount' => (float) $credit->debt,
                ];
            } else {
                $info['credits'][] = [
                    'title' => $credit->bank,
                    'description' => $credit->contract,
                    'amount' => (float) $credit->paid ?? 0,
                ];
            }
        }

        foreach ($loansInfo->loans as $loan) {
            $info['loans'][] = [
                'title' => $loan->organization->name,
                'description' => $loan->name,
                'amount' => (float) $loan->amount,
            ];
        }

        $info['total']['credits_debts'] = (float) $creditsInfo->totalCreditAmount;
        $info['total']['loans_debts'] = (float) $loansInfo->totalLoanAmount;


        $objects = BObject::active()->orderBy('code')->get();

        if ($request->get('access_objects') !== '*') {
            $accessObjects = explode(',', $request->get('access_objects'));

            $objects = BObject::active()->whereIn('id', $accessObjects)->orderBy('code')->get();
        }

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
            $providerDebtsAmountFix = $inf['provider_debt_fix'];
            $providerDebtsAmountFloat = $inf['provider_debt_float'];
            $serviceDebtsAmount = $inf['service_debt'];
            $salaryDebtsAmount = $inf['workers_salary_debt'];
            $totalDebts = $contractorDebtsAmount + $providerDebtsAmount + $serviceDebtsAmount + $contractorDebtsAmountGU + $salaryDebtsAmount;

            $info['debts'][] = [
                'id' => $object->id,
                'object_name' => $object->getName(),
                'contractors_debts' => $contractorDebtsAmount,
                'contractors_debts_gu' => $contractorDebtsAmountGU,
                'providers_debts' => $providerDebtsAmount,
                'provider_debt_fix' => $providerDebtsAmountFix,
                'provider_debt_float' => $providerDebtsAmountFloat,
                'service_debts' => $serviceDebtsAmount,
                'salary_debts' => $salaryDebtsAmount,
                'total_debts' => $totalDebts,
            ];

            $info['total']['contractors_debts'] += $contractorDebtsAmount;
            $info['total']['contractors_debts_gu'] += $contractorDebtsAmountGU;
            $info['total']['providers_debts'] += $providerDebtsAmount;
            $info['total']['provider_debt_fix'] += $providerDebtsAmountFix;
            $info['total']['provider_debt_float'] += $providerDebtsAmountFloat;
            $info['total']['service_debts'] += $serviceDebtsAmount;
            $info['total']['salary_debts'] += $salaryDebtsAmount;
            $info['total']['total_debts'] += $totalDebts;
        }

        $taxPlans = TaxPlanItem::where('object_id', 0)
            ->where('paid', false)
            ->where('due_date', '<', Carbon::now())
            ->get();

        $info['total']['tax_debts'] = $objectsInfo->summary->{'Активные'}->{'tax_debt'};

        $info['total']['tax_debts_nds'] = -$taxPlans->where('name', 'НДС')->sum('amount');
        $info['total']['tax_debts_strah'] = -$taxPlans->where('name', 'Страховые взносы')->sum('amount');
        $info['total']['tax_debts_prib'] = -$taxPlans->whereIn('name', ['Налог на прибыль аванс', 'Налог на прибыль'])->sum('amount');
        $info['total']['tax_debts_ndfl'] = -$taxPlans->where('name', 'НДФЛ')->sum('amount');
        $info['total']['tax_debts_transport'] = -$taxPlans->where('name', 'Транспортный налог')->sum('amount');
        $info['total']['tax_debts_penis'] = -$taxPlans->where('name', 'Пени')->sum('amount');

        $info['total']['total_debts'] += $info['total']['tax_debts'];

        return response()->json(compact('info'));
    }
}