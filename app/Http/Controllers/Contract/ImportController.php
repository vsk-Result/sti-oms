<?php

namespace App\Http\Controllers\Contract;

use App\Helpers\Sanitizer;
use App\Http\Controllers\Controller;
use App\Imports\Contract\ContractImport;
use App\Models\BankGuarantee;
use App\Models\Contract\Act;
use App\Models\Contract\ActPayment;
use App\Models\Contract\Contract;
use App\Models\Contract\ContractAvans;
use App\Models\Contract\ContractReceivedAvans;
use App\Models\Object\BObject;
use App\Models\Organization;
use App\Models\Status;
use App\Services\CurrencyExchangeRateService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportController extends Controller
{
    private Sanitizer $sanitizer;
    private CurrencyExchangeRateService $currencyService;

    public function __construct(CurrencyExchangeRateService $currencyService, Sanitizer $sanitizer)
    {
        $this->currencyService = $currencyService;
        $this->sanitizer = $sanitizer;
    }

    public function store(Request $request): RedirectResponse
    {
//        $contracts = Contract::all();
//        foreach ($contracts as $contract) {
//            foreach ($contract->acts as $act) {
//                foreach ($act->payments as $payment) {
//                    $payment->forceDelete();
//                }
//                $act->forceDelete();
//            }
//            foreach ($contract->avanses as $avanse) {
//                $avanse->forceDelete();
//            }
//            foreach ($contract->avansesReceived as $avanse) {
//                $avanse->forceDelete();
//            }
//            $contract->forceDelete();
//        }
//
//        $requestData = $request->toArray();
//        $importData = Excel::toArray(new ContractImport(), $requestData['file']);
//
//        foreach ($importData['Договора'] as $index => $row) {
//            if ($index === 0) continue;
//
//            $contractName = $this->sanitizer->set($row[2])->get();
//
//            $contract = Contract::where('name', $contractName)->first();
//
//            if (! $contract) {
//                $parent = null;
//
//                if (! empty($row[17])) {
//                    $parentName = $this->sanitizer->set($row[17])->get();
//                    $parent = Contract::where('name', $parentName)->first()->id;
//                    $contractName = $this->sanitizer->set($contractName)->replace($parentName, '')->trim()->onlyOneSpace()->get();
//                }
//
//                $contract = Contract::create([
//                    'parent_id' => $parent,
//                    'type_id' => $row[16],
//                    'amount_type_id' => $row[16] === 0 ? Contract::AMOUNT_TYPE_MAIN : Contract::AMOUNT_TYPE_ADDITIONAL,
//                    'company_id' => 1,
//                    'object_id' => BObject::where('code', $row[0])->first()->id,
//                    'name' => $contractName,
//                    'description' => null,
//                    'start_date' => null,
//                    'end_date' => null,
//                    'amount' => empty($row[3]) ? 0 : $row[3],
//                    'stage_id' => 0,
//                    'status_id' => Status::STATUS_ACTIVE,
//                    'currency' => $row[1],
//                    'currency_rate' => 1,
//                ]);
//            }
//
//            if (! empty($row[4])) {
//                ContractAvans::create([
//                    'contract_id' => $contract->id,
//                    'company_id' => $contract->company_id,
//                    'object_id' => $contract->object_id,
//                    'amount' => $row[4],
//                    'status_id' => Status::STATUS_ACTIVE,
//                    'currency' => $contract->currency,
//                    'currency_rate' => $contract->currency_rate,
//                ]);
//            }
//
//            $avanseDate = $row[5];
//            if (! empty($avanseDate) && ! empty($row[6])) {
//                $description = null;
//                $date = null;
//                if (is_numeric($avanseDate)) {
//                    $date = Carbon::parse(Date::excelToDateTimeObject($avanseDate))->format('Y-m-d');
//                } else {
//                    $description = $this->sanitizer->set($avanseDate)->get();
//                }
//
//                ContractReceivedAvans::create([
//                    'contract_id' => $contract->id,
//                    'company_id' => $contract->company_id,
//                    'object_id' => $contract->object_id,
//                    'date' => $date,
//                    'amount' => $row[6],
//                    'description' => $description,
//                    'status_id' => Status::STATUS_ACTIVE,
//                    'currency' => $contract->currency,
//                    'currency_rate' => $contract->currency !== 'RUB' && ! is_null($date)
//                        ? $this->currencyService->parseRateFromCBR($date, $contract->currency) ?? 0
//                        : $contract->currency_rate,
//                ]);
//            }
//
//            if (! empty($row[8]) || ! empty($row[9]) || ! empty($row[10]) || ! empty($row[11]) || ! empty($row[12]) || ! empty($row[13]) || ! empty($row[14]) || ! empty($row[15])) {
//
//                $date = null;
//                if (! empty($row[8])) {
//                    $date = Carbon::parse(Date::excelToDateTimeObject($row[8]))->format('Y-m-d');
//                }
//
//                $act = Act::create([
//                    'contract_id' => $contract->id,
//                    'company_id' => $contract->company_id,
//                    'object_id' => $contract->object_id,
//                    'date' => $date,
//                    'amount' => empty($row[9]) ? 0 : $row[9],
//                    'amount_avans' => empty($row[10]) ? 0 : $row[10],
//                    'amount_deposit' => empty($row[11]) ? 0 : $row[11],
//                    'description' => null,
//                    'status_id' => Status::STATUS_ACTIVE,
//                    'currency' => $contract->currency,
//                    'currency_rate' => $contract->currency_rate,
//                ]);
//
//                $act->update([
//                    'amount_need_paid' => $act->amount - $act->amount_avans - $act->amount_deposit
//                ]);
//
//                if (! empty($row[14])) {
//                    $description = null;
//                    $date = null;
//                    if (! empty($row[13])) {
//                        if (is_numeric($row[13])) {
//                            $date = Carbon::parse(Date::excelToDateTimeObject($row[13]))->format('Y-m-d');
//                        } else {
//                            $description = $this->sanitizer->set($row[13])->get();
//                        }
//                    }
//
//                    ActPayment::create([
//                        'contract_id' => $contract->id,
//                        'act_id' => $act->id,
//                        'company_id' => $act->company_id,
//                        'object_id' => $act->object_id,
//                        'date' => $date,
//                        'amount' => $row[14],
//                        'description' => $description,
//                        'status_id' => Status::STATUS_ACTIVE,
//                        'currency' => $act->currency,
//                        'currency_rate' => $contract->currency !== 'RUB' && ! is_null($date)
//                            ? $this->currencyService->parseRateFromCBR($date, $act->currency) ?? 0
//                            : $act->currency_rate,
//                    ]);
//                }
//            }
//        }

//        foreach ($importData['Договора'] as $index => $contractRow) {
//            if ($index === 0) continue;
//
//            $contractParent = $contractRow[1] === null ? null : Contract::where('name', trim($contractRow[1]))->first()->id;
//            Contract::create([
//                'parent_id' => $contractParent,
//                'type_id' => $contractRow[3],
//                'amount_type_id' => $contractRow[3] === 2 ? Contract::AMOUNT_TYPE_ADDITIONAL : null,
//                'company_id' => 1,
//                'object_id' => BObject::where('code', $contractRow[0])->first()->id,
//                'name' => trim($contractRow[2]),
//                'description' => $contractRow[7],
//                'start_date' => Carbon::parse(Date::excelToDateTimeObject($contractRow[4]))->format('Y-m-d'),
//                'end_date' => null,
//                'amount' => $contractRow[6],
//                'stage_id' => 0,
//                'status_id' => Status::STATUS_ACTIVE,
//                'currency' => $contractRow[5],
//                'currency_rate' => 1,
//            ]);
//        }
//
//        foreach ($importData['Авансы'] as $index => $avanseRow) {
//            if ($index === 0) continue;
//
//            $contract = Contract::where('name', trim($avanseRow[0]))->where('currency', $avanseRow[1])->first();
//
//            ContractAvans::create([
//                'contract_id' => $contract->id,
//                'company_id' => $contract->company_id,
//                'object_id' => $contract->object_id,
//                'amount' => $avanseRow[2],
//                'status_id' => Status::STATUS_ACTIVE,
//                'currency' => $contract->currency,
//                'currency_rate' => $contract->currency_rate,
//            ]);
//        }
//
//        foreach ($importData['Полученные авансы'] as $index => $avanseRow) {
//            if ($index === 0) continue;
//
//            $contract = Contract::where('name', trim($avanseRow[0]))->where('currency', $avanseRow[1])->first();
//            $avanseDate = Carbon::parse(Date::excelToDateTimeObject($avanseRow[2]))->format('Y-m-d');
//            ContractReceivedAvans::create([
//                'contract_id' => $contract->id,
//                'company_id' => $contract->company_id,
//                'object_id' => $contract->object_id,
//                'date' => $avanseDate,
//                'amount' => $avanseRow[3],
//                'status_id' => Status::STATUS_ACTIVE,
//                'currency' => $contract->currency,
//                'currency_rate' => $contract->currency !== 'RUB'
//                    ? $this->currencyService->parseRateFromCBR($avanseDate, $contract->currency)
//                    : $contract->currency_rate,
//            ]);
//        }
//
//        foreach ($importData['Акты'] as $index => $actRow) {
//            if ($index === 0) continue;
//
//            $contract = Contract::where('name', trim($actRow[0]))->where('currency', $actRow[1])->first();
//
//            $act = Act::create([
//                'contract_id' => $contract->id,
//                'company_id' => $contract->company_id,
//                'object_id' => $contract->object_id,
//                'date' => Carbon::parse(Date::excelToDateTimeObject($actRow[2]))->format('Y-m-d'),
//                'amount' => $actRow[3] === null ? 0 : $actRow[3],
//                'amount_avans' => $actRow[4] === null ? 0 : $actRow[4],
//                'amount_deposit' => $actRow[5] === null ? 0 : $actRow[5],
//                'description' => null,
//                'status_id' => Status::STATUS_ACTIVE,
//                'currency' => $contract->currency,
//                'currency_rate' => $contract->currency_rate,
//            ]);
//
//            $act->update([
//                'amount_need_paid' => $act->amount - $act->amount_avans - $act->amount_deposit
//            ]);
//
//            if ($actRow[7] !== null) {
//                $paymentDate = Carbon::parse(Date::excelToDateTimeObject($actRow[7]))->format('Y-m-d');
//                ActPayment::create([
//                    'contract_id' => $contract->id,
//                    'act_id' => $act->id,
//                    'company_id' => $act->company_id,
//                    'object_id' => $act->object_id,
//                    'date' => $paymentDate,
//                    'amount' => $actRow[8],
//                    'status_id' => Status::STATUS_ACTIVE,
//                    'currency' => $act->currency,
//                    'currency_rate' => $act->currency !== 'RUB'
//                        ? $this->currencyService->parseRateFromCBR($paymentDate, $act->currency)
//                        : $act->currency_rate,
//                ]);
//            }
//        }

        $requestData = $request->toArray();
        $importData = Excel::toArray(new ContractImport(), $requestData['file']);
        foreach ($importData['Банковские гарантии'] as $index => $guaranteeRow) {
            if ($index === 0) continue;

            BankGuarantee::create([
                'company_id' => 1,
                'bank_id' => null,
                'object_id' => BObject::where('code', $guaranteeRow[0])->first()->id ?? null,
                'contract_id' => Contract::where('name', $guaranteeRow[1])->first()->id ?? null,
                'organization_id' => Organization::where('name', $guaranteeRow[2])->first()->id ?? null,
                'number' => $guaranteeRow[3],
                'start_date' => $guaranteeRow[4] === null ? null : Carbon::parse(Date::excelToDateTimeObject($guaranteeRow[4]))->format('Y-m-d'),
                'end_date' => $guaranteeRow[5] === null ? null : Carbon::parse(Date::excelToDateTimeObject($guaranteeRow[5]))->format('Y-m-d'),
                'currency' => $guaranteeRow[6],
                'currency_rate' => 1,
                'amount' => $guaranteeRow[7],
                'start_date_deposit' => $guaranteeRow[8] === null ? null : Carbon::parse(Date::excelToDateTimeObject($guaranteeRow[8]))->format('Y-m-d'),
                'end_date_deposit' => $guaranteeRow[9] === null ? null : Carbon::parse(Date::excelToDateTimeObject($guaranteeRow[9]))->format('Y-m-d'),
                'amount_deposit' => $guaranteeRow[10],
                'commission' => $guaranteeRow[11] ?? 0,
                'target' => null,
                'status_id' => Status::STATUS_ACTIVE,
            ]);
        }

        return redirect()->back();
    }
}
