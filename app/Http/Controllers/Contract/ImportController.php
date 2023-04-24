<?php

namespace App\Http\Controllers\Contract;

use App\Helpers\Sanitizer;
use App\Http\Controllers\Controller;
use App\Imports\Contract\ContractImport;
use App\Models\Contract\Act;
use App\Models\Contract\ActPayment;
use App\Models\Contract\Contract;
use App\Models\Contract\ContractAvans;
use App\Models\Contract\ContractReceivedAvans;
use App\Models\Guarantee;
use App\Models\GuaranteePayment;
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
    private Sanitizer $sanitizer;
    private CurrencyExchangeRateService $currencyService;

    public function __construct(CurrencyExchangeRateService $currencyService, Sanitizer $sanitizer)
    {
        $this->currencyService = $currencyService;
        $this->sanitizer = $sanitizer;
    }

    public function store(Request $request): RedirectResponse
    {
        $requestData = $request->toArray();
        $importData = Excel::toArray(new ContractImport(), $requestData['file']);

        if (isset($importData['Договора'])) {

            $objectIds = BObject::whereNotIn('code', ['288', '358', '346', '360', '359', '349', '353'])->pluck('id')->toArray();
            Contract::whereIn('object_id', $objectIds)->delete();
            ContractAvans::whereIn('object_id', $objectIds)->delete();
            ContractReceivedAvans::whereIn('object_id', $objectIds)->delete();
            Act::whereIn('object_id', $objectIds)->delete();
            ActPayment::whereIn('object_id', $objectIds)->delete();

            foreach ($importData['Договора'] as $index => $row) {
                if ($index === 0) continue;

                $currency = $row[1];
                $contractName = $this->sanitizer->set($row[2])->get();

                $contract = Contract::where('name', $contractName)->where('currency', $currency)->first();

                $needCreate = false;

                if (! $contract) {
                    $needCreate = true;
                } elseif (! empty($row[3])) {
                    $needCreate = true;
                }

                if ($needCreate) {
                    $parent = null;

                    if (! empty($row[17])) {
                        $parentName = $this->sanitizer->set($row[17])->get();
                        $parent = Contract::where('name', $parentName)->first()->id;
//                    $contractName = $this->sanitizer->set($contractName)->replace($parentName, '')->trim()->onlyOneSpace()->get();
                    }

                    $contract = Contract::create([
                        'parent_id' => $parent,
                        'type_id' => $row[16],
                        'amount_type_id' => $row[16] === 0 ? Contract::AMOUNT_TYPE_MAIN : Contract::AMOUNT_TYPE_ADDITIONAL,
                        'company_id' => 1,
                        'object_id' => BObject::where('code', $row[0])->first()->id,
                        'name' => $contractName,
                        'description' => $row[18],
                        'start_date' => null,
                        'end_date' => null,
                        'amount' => empty($row[3]) ? 0 : $row[3],
                        'stage_id' => 0,
                        'status_id' => Status::STATUS_ACTIVE,
                        'currency' => $currency,
                        'currency_rate' => 1,
                    ]);
                }

                if (! empty($row[4])) {
                    ContractAvans::create([
                        'contract_id' => $contract->id,
                        'company_id' => $contract->company_id,
                        'object_id' => $contract->object_id,
                        'amount' => $row[4],
                        'status_id' => Status::STATUS_ACTIVE,
                        'currency' => $contract->currency,
                        'currency_rate' => $contract->currency_rate,
                    ]);
                }

                $avanseDate = $row[5];
                if (! empty($row[6])) {
                    $description = null;
                    $date = null;
                    if (is_numeric($avanseDate)) {
                        $date = Carbon::parse(Date::excelToDateTimeObject($avanseDate))->format('Y-m-d');
                    } else {
                        $description = $this->sanitizer->set($avanseDate)->get();
                    }

                    ContractReceivedAvans::create([
                        'contract_id' => $contract->id,
                        'company_id' => $contract->company_id,
                        'object_id' => $contract->object_id,
                        'date' => $date,
                        'amount' => $row[6],
                        'description' => $description,
                        'status_id' => Status::STATUS_ACTIVE,
                        'currency' => $contract->currency,
                        'currency_rate' => $contract->currency !== 'RUB' && ! is_null($date)
                            ? $this->currencyService->parseRateFromCBR($date, $contract->currency) ?? 0
                            : $contract->currency_rate,
                    ]);
                }

                if (! empty($row[8]) || ! empty($row[9]) || ! empty($row[10]) || ! empty($row[11]) || ! empty($row[12]) || ! empty($row[13]) || ! empty($row[14]) || ! empty($row[15])) {

                    $date = null;
                    if (! empty($row[8])) {
                        $date = Carbon::parse(Date::excelToDateTimeObject($row[8]))->format('Y-m-d');
                    }

                    $act = Act::create([
                        'contract_id' => $contract->id,
                        'company_id' => $contract->company_id,
                        'object_id' => $contract->object_id,
                        'date' => $date,
                        'amount' => empty($row[9]) ? 0 : $row[9],
                        'amount_avans' => empty($row[10]) ? 0 : $row[10],
                        'amount_deposit' => empty($row[11]) ? 0 : $row[11],
                        'description' => null,
                        'status_id' => Status::STATUS_ACTIVE,
                        'currency' => $contract->currency,
                        'currency_rate' => $contract->currency_rate,
                    ]);

                    $act->update([
                        'amount_need_paid' => $act->amount - $act->amount_avans - $act->amount_deposit
                    ]);

                    if (! empty($row[14])) {
                        $description = null;
                        $date = null;
                        if (! empty($row[13])) {
                            if (is_numeric($row[13])) {
                                $date = Carbon::parse(Date::excelToDateTimeObject($row[13]))->format('Y-m-d');
                            } else {
                                $description = $this->sanitizer->set($row[13])->get();
                            }
                        }

                        ActPayment::create([
                            'contract_id' => $contract->id,
                            'act_id' => $act->id,
                            'company_id' => $act->company_id,
                            'object_id' => $act->object_id,
                            'date' => $date,
                            'amount' => $row[14],
                            'description' => $description,
                            'status_id' => Status::STATUS_ACTIVE,
                            'currency' => $act->currency,
                            'currency_rate' => $contract->currency !== 'RUB' && ! is_null($date)
                                ? $this->currencyService->parseRateFromCBR($date, $act->currency) ?? 0
                                : $act->currency_rate,
                        ]);
                    }
                }
            }
        }

        if (isset($importData['СВОДНАЯ ГЭС-2'])) {

            $objectId = BObject::where('code', '288')->first()->id;
            Contract::where('object_id', $objectId)->delete();
            ContractAvans::where('object_id', $objectId)->delete();
            ContractReceivedAvans::where('object_id', $objectId)->delete();
            Act::where('object_id', $objectId)->delete();
            ActPayment::where('object_id', $objectId)->delete();
            Guarantee::where('object_id', $objectId)->delete();
            GuaranteePayment::where('object_id', $objectId)->delete();

            foreach ($importData['СВОДНАЯ ГЭС-2'] as $index => $row) {
                if ($index < 6 || empty($row[1])) continue;

                $contractName = $this->sanitizer->set($row[1])->get();
                $contract = Contract::where('name', $contractName)->first();

                $isAct = mb_strpos($row[6], 'Акт') !== false;
                $isTotal = mb_strpos($row[6], 'Итого') !== false;

                if (! $contract) {
                    $contract = Contract::create([
                        'parent_id' => null,
                        'type_id' => Contract::TYPE_MAIN,
                        'amount_type_id' => Contract::AMOUNT_TYPE_MAIN,
                        'company_id' => 1,
                        'object_id' => $objectId,
                        'name' => $contractName,
                        'description' => $row[6],
                        'start_date' => null,
                        'end_date' => null,
                        'amount' => empty($row[7]) ? 0 : $this->prepareAmount($row[7]),
                        'stage_id' => 0,
                        'status_id' => Status::STATUS_ACTIVE,
                        'currency' => 'RUB',
                        'currency_rate' => 1,
                    ]);

                    if (! empty($row[8])) {
                        ContractAvans::create([
                            'contract_id' => $contract->id,
                            'company_id' => $contract->company_id,
                            'object_id' => $contract->object_id,
                            'amount' => $this->prepareAmount($row[8]),
                            'status_id' => Status::STATUS_ACTIVE,
                            'currency' => $contract->currency,
                            'currency_rate' => $contract->currency_rate,
                        ]);
                    }

                    if (! empty($row[9])) {
                        ContractReceivedAvans::create([
                            'contract_id' => $contract->id,
                            'company_id' => $contract->company_id,
                            'object_id' => $contract->object_id,
                            'date' => null,
                            'amount' => $this->prepareAmount($row[9]),
                            'description' => $row[11],
                            'status_id' => Status::STATUS_ACTIVE,
                            'currency' => $contract->currency,
                            'currency_rate' => $contract->currency_rate,
                        ]);
                    }
                } else {
                    if ($isAct) {
                        $date = null;
                        $number = mb_substr($row[6], 0, 30);
                        if (str_contains($row[6], 'от ')) {
                            $number = mb_substr($row[6], 0, mb_strpos($row[6], 'от '));
                            try {
                                $date = Carbon::parse(mb_substr($row[6], mb_strpos($row[6], 'от ') + 3, 10))->format('Y-m-d');
                            } catch (\Exception $e) {
                                try {
                                    $date = Carbon::parse(mb_substr($row[6], mb_strpos($row[6], 'от ') + 3, 8))->format('Y-m-d');
                                } catch (\Exception $e) {
                                    $date = null;
                                }
                            }
                        }
                        $act = Act::create([
                            'contract_id' => $contract->id,
                            'company_id' => $contract->company_id,
                            'object_id' => $contract->object_id,
                            'number' => $number,
                            'date' => $date,
                            'amount' => empty($row[12]) ? 0 : $this->prepareAmount($row[12]),
                            'amount_avans' => empty($row[13]) ? 0 : $this->prepareAmount($row[13]),
                            'amount_deposit' => empty($row[14]) ? 0 : $this->prepareAmount($row[14]),
                            'description' => null,
                            'status_id' => Status::STATUS_ACTIVE,
                            'currency' => $contract->currency,
                            'currency_rate' => $contract->currency_rate,
                        ]);

                        $act->update([
                            'amount_need_paid' => $act->amount - $act->amount_avans - $act->amount_deposit
                        ]);

                        if (!empty($row[16])) {
                            ActPayment::create([
                                'contract_id' => $contract->id,
                                'act_id' => $act->id,
                                'company_id' => $act->company_id,
                                'object_id' => $act->object_id,
                                'date' => null,
                                'amount' => $this->prepareAmount($row[16]),
                                'description' => $row[18],
                                'status_id' => Status::STATUS_ACTIVE,
                                'currency' => $act->currency,
                                'currency_rate' => $act->currency_rate,
                            ]);
                        }

                        $guarantee = Guarantee::create([
                            'contract_id' => $contract->id,
                            'company_id' => $contract->company_id,
                            'object_id' => $contract->object_id,
                            'fact_amount' => $act->amount_deposit,
                            'currency' => 'RUB',
                        ]);

                        if (isset($row[20]) && !empty($row[20])) {
                            GuaranteePayment::create([
                                'contract_id' => $contract->id,
                                'guarantee_id' => $guarantee->id,
                                'company_id' => $contract->company_id,
                                'object_id' => $contract->object_id,
                                'amount' => $this->prepareAmount($row[20]),
                                'description' => is_numeric($row[22]) ? Carbon::parse(Date::excelToDateTimeObject($row[22]))->format('d.m.Y') : $row[22],
                                'status_id' => Status::STATUS_ACTIVE,
                                'currency' => 'RUB',
                            ]);

                            $guarantee->updatePayments();
                        }
                    } else if (! $isTotal) {
                        $parent = $contract;
                        if (empty($row[6])) {
                            $name = 'ДС 1000';
                        } else {
                            $name = $row[6];
                        }
                        $contract = Contract::create([
                            'parent_id' => $parent->id,
                            'type_id' => Contract::TYPE_ADDITIONAL,
                            'amount_type_id' => Contract::AMOUNT_TYPE_ADDITIONAL,
                            'company_id' => 1,
                            'object_id' => $objectId,
                            'name' => $name,
                            'description' => null,
                            'start_date' => null,
                            'end_date' => null,
                            'amount' => empty($row[7]) ? 0 : $this->prepareAmount($row[7]),
                            'stage_id' => 0,
                            'status_id' => Status::STATUS_ACTIVE,
                            'currency' => 'RUB',
                            'currency_rate' => 1,
                        ]);

                        if (! empty($row[8])) {
                            ContractAvans::create([
                                'contract_id' => $contract->id,
                                'company_id' => $contract->company_id,
                                'object_id' => $contract->object_id,
                                'amount' => $this->prepareAmount($row[8]),
                                'status_id' => Status::STATUS_ACTIVE,
                                'currency' => $contract->currency,
                                'currency_rate' => $contract->currency_rate,
                            ]);
                        }

                        if (! empty($row[9])) {
                            ContractReceivedAvans::create([
                                'contract_id' => $contract->id,
                                'company_id' => $contract->company_id,
                                'object_id' => $contract->object_id,
                                'date' => null,
                                'amount' => $this->prepareAmount($row[9]),
                                'description' => $row[11],
                                'status_id' => Status::STATUS_ACTIVE,
                                'currency' => $contract->currency,
                                'currency_rate' => $contract->currency_rate,
                            ]);
                        }
                    } else if ($isTotal) {
                        $contract->params = json_encode($row);
                        $contract->update();
                    }
                }
            }
        }

        return redirect()->back();
    }

    private function prepareAmount(string|null|float $amount): float
    {
        if (is_string($amount)) {
            $amount = explode("\n", $amount)[0];
            $amount = str_replace(' ', '', $amount);
            $amount = str_replace(',', '.', $amount);
            $amount = (float) $amount;
        }

        return $amount;
    }
}
