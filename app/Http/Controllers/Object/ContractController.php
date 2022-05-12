<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Contract\Act;
use App\Models\Contract\ActPayment;
use App\Models\CurrencyExchangeRate;
use App\Models\Object\BObject;
use App\Services\Contract\ContractService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    private ContractService $contractService;

    public function __construct(ContractService $contractService)
    {
        $this->contractService = $contractService;
    }

    public function index(BObject $object, Request $request): View
    {
        $total = [];
        $requestData = array_merge(['object_id' => [$object->id]], $request->toArray());
        $contracts = $this->contractService->filterContracts($requestData, $total);
        $objects = BObject::orderBy('code')->get();

        $actsMonths = [];
        foreach (ActPayment::whereIn('contract_id', $total['ids'])->orderBy('date')->get() as $payment) {
            if (empty($payment->date)) {
                continue;
            }

            $month = Carbon::parse($payment->date)->format('F Y');
            if (! isset($actsMonths[$month][$payment->currency])) {
                $actsMonths[$month][$payment->currency] = 0;
            }
            $actsMonths[$month][$payment->currency] += $payment->amount;
        }
        foreach ($actsMonths as $k => $currencies) {
            foreach ($currencies as $currency => $amount) {
                $actsMonths[$k][$currency] = $amount;
            }
        }
        $RUBActsAmounts = [];

        foreach ($actsMonths as $currencies) {
            $RUBActsAmounts[] = $currencies['RUB'];
        }

        $actsMonths = array_keys($actsMonths);

        return view('objects.tabs.contracts', compact('object', 'contracts', 'objects', 'total', 'actsMonths', 'RUBActsAmounts'));
    }
}
