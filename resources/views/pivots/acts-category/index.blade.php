@extends('layouts.app')

@section('toolbar-title', 'Отчет по категориям')
@section('breadcrumbs', Breadcrumbs::render('pivots.acts_category.index'))

@section('content')
    <div class="card mb-5 mb-xl-8 border-0">
        <div class="card-body p-0 ps-0">
            <div class="table-responsive freeze-table">
                <table class="table table-bordered align-middle table-row-dashed fs-6">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 gs-0 cell-center">
                            <th class="min-w-200px ps-2">Категория</th>
                            <th class="min-w-200px">Сумма по договору</th>
                            <th class="min-w-200px">% по договору</th>

                            @foreach($acts as $act)
                                <th class="min-w-200px">{{ $act->name }}</th>
                            @endforeach

                            <th class="min-w-200px">Итого выполнение</th>
                            <th class="min-w-200px">% выполнение</th>
                            <th class="min-w-200px">Остато к выполнению</th>
                            <th class="min-w-200px">Оплата</th>
                            <th class="min-w-200px">% оплаты</th>
                            <th class="min-w-200px">Остаток к оплате</th>
                            <th class="min-w-200px">% остатка к оплатее</th>
                        </tr>
                    </thead>

                    <tbody class="text-gray-600 fw-bold fs-7">
                        <tr class="object-row">
                            <td class="ps-2 fw-bolder">Материал</td>
                            <td class="text-right">
                                {{ \App\Models\CurrencyExchangeRate::format(\App\Models\Contract\Contract::where('type_id', \App\Models\Contract\Contract::TYPE_MAIN), 'RUB', 0, true) }}
                            </td>

                            @foreach($acts as $act)
                                @php
                                    $amount = $plans->where('object_id', $object->id)->where('date', $period['start'])->sum('amount');
                                @endphp
                                <td class="text-right fw-bolder">
                                    {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                                </td>
                            @endforeach

                            <td class="text-right fw-bolder pe-2">
                                {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                            </td>
                        </tr>

                        @foreach($acts as $act)

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
    </script>
@endpush

@push('styles')
    <style>
        .table td, .table th, .table tbody tr:last-child td {
            border: 1px solid #c8c8c8 !important;
            color: #3f4254;
        }

        .text-right {
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
