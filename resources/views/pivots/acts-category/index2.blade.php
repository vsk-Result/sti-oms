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
            </div>
        </div>

        <div class="card-body p-0 ps-0">
            <div class="table-responsive freeze-table">
                <table class="table table-bordered align-middle table-row-dashed fs-6">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 gs-0 cell-center">
                            <th class="min-w-200px ps-2">Категория</th>
                            <th class="min-w-200px">Сумма по договору</th>
                            <th class="min-w-200px">% по договору</th>

                            @foreach($acts as $act)
                                <th class="min-w-200px">{{ $act->number }}</th>
                            @endforeach

                            <th class="min-w-200px">Итого выполнение</th>
                            <th class="min-w-200px">% выполнение</th>
                            <th class="min-w-200px">Остаток к выполнению</th>
                            <th class="min-w-200px">Оплата</th>
                            <th class="min-w-200px">% оплаты</th>
                            <th class="min-w-200px">Остаток к оплате</th>
                            <th class="min-w-200px">% остатка к оплате</th>
                        </tr>
                    </thead>

                    @php
                        $totalMaterialAmount = $acts->sum('amount');
                        $totalRadAmount = $acts->sum('rad_amount');
                        $totalOpsteAmount = $acts->sum('opste_amount');

                        $totalMaterialContractAmount = 0;
                        $totalRadContractAmount = 0;
                        $totalOpsteContractAmount = 0;

                        foreach (App\Models\Contract\Contract::whereIn('object_id', $activeObjectIds)->where('type_id', App\Models\Contract\Contract::TYPE_MAIN)->get() as $contract) {
                            $totalMaterialContractAmount += $contract->getMaterialAmount();
                            $totalRadContractAmount += $contract->getRadAmount();
                            $totalOpsteContractAmount += $contract->getOpsteAmount();
                        }

                        $totalMaterialPaidAmount = 0;
                        $totalRadPaidAmount = 0;
                        $totalOpstePaidAmount = 0;

                        foreach ($acts as $act) {
                            $object = $act->object;
                            $receivePaymentsFromCustomers = $object->payments()
                                ->where('payment_type_id', App\Models\Payment::PAYMENT_TYPE_NON_CASH)
                                ->where('company_id', 1)
                                ->whereIn('organization_sender_id', $object->customers->pluck('id')->toArray())
                                ->get();

                            $receiveMaterialFromCustomers = $receivePaymentsFromCustomers->where('amount', $act->amount)->where('category', \App\Models\Payment::CATEGORY_MATERIAL)->first();
                            if ($receiveMaterialFromCustomers) {
                                $totalMaterialPaidAmount += $act->amount;
                            }

                            $receiveRadFromCustomers = $receivePaymentsFromCustomers->where('amount', $act->rad_amount)->where('category', \App\Models\Payment::CATEGORY_RAD)->first();
                            if ($receiveRadFromCustomers) {
                                $totalRadPaidAmount += $act->rad_amount;
                            }

                            $receiveOpsteFromCustomers = $receivePaymentsFromCustomers->where('amount', $act->opste_amount)->where('category', \App\Models\Payment::CATEGORY_OPSTE)->first();
                            if ($receiveOpsteFromCustomers) {
                                $totalOpstePaidAmount += $act->opste_amount;
                            }
                        }

                        $totalMaterialLeftPaidAmount = $totalMaterialContractAmount - $totalMaterialPaidAmount;
                        $totalRadLeftPaidAmount = $totalRadContractAmount - $totalRadPaidAmount;
                        $totalOpsteLeftPaidAmount = $totalOpsteContractAmount - $totalOpstePaidAmount;

                        $totalAmount = $totalMaterialAmount + $totalRadAmount + $totalOpsteAmount;
                        $totalPaidAmount = $totalMaterialPaidAmount + $totalRadPaidAmount + $totalOpstePaidAmount;
                        $totalLeftPaidAmount = $totalMaterialLeftPaidAmount + $totalRadLeftPaidAmount + $totalOpsteLeftPaidAmount;
                        $totalContractAmount = $totalMaterialContractAmount + $totalRadContractAmount + $totalOpsteContractAmount;
                    @endphp

                    <tbody class="text-gray-600 fw-bold fs-7">
                        <tr>
                            <td class="ps-2 fw-bolder">Материал</td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalMaterialContractAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format($totalContractAmount != 0 ? $totalMaterialContractAmount / $totalContractAmount * 100 : 0) . '%' }}
                            </td>

                            @foreach($acts as $act)
                                <td class="cell-center fw-bolder">
                                    {{ \App\Models\CurrencyExchangeRate::format($act->amount, 'RUB', 0, true) }}
                                </td>
                            @endforeach

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
                                {{ number_format($totalPaidAmount != 0 ? $totalMaterialPaidAmount / $totalPaidAmount * 100 : 0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalMaterialLeftPaidAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format($totalLeftPaidAmount != 0 ? $totalMaterialLeftPaidAmount / $totalLeftPaidAmount * 100 : 0) . '%' }}
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

                            @foreach($acts as $act)
                                <td class="cell-center fw-bolder">
                                    {{ \App\Models\CurrencyExchangeRate::format($act->rad_amount, 'RUB', 0, true) }}
                                </td>
                            @endforeach

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
                                {{ number_format($totalPaidAmount != 0 ? $totalRadPaidAmount / $totalPaidAmount * 100 : 0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalRadLeftPaidAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format($totalLeftPaidAmount != 0 ? $totalRadLeftPaidAmount / $totalLeftPaidAmount * 100 : 0) . '%' }}
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

                            @foreach($acts as $act)
                                <td class="cell-center fw-bolder">
                                    {{ \App\Models\CurrencyExchangeRate::format($act->opste_amount, 'RUB', 0, true) }}
                                </td>
                            @endforeach

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
                                {{ number_format($totalPaidAmount != 0 ? $totalOpstePaidAmount / $totalPaidAmount * 100 : 0) . '%' }}
                            </td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalOpsteLeftPaidAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">
                                {{ number_format($totalLeftPaidAmount != 0 ? $totalOpsteLeftPaidAmount / $totalLeftPaidAmount * 100 : 0) . '%' }}
                            </td>
                        </tr>

                        <tr class="object-row fw-bolder">
                            <td class="ps-2 fw-bolder">Итого</td>
                            <td class="cell-center">
                                {{ \App\Models\CurrencyExchangeRate::format($totalContractAmount, 'RUB', 0, true) }}
                            </td>
                            <td class="cell-center">100%</td>

                            @foreach($acts as $act)
                                <td class="cell-center fw-bolder"></td>
                            @endforeach

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
                        </tr>
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
