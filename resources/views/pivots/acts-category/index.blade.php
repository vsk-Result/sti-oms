@extends('layouts.app')

@section('title', 'Отчет по категориям')
@section('toolbar-title', 'Отчет по категориям')
@section('breadcrumbs', Breadcrumbs::render('pivots.acts_category.index'))

@section('content')
    @include('pivots.acts-category.modals.filter')

    <div class="card mb-5 mb-xl-8 border-0">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
            </div>

            <div class="card-toolbar">
                <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#filterActCategoryModal">
                    <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                        </svg>
                    </span>
                    Фильтр
                </button>

                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <form action="{{ route('pivots.acts_category.exports.store') . (strpos(request()->fullUrl(), '?') !== false ? substr(request()->fullUrl(), strpos(request()->fullUrl(), '?')) : '') }}" method="POST" class="hidden">
                        @csrf
                        <a
                                href="javascript:void(0);"
                                class="btn btn-light-primary"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                        >
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black"></rect>
                                            <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black"></path>
                                            <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4"></path>
                                        </svg>
                                    </span>
                            Экспорт в Excel
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-0 ps-0">
            <div class="table-responsive freeze-table">
                <table class="table table-bordered align-middle table-row-dashed fs-6">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 gs-0 cell-center">
                            <th class="min-w-200px ps-2"></th>
                            <th colspan="9">Доходы</th>
                            <th colspan="3">Расходы</th>
                            <th colspan="2">Отклонение</th>
                        </tr>
                        <tr class="text-start text-muted fw-bolder fs-7 gs-0 cell-center">
                            <th class="min-w-200px ps-2">Категория</th>
                            <th class="min-w-200px">Сумма по договору</th>
                            <th class="min-w-200px">% по договору</th>
                            <th class="min-w-200px">Итого выполнение</th>
                            <th class="min-w-200px">% выполнение</th>
                            <th class="min-w-200px">Остаток к выполнению</th>
                            <th class="min-w-200px">Оплата</th>
                            <th class="min-w-200px">% оплаты</th>
                            <th class="min-w-200px">Остаток к оплате</th>
                            <th class="min-w-200px">% остатка к оплате</th>
                            <th class="min-w-200px">Бюджет</th>
                            <th class="min-w-200px">Оплата</th>
                            <th class="min-w-200px">% оплаты</th>
                            <th class="min-w-200px">Сумма</th>
                            <th class="min-w-200px">% суммы</th>
                        </tr>
                    </thead>

                    @inject('currencyExchangeService', 'App\Services\CurrencyExchangeRateService')

                    @php
                        $EURExchangeRate = $currencyExchangeService->getExchangeRate(\Carbon\Carbon::now()->format('Y-m-d'), 'EUR');

                         $totalMaterialAmount = $acts->sum('amount');
                         $totalRadAmount = $acts->sum('rad_amount');
                         $totalOpsteAmount = $acts->sum('opste_amount');

                         if ($EURExchangeRate) {
                             $totalMaterialAmount += $actsEUR->sum('amount') * $EURExchangeRate->rate;
                             $totalRadAmount += $actsEUR->sum('rad_amount') * $EURExchangeRate->rate;
                             $totalOpsteAmount += $actsEUR->sum('opste_amount') * $EURExchangeRate->rate;
                         }

                         $totalContractAmount = 0;
                         $totalMaterialContractAmount = 0;
                         $totalRadContractAmount = 0;
                         $totalOpsteContractAmount = 0;

                         $mainContracts = App\Models\Contract\Contract::whereIn('object_id', $activeObjectIds)->where('type_id', App\Models\Contract\Contract::TYPE_MAIN)->get();

                         foreach ($mainContracts as $contract) {
                            $totalContractAmount += $contract->getAmount('RUB');
                            $totalMaterialContractAmount += $contract->getMaterialAmount('RUB');
                            $totalRadContractAmount += $contract->getRadAmount('RUB');
                            $totalOpsteContractAmount += $contract->getOpsteAmount('RUB');

                            if ($EURExchangeRate) {
                                $totalContractAmount += $contract->getAmount('EUR') * $EURExchangeRate->rate;
                                $totalMaterialContractAmount += $contract->getMaterialAmount('EUR') * $EURExchangeRate->rate;
                                $totalRadContractAmount += $contract->getRadAmount('EUR') * $EURExchangeRate->rate;
                                $totalOpsteContractAmount += $contract->getOpsteAmount('EUR') * $EURExchangeRate->rate;
                            }
                        }

                         $totalMaterialPaidAmount = 0;
                         $totalRadPaidAmount = 0;
                         $totalOpstePaidAmount = 0;

                         foreach ($activeObjects as $aobject) {
                             $receivePaymentsRUB = $aobject->payments()
                                ->where('payment_type_id', App\Models\Payment::PAYMENT_TYPE_NON_CASH)
                                ->where('company_id', 1)
                                ->whereIn('organization_sender_id', $aobject->customers->pluck('id')->toArray())
                                ->where('currency', 'RUB')
                                ->get();

                            $totalMaterialPaidAmount += $receivePaymentsRUB->where('category', \App\Models\Payment::CATEGORY_MATERIAL)->sum('amount');
                            $totalRadPaidAmount += $receivePaymentsRUB->where('category', \App\Models\Payment::CATEGORY_RAD)->sum('amount');
                            $totalOpstePaidAmount += $receivePaymentsRUB->where('category', \App\Models\Payment::CATEGORY_OPSTE)->sum('amount');

                            if ($EURExchangeRate) {
                                $receivePaymentsEUR = $aobject->payments()
                                    ->where('payment_type_id', App\Models\Payment::PAYMENT_TYPE_NON_CASH)
                                    ->where('company_id', 1)
                                    ->whereIn('organization_sender_id', $aobject->customers->pluck('id')->toArray())
                                    ->where('currency', 'EUR')
                                    ->get();

                                $totalMaterialPaidAmount += $receivePaymentsEUR->where('category', \App\Models\Payment::CATEGORY_MATERIAL)->sum('currency_amount') * $EURExchangeRate->rate;
                                $totalRadPaidAmount += $receivePaymentsEUR->where('category', \App\Models\Payment::CATEGORY_RAD)->sum('currency_amount') * $EURExchangeRate->rate;
                                $totalOpstePaidAmount += $receivePaymentsEUR->where('category', \App\Models\Payment::CATEGORY_OPSTE)->sum('currency_amount') * $EURExchangeRate->rate;
                            }
                         }

                         $totalMaterialLeftPaidAmount = $totalMaterialContractAmount - $totalMaterialPaidAmount;
                         $totalRadLeftPaidAmount = $totalRadContractAmount - $totalRadPaidAmount;
                         $totalOpsteLeftPaidAmount = $totalOpsteContractAmount - $totalOpstePaidAmount;

                         $totalAmount = $totalMaterialAmount + $totalRadAmount + $totalOpsteAmount;
                         $totalPaidAmount = $totalMaterialPaidAmount + $totalRadPaidAmount + $totalOpstePaidAmount;
                         $totalLeftPaidAmount = $totalMaterialLeftPaidAmount + $totalRadLeftPaidAmount + $totalOpsteLeftPaidAmount;
                    @endphp

                    @php
                        $objectPaymentInfo = [];
                        $totalPayments = 0;
                        $totalMaterialPayments = 0;
                        $totalRadPayments = 0;
                        $totalRadOwnPayments = 0;
                        $totalRadContractorPayments = 0;
                        $totalServicePayments = 0;
                        $totalServiceAmountPayments = 0;
                        $totalServiceGeneralPayments = 0;

                        $objectBudgetInfo = [];
                        $totalBudget = 0;
                        $totalBudgetMaterial = 0;
                        $totalBudgetRad = 0;
                        $totalBudgetRadOwn = 0;
                        $totalBudgetRadContractor = 0;
                        $totalBudgetService = 0;
                        $totalBudgetServiceAmount = 0;
                        $totalBudgetServiceGeneral = 0;

                        foreach ($activeObjects as $aobject) {
                            $objectPayments = $aobject->payments->where('amount', '<', 0);

                            $materialRadPayments = $objectPayments
                                ->whereIn('code', ['7.8', '7.8.1', '7.8.2', '7.9', '7.9.1', '7.9.2', '7.10'])
                                ->where('category', \App\Models\Payment::CATEGORY_MATERIAL)
                                ->sum('amount');

                             $materialPayments = $objectPayments
                                ->whereNotIn('code', ['7.8', '7.8.1', '7.8.2', '7.9', '7.9.1', '7.9.2', '7.10'])
                                ->where('category', \App\Models\Payment::CATEGORY_MATERIAL)
                                ->sum('amount');

                             $radPayments = $objectPayments
                                ->where('category', \App\Models\Payment::CATEGORY_RAD)
                                ->sum('amount');

                             $radOwnPayments = $objectPayments
                                ->whereIn('code', ['7.8', '7.8.1', '7.8.2', '7.9', '7.9.1', '7.9.2', '7.10'])
                                ->where('category', \App\Models\Payment::CATEGORY_RAD)
                                ->sum('amount');

                             $serviceRadPayments = $objectPayments
                                ->whereIn('code', ['7.8', '7.8.1', '7.8.2', '7.9', '7.9.1', '7.9.2', '7.10'])
                                ->whereNotIn('category', [\App\Models\Payment::CATEGORY_RAD, \App\Models\Payment::CATEGORY_MATERIAL])
                                ->sum('amount');

                             $servicePayments = $objectPayments
                                ->whereNotIn('code', ['7.8', '7.8.1', '7.8.2', '7.9', '7.9.1', '7.9.2', '7.10'])
                                ->whereNotIn('category', [\App\Models\Payment::CATEGORY_RAD, \App\Models\Payment::CATEGORY_MATERIAL])
                                ->sum('amount');

                             $generalCosts = $aobject->generalCosts()->sum('amount');

                             $radPayments += $materialRadPayments + $serviceRadPayments;

                             $radOwn = $materialRadPayments + $serviceRadPayments + $radOwnPayments;

                             $objectPaymentInfo[$aobject->id] = [
                                'rad' => $radPayments,
                                'rad_own' => $radOwn,
                                'rad_contractor' => $radPayments - $radOwn,
                                'material' => $materialPayments,
                                'service' => $servicePayments + $generalCosts,
                                'service_amount' => $servicePayments,
                                'service_general_cost' => $generalCosts,
                                'total' => $radPayments + $materialPayments + $servicePayments + $generalCosts
                            ];

                             $objectBudgets = $aobject->budgets;

                             $totalPayments += $radPayments + $materialPayments + $servicePayments + $generalCosts;
                             $totalMaterialPayments += $materialPayments;
                             $totalRadPayments += $radPayments;
                             $totalRadOwnPayments += $radOwn;
                             $totalServicePayments += $servicePayments + $generalCosts;
                             $totalServiceAmountPayments += $servicePayments;
                             $totalServiceGeneralPayments += $generalCosts;

                             $objectBudgetInfo[$aobject->id] = [
                                 'rad' => $objectBudgets->whereIn('type_id', [\App\Models\Object\Budget::TYPE_RAD_SELF, \App\Models\Object\Budget::TYPE_RAD_CONTRACTORS])->sum('amount'),
                                'rad_own' => $objectBudgets->where('type_id', \App\Models\Object\Budget::TYPE_RAD_SELF)->sum('amount'),
                                'rad_contractor' => $objectBudgets->where('type_id', \App\Models\Object\Budget::TYPE_RAD_CONTRACTORS)->sum('amount'),
                                'material' => $objectBudgets->where('type_id', \App\Models\Object\Budget::TYPE_MATERIAL)->sum('amount'),
                                'service' => $objectBudgets->whereIn('type_id', [\App\Models\Object\Budget::TYPE_SERVICE_OBJECT, \App\Models\Object\Budget::TYPE_SERVICE_GENERAL])->sum('amount'),
                                'service_amount' => $objectBudgets->where('type_id', \App\Models\Object\Budget::TYPE_SERVICE_OBJECT)->sum('amount'),
                                'service_general_cost' => $objectBudgets->where('type_id', \App\Models\Object\Budget::TYPE_SERVICE_GENERAL)->sum('amount'),
                                'total' => $objectBudgets->sum('amount')
                              ];

                             $totalBudget += $objectBudgetInfo[$aobject->id]['total'];
                             $totalBudgetMaterial += $objectBudgetInfo[$aobject->id]['material'];
                            $totalBudgetRadOwn += $objectBudgetInfo[$aobject->id]['rad_own'];
                            $totalBudgetRadContractor += $objectBudgetInfo[$aobject->id]['rad_contractor'];
                            $totalBudgetRad += $objectBudgetInfo[$aobject->id]['rad'];
                            $totalBudgetServiceAmount += $objectBudgetInfo[$aobject->id]['service_amount'];
                            $totalBudgetServiceGeneral += $objectBudgetInfo[$aobject->id]['service_general_cost'];
                            $totalBudgetService += $objectBudgetInfo[$aobject->id]['service'];
                         }

                         $totalRadContractorPayments = $totalRadPayments - $totalRadOwnPayments;

                        $totalBudgetDeviation = $totalBudget != 0 ? (abs($totalPayments) / $totalBudget * 100) : 0;
                        $totalBudgetMaterialDeviation = $totalBudgetMaterial != 0 ? (abs($totalMaterialPayments) / $totalBudgetMaterial * 100) : 0;
                        $totalBudgetRadDeviation = $totalBudgetRad != 0 ? (abs($totalRadPayments) / $totalBudgetRad * 100) : 0;
                        $totalBudgetRadOwnDeviation = $totalBudgetRadOwn != 0 ? (abs($totalRadOwnPayments) / $totalBudgetRadOwn * 100) : 0;
                        $totalBudgetRadContractorDeviation = $totalBudgetRadContractor != 0 ? (abs($totalRadContractorPayments) / $totalBudgetRadContractor * 100) : 0;
                        $totalBudgetServiceDeviation = $totalBudgetService != 0 ? (abs($totalServicePayments) / $totalBudgetService * 100) : 0;
                        $totalBudgetServiceAmountDeviation = $totalBudgetServiceAmount != 0 ? (abs($totalServiceAmountPayments) / $totalBudgetServiceAmount * 100) : 0;
                        $totalBudgetServiceGeneralDeviation = $totalBudgetServiceGeneral != 0 ? (abs($totalServiceGeneralPayments) / $totalBudgetServiceGeneral * 100) : 0;
                    @endphp

                    <tbody class="text-gray-600 fw-bold fs-7">
                        <tr class="object-row fw-bolder">
                            <td class="ps-2 fw-bolder">Итого</td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalContractAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">100%</td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format($totalContractAmount != 0 ? $totalAmount / $totalContractAmount * 100 : 0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalContractAmount - $totalAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalPaidAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format($totalContractAmount != 0 ? $totalPaidAmount / $totalContractAmount * 100 : 0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalLeftPaidAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format($totalContractAmount != 0 ? $totalLeftPaidAmount / $totalContractAmount * 100 : 0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalBudget, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalPayments, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center {{ $totalBudgetDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                {{ number_format($totalBudgetDeviation) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(min(0, $totalBudget + $totalPayments), 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center {{ $totalBudgetDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                {{ number_format(min(0, 100 - $totalBudgetDeviation)) . '%' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-2 fw-bolder">Материалы</td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalMaterialContractAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format($totalContractAmount != 0 ? $totalMaterialContractAmount / $totalContractAmount * 100 : 0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalMaterialAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format($totalMaterialContractAmount != 0 ? $totalMaterialAmount / $totalMaterialContractAmount * 100 : 0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalMaterialContractAmount - $totalMaterialAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalMaterialPaidAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format($totalMaterialContractAmount != 0 ? $totalMaterialPaidAmount / $totalMaterialContractAmount * 100 : 0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalMaterialLeftPaidAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format($totalMaterialContractAmount != 0 ? $totalMaterialLeftPaidAmount / $totalMaterialContractAmount * 100 : 0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalBudgetMaterial, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalMaterialPayments, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center {{ $totalBudgetMaterialDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                {{ number_format($totalBudgetMaterialDeviation) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(min(0, $totalBudgetMaterial + $totalMaterialPayments), 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center {{ $totalBudgetMaterialDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                {{ number_format(min(0, 100 - $totalBudgetMaterialDeviation)) . '%' }}
                            </td>
                        </tr>

                        <tr>
                            <td class="ps-2 fw-bolder">Работы</td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalRadContractAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format($totalContractAmount != 0 ? $totalRadContractAmount / $totalContractAmount * 100 : 0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalRadAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format($totalRadContractAmount != 0 ? $totalRadAmount / $totalRadContractAmount * 100 : 0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalRadContractAmount - $totalRadAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalRadPaidAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format($totalRadContractAmount != 0 ? $totalRadPaidAmount / $totalRadContractAmount * 100 : 0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalRadLeftPaidAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format($totalRadContractAmount != 0 ? $totalRadLeftPaidAmount / $totalRadContractAmount * 100 : 0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalBudgetRad, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalRadPayments, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center {{ $totalBudgetRadDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                {{ number_format($totalBudgetRadDeviation) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(min(0, $totalBudgetRad + $totalRadPayments), 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center {{ $totalBudgetRadDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                {{ number_format(min(0, 100 - $totalBudgetRadDeviation)) . '%' }}
                            </td>
                        </tr>

                        <tr class="fst-italic">
                            <td class="ps-2 fw-bolder ps-8">Подрядчики</td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format(0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format(0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format(0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format(0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalBudgetRadOwn, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalRadOwnPayments, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center {{ $totalBudgetRadOwnDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                {{ number_format($totalBudgetRadOwnDeviation) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(min(0, $totalBudgetRadOwn + $totalRadOwnPayments), 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center {{ $totalBudgetRadOwnDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                {{ number_format(min(0, 100 - $totalBudgetRadOwnDeviation)) . '%' }}
                            </td>
                        </tr>

                        <tr class="fst-italic">
                            <td class="ps-2 fw-bolder ps-8">Свои силы</td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format(0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format(0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format(0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format(0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalBudgetRadContractor, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalRadContractorPayments, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center {{ $totalBudgetRadContractorDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                {{ number_format($totalBudgetRadContractorDeviation) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(min(0, $totalBudgetRadContractor + $totalRadContractorPayments), 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center {{ $totalBudgetRadContractorDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                {{ number_format(min(0, 100 - $totalBudgetRadContractorDeviation)) . '%' }}
                            </td>
                        </tr>

                        <tr>
                            <td class="ps-2 fw-bolder">Накладные</td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalOpsteContractAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format($totalContractAmount != 0 ? $totalOpsteContractAmount / $totalContractAmount * 100 : 0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalOpsteAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format($totalOpsteContractAmount != 0 ? $totalOpsteAmount / $totalOpsteContractAmount * 100 : 0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalOpsteContractAmount - $totalOpsteAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalOpstePaidAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format($totalOpsteContractAmount != 0 ? $totalOpstePaidAmount / $totalOpsteContractAmount * 100 : 0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalOpsteLeftPaidAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format($totalOpsteContractAmount != 0 ? $totalOpsteLeftPaidAmount / $totalOpsteContractAmount * 100 : 0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalBudgetService, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalServicePayments, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center {{ $totalBudgetServiceDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                {{ number_format($totalBudgetServiceDeviation) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(min(0, $totalBudgetService + $totalServicePayments), 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center {{ $totalBudgetServiceDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                {{ number_format(min(0, 100 - $totalBudgetServiceDeviation)) . '%' }}
                            </td>
                        </tr>

                        <tr class="fst-italic">
                            <td class="ps-2 fw-bolder ps-8">Накладные расходы объекта</td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format(0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format(0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format(0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format(0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalBudgetServiceAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalServiceAmountPayments, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center {{ $totalBudgetServiceAmountDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                {{ number_format($totalBudgetServiceAmountDeviation) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(min(0, $totalBudgetServiceAmount + $totalServiceAmountPayments), 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center {{ $totalBudgetServiceAmountDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                {{ number_format(min(0, 100 - $totalBudgetServiceAmountDeviation)) . '%' }}
                            </td>
                        </tr>

                        <tr class="fst-italic">
                            <td class="ps-2 fw-bolder ps-8">Общие затраты офиса</td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format(0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format(0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format(0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format(0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalBudgetServiceGeneral, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalServiceGeneralPayments, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center {{ $totalBudgetServiceGeneralDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                {{ number_format($totalBudgetServiceGeneralDeviation) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format(min(0, $totalBudgetServiceGeneral + $totalServiceGeneralPayments), 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center {{ $totalBudgetServiceGeneralDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                {{ number_format(min(0, 100 - $totalBudgetServiceGeneralDeviation)) . '%' }}
                            </td>
                        </tr>

                        @foreach($activeObjects as $object)
                            @php
                                $acts = $object->acts()->where('currency', 'RUB')->get();
                                $actsEUR = $object->acts()->where('currency', 'EUR')->get();

                                $totalMaterialAmount = $acts->sum('amount');
                                $totalRadAmount = $acts->sum('rad_amount');
                                $totalOpsteAmount = $acts->sum('opste_amount');

                                if ($EURExchangeRate) {
                                     $totalMaterialAmount += $actsEUR->sum('amount') * $EURExchangeRate->rate;
                                     $totalRadAmount += $actsEUR->sum('rad_amount') * $EURExchangeRate->rate;
                                     $totalOpsteAmount += $actsEUR->sum('opste_amount') * $EURExchangeRate->rate;
                                 }

                                $totalContractAmount = 0;
                                $totalMaterialContractAmount = 0;
                                $totalRadContractAmount = 0;
                                $totalOpsteContractAmount = 0;

                                $mainContracts = $object->contracts()->where('type_id', App\Models\Contract\Contract::TYPE_MAIN)->get();

                                foreach ($mainContracts as $contract) {
                                    $totalContractAmount += $contract->getAmount('RUB');
                                    $totalMaterialContractAmount += $contract->getMaterialAmount('RUB');
                                    $totalRadContractAmount += $contract->getRadAmount('RUB');
                                    $totalOpsteContractAmount += $contract->getOpsteAmount('RUB');

                                    if ($EURExchangeRate) {
                                        $totalContractAmount += $contract->getAmount('EUR') * $EURExchangeRate->rate;
                                        $totalMaterialContractAmount += $contract->getMaterialAmount('EUR') * $EURExchangeRate->rate;
                                        $totalRadContractAmount += $contract->getRadAmount('EUR') * $EURExchangeRate->rate;
                                        $totalOpsteContractAmount += $contract->getOpsteAmount('EUR') * $EURExchangeRate->rate;
                                    }
                                }

                                $receivePaymentsRUB = $object->payments()
                                    ->where('payment_type_id', App\Models\Payment::PAYMENT_TYPE_NON_CASH)
                                    ->where('company_id', 1)
                                    ->whereIn('organization_sender_id', $object->customers->pluck('id')->toArray())
                                    ->where('currency', 'RUB')
                                    ->get();

                                $totalMaterialPaidAmount = $receivePaymentsRUB->where('category', \App\Models\Payment::CATEGORY_MATERIAL)->sum('amount');
                                $totalRadPaidAmount = $receivePaymentsRUB->where('category', \App\Models\Payment::CATEGORY_RAD)->sum('amount');
                                $totalOpstePaidAmount = $receivePaymentsRUB->where('category', \App\Models\Payment::CATEGORY_OPSTE)->sum('amount');

                                if ($EURExchangeRate) {
                                    $receivePaymentsEUR = $object->payments()
                                        ->where('payment_type_id', App\Models\Payment::PAYMENT_TYPE_NON_CASH)
                                        ->where('company_id', 1)
                                        ->whereIn('organization_sender_id', $object->customers->pluck('id')->toArray())
                                        ->where('currency', 'EUR')
                                        ->get();

                                    $totalMaterialPaidAmount += $receivePaymentsEUR->where('category', \App\Models\Payment::CATEGORY_MATERIAL)->sum('currency_amount') * $EURExchangeRate->rate;
                                    $totalRadPaidAmount += $receivePaymentsEUR->where('category', \App\Models\Payment::CATEGORY_RAD)->sum('currency_amount') * $EURExchangeRate->rate;
                                    $totalOpstePaidAmount += $receivePaymentsEUR->where('category', \App\Models\Payment::CATEGORY_OPSTE)->sum('currency_amount') * $EURExchangeRate->rate;
                                }

                                $totalMaterialLeftPaidAmount = $totalMaterialContractAmount - $totalMaterialPaidAmount;
                                $totalRadLeftPaidAmount = $totalRadContractAmount - $totalRadPaidAmount;
                                $totalOpsteLeftPaidAmount = $totalOpsteContractAmount - $totalOpstePaidAmount;

                                $totalAmount = $totalMaterialAmount + $totalRadAmount + $totalOpsteAmount;
                                $totalPaidAmount = $totalMaterialPaidAmount + $totalRadPaidAmount + $totalOpstePaidAmount;
                                $totalLeftPaidAmount = $totalMaterialLeftPaidAmount + $totalRadLeftPaidAmount + $totalOpsteLeftPaidAmount;

                                $totalPaidAmountPercent = round($totalContractAmount != 0 ? $totalPaidAmount / $totalContractAmount * 100 : 0);
                                $totalMaterialPaidAmountPercent = round($totalMaterialContractAmount != 0 ? $totalMaterialPaidAmount / $totalMaterialContractAmount * 100 : 0);
                                $totalRadPaidAmountPercent = round($totalRadContractAmount != 0 ? $totalRadPaidAmount / $totalRadContractAmount * 100 : 0);
                                $totalOpstePaidAmountPercent = round($totalOpsteContractAmount != 0 ? $totalOpstePaidAmount / $totalOpsteContractAmount * 100 : 0);

                                $totalAmountPercent = round($totalContractAmount != 0 ? $totalAmount / $totalContractAmount * 100 : 0);
                                $totalMaterialAmountPercent = round($totalMaterialContractAmount != 0 ? $totalMaterialAmount / $totalMaterialContractAmount * 100 : 0);
                                $totalRadAmountPercent = round($totalRadContractAmount != 0 ? $totalRadAmount / $totalRadContractAmount * 100 : 0);
                                $totalOpsteAmountPercent = round($totalOpsteContractAmount != 0 ? $totalOpsteAmount / $totalOpsteContractAmount * 100 : 0);

                                $totalBudgetDeviation = $objectBudgetInfo[$object->id]['total'] != 0 ? (abs($objectPaymentInfo[$object->id]['total']) / $objectBudgetInfo[$object->id]['total'] * 100) : 0;
                                $totalBudgetMaterialDeviation = $objectBudgetInfo[$object->id]['material'] != 0 ? (abs($objectPaymentInfo[$object->id]['material']) / $objectBudgetInfo[$object->id]['material'] * 100) : 0;
                                $totalBudgetRadDeviation = $objectBudgetInfo[$object->id]['rad'] != 0 ? (abs($objectPaymentInfo[$object->id]['rad']) / $objectBudgetInfo[$object->id]['rad'] * 100) : 0;
                                $totalBudgetRadOwnDeviation = $objectBudgetInfo[$object->id]['rad_own'] != 0 ? (abs($objectPaymentInfo[$object->id]['rad_own']) / $objectBudgetInfo[$object->id]['rad_own'] * 100) : 0;
                                $totalBudgetRadContractorDeviation = $objectBudgetInfo[$object->id]['rad_contractor'] != 0 ? (abs($objectPaymentInfo[$object->id]['rad_contractor']) / $objectBudgetInfo[$object->id]['rad_contractor'] * 100) : 0;
                                $totalBudgetServiceDeviation = $objectBudgetInfo[$object->id]['service'] != 0 ? (abs($objectPaymentInfo[$object->id]['service']) / $objectBudgetInfo[$object->id]['service'] * 100) : 0;
                                $totalBudgetServiceAmountDeviation = $objectBudgetInfo[$object->id]['service_amount'] != 0 ? (abs($objectPaymentInfo[$object->id]['service_amount']) / $objectBudgetInfo[$object->id]['service_amount'] * 100) : 0;
                                $totalBudgetServiceGeneralDeviation = $objectBudgetInfo[$object->id]['service_general_cost'] != 0 ? (abs($objectPaymentInfo[$object->id]['service_general_cost']) / $objectBudgetInfo[$object->id]['service_general_cost'] * 100) : 0;
                            @endphp
                            <tr class="object-row fw-bolder">
                                <td class="ps-2 fw-bolder collapse-trigger cursor-pointer" data-trigger="collapse_{{ $object->id }}">
                                    <span class="fs-5">+</span>
                                    {{ $object->getName() }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($totalContractAmount, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">100%</td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($totalAmount, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalAmountPercent < 0 || $totalAmountPercent > 100 ? 'fw-bolder text-danger' : '' }}">
                                    {{ number_format($totalAmountPercent) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($totalContractAmount - $totalAmount, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($totalPaidAmount, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalPaidAmountPercent < 0 || $totalPaidAmountPercent > 100 ? 'fw-bolder text-danger' : '' }}">
                                    {{ number_format($totalPaidAmountPercent) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($totalLeftPaidAmount, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format($totalContractAmount != 0 ? $totalLeftPaidAmount / $totalContractAmount * 100 : 0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($objectBudgetInfo[$object->id]['total'], 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($objectPaymentInfo[$object->id]['total'], 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalBudgetDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                    {{ number_format($totalBudgetDeviation) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(min(0, $objectBudgetInfo[$object->id]['total'] + $objectPaymentInfo[$object->id]['total']), 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalBudgetDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                    {{ number_format(min(0, 100 - $totalBudgetDeviation)) . '%' }}
                                </td>
                            </tr>
                            <tr class="collapse-row" data-trigger="collapse_{{ $object->id }}" style="display: none;">
                                <td class="ps-2 fst-italic">Материалы</td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($totalMaterialContractAmount, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format($totalContractAmount != 0 ? $totalMaterialContractAmount / $totalContractAmount * 100 : 0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($totalMaterialAmount, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalMaterialAmountPercent < 0 || $totalMaterialAmountPercent > 100 ? 'fw-bolder text-danger' : '' }}">
                                    {{ number_format($totalMaterialAmountPercent) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($totalMaterialContractAmount - $totalMaterialAmount, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($totalMaterialPaidAmount, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalMaterialPaidAmountPercent < 0 || $totalMaterialPaidAmountPercent > 100 ? 'fw-bolder text-danger' : '' }}">
                                    {{ number_format($totalMaterialPaidAmountPercent) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($totalMaterialLeftPaidAmount, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format($totalMaterialContractAmount != 0 ? $totalMaterialLeftPaidAmount / $totalMaterialContractAmount * 100 : 0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($objectBudgetInfo[$object->id]['material'], 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($objectPaymentInfo[$object->id]['material'], 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalBudgetMaterialDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                    {{ number_format($totalBudgetMaterialDeviation) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(min(0, $objectBudgetInfo[$object->id]['material'] + $objectPaymentInfo[$object->id]['material']), 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalBudgetMaterialDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                    {{ number_format(min(0, 100 - $totalBudgetMaterialDeviation)) . '%' }}
                                </td>
                            </tr>

                            <tr class="collapse-row" data-trigger="collapse_{{ $object->id }}" style="display: none;">
                                <td class="ps-2 fst-italic">Работы</td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($totalRadContractAmount, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format($totalContractAmount != 0 ? $totalRadContractAmount / $totalContractAmount * 100 : 0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($totalRadAmount, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalRadAmountPercent < 0 || $totalRadAmountPercent > 100 ? 'fw-bolder text-danger' : '' }}">
                                    {{ number_format($totalRadAmountPercent) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($totalRadContractAmount - $totalRadAmount, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($totalRadPaidAmount, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalRadPaidAmountPercent < 0 || $totalRadPaidAmountPercent > 100 ? 'fw-bolder text-danger' : '' }}">
                                    {{ number_format($totalRadPaidAmountPercent) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($totalRadLeftPaidAmount, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format($totalRadContractAmount != 0 ? $totalRadLeftPaidAmount / $totalRadContractAmount * 100 : 0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($objectBudgetInfo[$object->id]['rad'], 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($objectPaymentInfo[$object->id]['rad'], 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalBudgetRadDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                    {{ number_format($totalBudgetRadDeviation) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(min(0, $objectBudgetInfo[$object->id]['rad'] + $objectPaymentInfo[$object->id]['rad']), 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalBudgetRadDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                    {{ number_format(min(0, 100 - $totalBudgetRadDeviation)) . '%' }}
                                </td>
                            </tr>

                            <tr class="collapse-row" data-trigger="collapse_{{ $object->id }}" style="display: none;">
                                <td class="ps-8 fst-italic">Подрядчики</td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format(0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format(0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format(0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format(0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($objectBudgetInfo[$object->id]['rad_own'], 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($objectPaymentInfo[$object->id]['rad_own'], 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalBudgetRadOwnDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                    {{ number_format($totalBudgetRadOwnDeviation) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(min(0, $objectBudgetInfo[$object->id]['rad_own'] + $objectPaymentInfo[$object->id]['rad_own']), 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalBudgetRadOwnDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                    {{ number_format(min(0, 100 - $totalBudgetRadOwnDeviation)) . '%' }}
                                </td>
                            </tr>

                            <tr class="collapse-row" data-trigger="collapse_{{ $object->id }}" style="display: none;">
                                <td class="ps-8 fst-italic">Свои силы</td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format(0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format(0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format(0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format(0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($objectBudgetInfo[$object->id]['rad_contractor'], 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($objectPaymentInfo[$object->id]['rad_contractor'], 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalBudgetRadContractorDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                    {{ number_format($totalBudgetRadContractorDeviation) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(min(0, $objectBudgetInfo[$object->id]['rad_contractor'] + $objectPaymentInfo[$object->id]['rad_contractor']), 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalBudgetRadContractorDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                    {{ number_format(min(0, 100 - $totalBudgetRadContractorDeviation)) . '%' }}
                                </td>
                            </tr>

                            <tr class="collapse-row" data-trigger="collapse_{{ $object->id }}" style="display: none;">
                                <td class="ps-2 fst-italic">Накладные</td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($totalOpsteContractAmount, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format($totalContractAmount != 0 ? $totalOpsteContractAmount / $totalContractAmount * 100 : 0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($totalOpsteAmount, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalOpsteAmountPercent < 0 || $totalOpsteAmountPercent > 100 ? 'fw-bolder text-danger' : '' }}">
                                    {{ number_format($totalOpsteAmountPercent) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($totalOpsteContractAmount - $totalOpsteAmount, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($totalOpstePaidAmount, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalOpstePaidAmountPercent < 0 || $totalOpstePaidAmountPercent > 100 ? 'fw-bolder text-danger' : '' }}">
                                    {{ number_format($totalOpstePaidAmountPercent) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($totalOpsteLeftPaidAmount, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format($totalOpsteContractAmount != 0 ? $totalOpsteLeftPaidAmount / $totalOpsteContractAmount * 100 : 0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($objectBudgetInfo[$object->id]['service'], 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($objectPaymentInfo[$object->id]['service'], 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalBudgetServiceDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                    {{ number_format($totalBudgetServiceDeviation) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(min(0, $objectBudgetInfo[$object->id]['service'] + $objectPaymentInfo[$object->id]['service']), 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalBudgetServiceDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                    {{ number_format(min(0, 100 - $totalBudgetServiceDeviation)) . '%' }}
                                </td>
                            </tr>

                            <tr class="collapse-row" data-trigger="collapse_{{ $object->id }}" style="display: none;">
                                <td class="ps-8 fst-italic">Накладные расходы объекта</td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format(0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format(0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format(0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format(0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($objectBudgetInfo[$object->id]['service_amount'], 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($objectPaymentInfo[$object->id]['service_amount'], 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalBudgetServiceAmountDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                    {{ number_format($totalBudgetServiceAmountDeviation) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(min(0, $objectBudgetInfo[$object->id]['service_amount'] + $objectPaymentInfo[$object->id]['service_amount']), 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalBudgetServiceAmountDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                    {{ number_format(min(0, 100 - $totalBudgetServiceAmountDeviation)) . '%' }}
                                </td>
                            </tr>

                            <tr class="collapse-row" data-trigger="collapse_{{ $object->id }}" style="display: none;">
                                <td class="ps-8 fst-italic">Общие затраты офиса</td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format(0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format(0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format(0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ number_format(0) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($objectBudgetInfo[$object->id]['service_general_cost'], 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format($objectPaymentInfo[$object->id]['service_general_cost'], 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalBudgetServiceGeneralDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                    {{ number_format($totalBudgetServiceGeneralDeviation) . '%' }}
                                </td>
                                <td class="cell-center">
                                    {{ \App\Models\CurrencyExchangeRate::format(min(0, $objectBudgetInfo[$object->id]['service_general_cost'] + $objectPaymentInfo[$object->id]['service_general_cost']), 'RUB', 0, true) }}
                                </td>
                                <td class="cell-center {{ $totalBudgetServiceGeneralDeviation > 100 ? 'text-danger fw-bolder' : '' }}">
                                    {{ number_format(min(0, 100 - $totalBudgetServiceGeneralDeviation)) . '%' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            mainApp.initFreezeTable(1);
        });

        $(document).on('click', '.collapse-trigger', function() {
            const trigger = $(this).data('trigger');
            const isCollapsed = $(this).hasClass('collapsed');

            if (isCollapsed) {
                $(this).find('span').text('+');
                $(this).removeClass('collapsed');
                $(`.collapse-row[data-trigger="${trigger}"]`).hide();
                mainApp.initFreezeTable(1);
            } else {
                $(this).find('span').text('-');
                $(this).addClass('collapsed');
                $(`.collapse-row[data-trigger="${trigger}"]`).show();
                mainApp.initFreezeTable(1);
            }
        })
    </script>
@endpush

@push('styles')
    <style>
        .table td, .table th, .table tbody tr:last-child td {
            border: 1px solid #c8c8c8 !important;
            color: #3f4254;
        }

        .cell-center {
            text-align: right !important;
        }

        .cell-center {
            vertical-align: middle !important;
            text-align: center !important;
        }

        .total-row {
            background-color: #e7e7e7 !important;
            font-weight: bold !important;
        }

        .object-row {
            background-color: #f7f7f7 !important;
        }

        .divider-row td {
            height: 6px;
            padding: 0 !important;
        }
    </style>
@endpush
