<?php

namespace App\Http\Controllers\API\Pivot\Object;

use App\Http\Controllers\Controller;
use App\Models\CurrencyExchangeRate;
use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Object\BObject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ObjectInfoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
//        if (! $request->has('verify_hash')) {
//            abort(403);
//            return response()->json([], 403);
//        }
//
//        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
//            abort(403);
//            return response()->json([], 403);
//        }

        $info = [
            'debts' => [],
            'total' => [
                'contractors_debts' => 0,
                'providers_debts' => 0,
                'total_debts' => 0,
            ]
        ];
        $objects = BObject::active()->orderBy('code')->get();

        foreach ($objects as $object) {
            if ($object->code === '288') {
                $contractorDebts = $object->getContractorDebts();
                $contractorDebtsAmount = 0;

                foreach ($contractorDebts as $in) {
                    $contractorDebtsAmount += $in['worktype'][1] ?? 0;
                    $contractorDebtsAmount += $in['worktype'][2] ?? 0;
                    $contractorDebtsAmount += $in['worktype'][4] ?? 0;
                    $contractorDebtsAmount += $in['worktype'][7] ?? 0;
                }

                $providerDebts = $object->getProviderDebts();
                $providerDebtsAmount = 0;

                foreach ($providerDebts as $in) {
                    $providerDebtsAmount += $in['worktype'][1] ?? 0;
                    $providerDebtsAmount += $in['worktype'][2] ?? 0;
                    $providerDebtsAmount += $in['worktype'][4] ?? 0;
                    $providerDebtsAmount += $in['worktype'][7] ?? 0;
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

            $contractorDebtsAmount = $object->getContractorDebtsAmount();

            $debtObjectImport = DebtImport::where('type_id', DebtImport::TYPE_OBJECT)->latest('date')->first();
            $objectExistInObjectImport = $debtObjectImport->debts()->where('object_id', $object->id)->count() > 0;

            if ($objectExistInObjectImport) {
                $contractorDebtsAvans = Debt::where('import_id', $debtObjectImport->id)->where('type_id', Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->sum('avans');
                $contractorDebtsAmount = $contractorDebtsAmount + $contractorDebtsAvans;
            }

            $providerDebtsAmount = $object->getProviderDebtsAmount();
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
        }

        $includeObjectCodes = ['349'];
        $includeObjects = BObject::whereIn('code', $includeObjectCodes)->orderBy('code')->get();
        foreach ($includeObjects as $object) {
            $contractorDebtsAmount = $object->getContractorDebtsAmount();

            $debtObjectImport = DebtImport::where('type_id', DebtImport::TYPE_OBJECT)->latest('date')->first();
            $objectExistInObjectImport = $debtObjectImport->debts()->where('object_id', $object->id)->count() > 0;

            if ($objectExistInObjectImport) {
                $contractorDebtsAvans = Debt::where('import_id', $debtObjectImport->id)->where('type_id', Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->sum('avans');
                $contractorDebtsAmount = $contractorDebtsAmount + $contractorDebtsAvans;
            }

            $providerDebtsAmount = $object->getProviderDebtsAmount();
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
        }

        $info['total']['contractors_debts'] = CurrencyExchangeRate::format($info['total']['contractors_debts'], 'RUB');
        $info['total']['providers_debts'] = CurrencyExchangeRate::format($info['total']['providers_debts'], 'RUB');
        $info['total']['total_debts'] = CurrencyExchangeRate::format($info['total']['total_debts'], 'RUB');

        return response()->json(compact('info'));
    }
}