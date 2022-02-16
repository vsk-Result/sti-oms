<?php

namespace App\Http\Controllers\Contract;

use App\Http\Controllers\Controller;
use App\Imports\Contract\ContractImport;
use App\Models\BankGuarantee;
use App\Models\Contract\Act;
use App\Models\Contract\ActPayment;
use App\Models\Contract\Contract;
use App\Models\Contract\ContractAvans;
use App\Models\Contract\ContractReceivedAvans;
use App\Models\Object\BObject;
use App\Models\Status;
use App\Services\CurrencyExchangeRateService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportController extends Controller
{
    private CurrencyExchangeRateService $currencyService;

    public function __construct(CurrencyExchangeRateService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function store(Request $request): RedirectResponse
    {
        $requestData = $request->toArray();
        $importData = Excel::toArray(new ContractImport(), $requestData['file']);

        foreach ($importData['Договора'] as $index => $contractRow) {
            if ($index === 0) continue;

            $contractParent = $contractRow[1] === null ? null : Contract::where('name', trim($contractRow[1]))->first()->id;
            Contract::create([
                'parent_id' => $contractParent,
                'type_id' => $contractRow[3],
                'amount_type_id' => $contractRow[3] === 2 ? Contract::AMOUNT_TYPE_ADDITIONAL : null,
                'company_id' => 1,
                'object_id' => BObject::where('code', $contractRow[0])->first()->id,
                'name' => trim($contractRow[2]),
                'description' => $contractRow[7],
                'start_date' => Carbon::parse(Date::excelToDateTimeObject($contractRow[4]))->format('Y-m-d'),
                'end_date' => null,
                'amount' => $contractRow[6],
                'stage_id' => 0,
                'status_id' => Status::STATUS_ACTIVE,
                'currency' => $contractRow[5],
                'currency_rate' => 1,
            ]);
        }

        foreach ($importData['Авансы'] as $index => $avanseRow) {
            if ($index === 0) continue;

            $contract = Contract::where('name', trim($avanseRow[0]))->where('currency', $avanseRow[1])->first();

            ContractAvans::create([
                'contract_id' => $contract->id,
                'company_id' => $contract->company_id,
                'object_id' => $contract->object_id,
                'amount' => $avanseRow[2],
                'status_id' => Status::STATUS_ACTIVE,
                'currency' => $contract->currency,
                'currency_rate' => $contract->currency_rate,
            ]);
        }

        foreach ($importData['Полученные авансы'] as $index => $avanseRow) {
            if ($index === 0) continue;

            $contract = Contract::where('name', trim($avanseRow[0]))->where('currency', $avanseRow[1])->first();
            $avanseDate = Carbon::parse(Date::excelToDateTimeObject($avanseRow[2]))->format('Y-m-d');
            ContractReceivedAvans::create([
                'contract_id' => $contract->id,
                'company_id' => $contract->company_id,
                'object_id' => $contract->object_id,
                'date' => $avanseDate,
                'amount' => $avanseRow[3],
                'status_id' => Status::STATUS_ACTIVE,
                'currency' => $contract->currency,
                'currency_rate' => $contract->currency !== 'RUB'
                    ? $this->currencyService->parseRateFromCBR($avanseDate, $contract->currency)
                    : $contract->currency_rate,
            ]);
        }

        foreach ($importData['Акты'] as $index => $actRow) {
            if ($index === 0) continue;

            $contract = Contract::where('name', trim($actRow[0]))->where('currency', $actRow[1])->first();

            $act = Act::create([
                'contract_id' => $contract->id,
                'company_id' => $contract->company_id,
                'object_id' => $contract->object_id,
                'date' => Carbon::parse(Date::excelToDateTimeObject($actRow[2]))->format('Y-m-d'),
                'amount' => $actRow[3] === null ? 0 : $actRow[3],
                'amount_avans' => $actRow[4] === null ? 0 : $actRow[4],
                'amount_deposit' => $actRow[5] === null ? 0 : $actRow[5],
                'description' => null,
                'status_id' => Status::STATUS_ACTIVE,
                'currency' => $contract->currency,
                'currency_rate' => $contract->currency_rate,
            ]);

            $act->update([
                'amount_need_paid' => $act->amount - $act->amount_avans - $act->amount_deposit
            ]);

            if ($actRow[7] !== null) {
                $paymentDate = Carbon::parse(Date::excelToDateTimeObject($actRow[7]))->format('Y-m-d');
                ActPayment::create([
                    'contract_id' => $contract->id,
                    'act_id' => $act->id,
                    'company_id' => $act->company_id,
                    'object_id' => $act->object_id,
                    'date' => $paymentDate,
                    'amount' => $actRow[8],
                    'status_id' => Status::STATUS_ACTIVE,
                    'currency' => $act->currency,
                    'currency_rate' => $act->currency !== 'RUB'
                        ? $this->currencyService->parseRateFromCBR($paymentDate, $act->currency)
                        : $act->currency_rate,
                ]);
            }
        }

        foreach ($importData['Банковские гарантии'] as $index => $guaranteeRow) {
            if ($index === 0) continue;

            BankGuarantee::create([
                'company_id' => 1,
                'bank_id' => null,
                'object_id' => BObject::where('code', $guaranteeRow[0])->first()->id,
                'start_date' => null,
                'end_date' => $guaranteeRow[1] === null ? null : Carbon::parse(Date::excelToDateTimeObject($guaranteeRow[1]))->format('Y-m-d'),
                'amount' => $guaranteeRow[2],
                'start_date_deposit' => null,
                'end_date_deposit' => $guaranteeRow[3] === null ? null : Carbon::parse(Date::excelToDateTimeObject($guaranteeRow[3]))->format('Y-m-d'),
                'amount_deposit' => $guaranteeRow[4],
                'target' => null,
                'status_id' => Status::STATUS_ACTIVE,
                'currency' => $guaranteeRow[5],
                'currency_rate' => 1,
            ]);
        }

        return redirect()->back();
    }
}
