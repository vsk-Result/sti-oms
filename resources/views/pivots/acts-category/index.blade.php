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
                    <form action="{{ route('pivots.acts_category.exports.store') }}" method="POST" class="hidden">
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

                        foreach ($activeObjects as $aobject) {
                            $receivePayments = $aobject->payments()
                                ->where('payment_type_id', App\Models\Payment::PAYMENT_TYPE_NON_CASH)
                                ->where('company_id', 1)
                                ->whereIn('organization_sender_id', $aobject->customers->pluck('id')->toArray())
                                ->get();

                            $totalMaterialPaidAmount += $receivePayments->where('category', \App\Models\Payment::CATEGORY_MATERIAL)->sum('amount');
                            $totalRadPaidAmount += $receivePayments->where('category', \App\Models\Payment::CATEGORY_RAD)->sum('amount');
                            $totalOpstePaidAmount += $receivePayments->where('category', \App\Models\Payment::CATEGORY_OPSTE)->sum('amount');
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
                        </tr>

                        @foreach($activeObjects as $object)
                            @php
                                $acts = $object->acts;
                                $totalMaterialAmount = $acts->sum('amount');
                                $totalRadAmount = $acts->sum('rad_amount');
                                $totalOpsteAmount = $acts->sum('opste_amount');

                                $totalMaterialContractAmount = 0;
                                $totalRadContractAmount = 0;
                                $totalOpsteContractAmount = 0;

                                foreach ($object->contracts()->where('type_id', App\Models\Contract\Contract::TYPE_MAIN)->get() as $contract) {
                                    $totalMaterialContractAmount += $contract->getMaterialAmount();
                                    $totalRadContractAmount += $contract->getRadAmount();
                                    $totalOpsteContractAmount += $contract->getOpsteAmount();
                                }

                                $receivePayments = $object->payments()
                                    ->where('payment_type_id', App\Models\Payment::PAYMENT_TYPE_NON_CASH)
                                    ->where('company_id', 1)
                                    ->whereIn('organization_sender_id', $object->customers->pluck('id')->toArray())
                                    ->get();

                                $totalMaterialPaidAmount = $receivePayments->where('category', \App\Models\Payment::CATEGORY_MATERIAL)->sum('amount');
                                $totalRadPaidAmount = $receivePayments->where('category', \App\Models\Payment::CATEGORY_RAD)->sum('amount');
                                $totalOpstePaidAmount = $receivePayments->where('category', \App\Models\Payment::CATEGORY_OPSTE)->sum('amount');

                                $totalMaterialLeftPaidAmount = $totalMaterialContractAmount - $totalMaterialPaidAmount;
                                $totalRadLeftPaidAmount = $totalRadContractAmount - $totalRadPaidAmount;
                                $totalOpsteLeftPaidAmount = $totalOpsteContractAmount - $totalOpstePaidAmount;

                                $totalAmount = $totalMaterialAmount + $totalRadAmount + $totalOpsteAmount;
                                $totalPaidAmount = $totalMaterialPaidAmount + $totalRadPaidAmount + $totalOpstePaidAmount;
                                $totalLeftPaidAmount = $totalMaterialLeftPaidAmount + $totalRadLeftPaidAmount + $totalOpsteLeftPaidAmount;
                                $totalContractAmount = $totalMaterialContractAmount + $totalRadContractAmount + $totalOpsteContractAmount;

                                $totalMaterialPaidAmountPercent = $totalMaterialContractAmount != 0 ? $totalMaterialPaidAmount / $totalMaterialContractAmount * 100 : 0;
                                $totalRadPaidAmountPercent = $totalRadContractAmount != 0 ? $totalRadPaidAmount / $totalRadContractAmount * 100 : 0;
                                $totalOpstePaidAmountPercent = $totalOpsteContractAmount != 0 ? $totalOpstePaidAmount / $totalOpsteContractAmount * 100 : 0;

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
                                <td class="cell-center">
                                    {{ number_format($totalMaterialContractAmount != 0 ? $totalMaterialAmount / $totalMaterialContractAmount * 100 : 0) . '%' }}
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
                                <td class="cell-center">
                                    {{ number_format($totalRadContractAmount != 0 ? $totalRadAmount / $totalRadContractAmount * 100 : 0) . '%' }}
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
                                <td class="cell-center">
                                    {{ number_format($totalOpsteContractAmount != 0 ? $totalOpsteAmount / $totalOpsteContractAmount * 100 : 0) . '%' }}
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

        $('.collapse-trigger').on('click', function() {
            const trigger = $(this).data('trigger');
            const isCollapsed = $(this).hasClass('collapsed');

            if (isCollapsed) {
                $(this).find('span').text('+');
                $(this).removeClass('collapsed');
                $(`.collapse-row[data-trigger="${trigger}"]`).hide();
            } else {
                $(this).find('span').text('-');
                $(this).addClass('collapsed');
                $(`.collapse-row[data-trigger="${trigger}"]`).show();
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
