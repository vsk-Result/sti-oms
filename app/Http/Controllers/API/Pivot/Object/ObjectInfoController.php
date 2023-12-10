<?php

namespace App\Http\Controllers\API\Pivot\Object;

use App\Http\Controllers\Controller;
use App\Models\CurrencyExchangeRate;
use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Object\BObject;
use App\Services\PivotObjectDebtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ObjectInfoController extends Controller
{
    private PivotObjectDebtService $pivotObjectDebtService;

    public function __construct(
        PivotObjectDebtService $pivotObjectDebtService
    ) {
        $this->pivotObjectDebtService = $pivotObjectDebtService;
    }

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

        $info = [
            'debts' => [],
            'total' => [
                'contractors_debts' => 0,
                'providers_debts' => 0,
                'service_debts' => 0,
                'total_debts' => 0,
            ]
        ];
        $objects = BObject::active()->orderBy('code')->get();

        foreach ($objects as $object) {
            $debts = $this->pivotObjectDebtService->getPivotDebtForObject($object->id);

            if ($object->code === '288') {
                $contractorDebts = $debts['contractor']->debts;
                $contractorDebtsAmount = 0;

                foreach ($contractorDebts as $in) {
                    $contractorDebtsAmount += array($in->worktype)[1] ?? 0;
                    $contractorDebtsAmount += array($in->worktype)[2] ?? 0;
                    $contractorDebtsAmount += array($in->worktype)[4] ?? 0;
                    $contractorDebtsAmount += array($in->worktype)[7] ?? 0;
                }

                $providerDebts = $debts['provider']->debts;
                $providerDebtsAmount = 0;

                foreach ($providerDebts as $in) {
                    $providerDebtsAmount += array($in->worktype)[1] ?? 0;
                    $providerDebtsAmount += array($in->worktype)[2] ?? 0;
                    $providerDebtsAmount += array($in->worktype)[4] ?? 0;
                    $providerDebtsAmount += array($in->worktype)[7] ?? 0;
                }

                $totalDebts = $contractorDebtsAmount + $providerDebtsAmount;

                $info['debts'][] = [
                    'object_name' => $object->getName(),
                    'contractors_debts' => CurrencyExchangeRate::format($contractorDebtsAmount, 'RUB'),
                    'providers_debts' => CurrencyExchangeRate::format($providerDebtsAmount, 'RUB'),
                    'total_debts' => CurrencyExchangeRate::format($totalDebts, 'RUB'),
                ];

                $info['total']['contractors_debts'] += $contractorDebtsAmount;
                $info['total']['providers_debts'] += $providerDebtsAmount;
                $info['total']['total_debts'] += $totalDebts;

                continue;
            }

            $contractorDebtsAmount = $debts['contractor']->total_amount;

            $debtObjectImport = DebtImport::where('type_id', DebtImport::TYPE_OBJECT)->latest('date')->first();
            $objectExistInObjectImport = $debtObjectImport->debts()->where('object_id', $object->id)->count() > 0;

            if ($objectExistInObjectImport) {
                $contractorDebtsAvans = Debt::where('import_id', $debtObjectImport->id)->where('type_id', Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->sum('avans');
                $contractorDebtsGU = Debt::where('import_id', $debtObjectImport->id)->where('type_id', Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->sum('guarantee');
                $contractorDebtsAmount = $contractorDebtsAmount + $contractorDebtsAvans + $contractorDebtsGU;
            }

            $providerDebtsAmount = $debts['provider']->total_amount;
            $serviceDebtsAmount = $debts['service']->total_amount;
            $totalDebts = $contractorDebtsAmount + $providerDebtsAmount + $serviceDebtsAmount;

            $info['debts'][] = [
                'object_name' => $object->getName(),
                'contractors_debts' => CurrencyExchangeRate::format($contractorDebtsAmount, 'RUB'),
                'providers_debts' => CurrencyExchangeRate::format($providerDebtsAmount, 'RUB'),
                'service_debts' => CurrencyExchangeRate::format($serviceDebtsAmount, 'RUB'),
                'total_debts' => CurrencyExchangeRate::format($totalDebts, 'RUB'),
            ];

            $info['total']['contractors_debts'] += $contractorDebtsAmount;
            $info['total']['providers_debts'] += $providerDebtsAmount;
            $info['total']['total_debts'] += $totalDebts;
        }

        $info['total']['contractors_debts'] = CurrencyExchangeRate::format($info['total']['contractors_debts'], 'RUB');
        $info['total']['providers_debts'] = CurrencyExchangeRate::format($info['total']['providers_debts'], 'RUB');
        $info['total']['service_debts'] = CurrencyExchangeRate::format($info['total']['service_debts'], 'RUB');
        $info['total']['total_debts'] = CurrencyExchangeRate::format($info['total']['total_debts'], 'RUB');

        return response()->json(compact('info'));
    }
}