@extends('objects.layouts.show')

@section('object-tab-title', 'Долги')

@section('object-tab-content')
    @include('objects.modals.debt_upload_manual')
    @include('objects.modals.debt_upload_replace_manual')
    @include('objects.modals.debt_import_details')

    <div class="card border-0">
        <div class="card-header border-0 p-0">
            <div>
                @if ($hasExpiredManualUpload)
                    <div class="alert alert-dismissible bg-light-warning d-flex flex-column flex-sm-row p-5">
                        <div class="d-flex flex-column pe-0 pe-sm-10">
                            <span>Обратите внимание, вы обновляли долги вручную более 2 дней назад</span>
                        </div>
                    </div>
                @endif
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end align-items-center">
                    <a
                            class="btn btn-light-primary me-3"
                            href="javascript:void(0);"
                            data-bs-toggle="modal"
                            data-bs-target="#debtsManualReplaceModal"
                    >
                        Обновить вручную
                    </a>

                    <form action="{{ route('objects.debts.exports.store', $object) }}" method="POST" class="hidden me-3">
                        @csrf
                        <a
                                href="javascript:void(0);"
                                class="btn btn-light-success"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                        >
                            Экспорт в Excel
                        </a>
                    </form>

                    @if(! auth()->user()->hasRole('finance-object-user-mini'))
                        <a
                                class="btn btn-light-info"
                                href="javascript:void(0);"
                                data-bs-toggle="modal"
                                data-bs-target="#debtsImportDetailsModal"
                        >
                            Источники
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6 g-xl-9">
        <div class="col-lg-12">
            <div class="card card-flush h-lg-100">
                <div class="card-header mt-6 align-items-baseline">
                    <div class="card-title">
                        <div class="d-flex flex-column">
                            <h3 class="fw-bolder mb-1">Долг подрядчикам</h3>
                            @if(! auth()->user()->hasRole('finance-object-user-mini'))
                                <a class="btn btn-sm btn-light-primary mt-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#debtUploadManualModal">
                                    Обновить
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="card-toolbar">
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bolder text-danger">
                                    {{ \App\Models\CurrencyExchangeRate::format($contractorDebts['total']['total_amount']) }}
                                </div>
                            </div>
                            <div class="fw-bold fs-6 text-gray-400">Итого долг</div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-9 pt-0">
                    @php
                        $periodPivotData = [
                            'dates' => [],
                            'data' => [],
                        ];

                        foreach ($contractorDebts['organizations'] as $organizationInfo) {
                            foreach ($organizationInfo['details'] as $detail) {
                                $date = \Carbon\Carbon::parse($detail['date'])->format('F Y');

                                if (! isset($periodPivotData['data'][$organizationInfo['organization_name']][$date])) {
                                    $periodPivotData['data'][$organizationInfo['organization_name']][$date] = 0;
                                }

                                $periodPivotData['data'][$organizationInfo['organization_name']][$date] += $detail['amount'];

                                if (! in_array($date, $periodPivotData['dates'])) {
                                    $periodPivotData['dates'][] = $date;
                                }
                            }
                        }

                        $sortedDates = $periodPivotData['dates'];
                        asort($sortedDates);

                        $periodPivotData['dates'] = $sortedDates;
                    @endphp

                    <table class="table table-hover align-middle table-row-dashed fs-6">
                        <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="ps-2">Контрагент</th>
                                <th class="w-150px text-end">Неотр. аванс</th>
                                <th class="w-150px text-end">ГУ</th>
                                <th class="w-150px text-end">в т.ч. ГУ срок наступил</th>
                                <th class="w-150px text-end pe-2">Авансы к оплате</th>
                                <th class="w-175px text-end">Долг за СМР</th>

                                @if (count($periodPivotData['dates']) > 0)
                                    <th class="w-40px ps-3 fs-1 fw-bold collapse-trigger cursor-pointer cell-center" data-trigger="contractors-periods">+</th>
                                @endif

                                @foreach($periodPivotData['dates'] as $date)
                                    <th class="w-150px text-end collapse-col period {{ $loop->last ? 'pe-2' : '' }}" data-trigger="contractors-periods" style="display: none;">{{ translate_year_month_word($date) }}</th>
                                @endforeach
                            </tr>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="ps-2 hl">ИТОГО</th>
                                <th class="w-150px text-end hl text-success">
                                    {{ \App\Models\CurrencyExchangeRate::format($contractorDebts['total']['unwork_avans']) }}
                                </th>
                                <th class="w-150px text-end hl text-danger">
                                    {{ \App\Models\CurrencyExchangeRate::format($contractorDebts['total']['guarantee']) }}
                                </th>
                                <th class="w-150px text-end hl text-danger">
                                    {{ \App\Models\CurrencyExchangeRate::format($contractorDebts['total']['guarantee_deadline']) }}
                                </th>
                                <th class="w-150px text-end pe-2 hl text-danger">
                                    {{ \App\Models\CurrencyExchangeRate::format($contractorDebts['total']['avans']) }}
                                </th>
                                <th class="w-175px text-end hl text-danger">
                                    {{ \App\Models\CurrencyExchangeRate::format($contractorDebts['total']['amount']) }}
                                </th>
                                <th></th>

                                @foreach($periodPivotData['dates'] as $date)
                                    <th class="collapse-col period {{ $loop->last ? 'pe-2' : '' }}" data-trigger="contractors-periods" style="display: none;"></th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            @forelse($contractorDebts['organizations'] as $organizationInfo)
                                <tr>
                                    <td class="ps-2">
                                        @include('partials.check_organization', ['organizationName' => $organizationInfo['organization_name']])
                                    </td>
                                    <td class="text-success text-end pe-2">
                                        {{ \App\Models\CurrencyExchangeRate::format($organizationInfo['unwork_avans'], 'RUB', 0, true) }}
                                    </td>
                                    <td class="text-danger text-end pe-2">
                                        {{ \App\Models\CurrencyExchangeRate::format($organizationInfo['guarantee'], 'RUB', 0, true) }}
                                    </td>
                                    <td class="text-danger text-end pe-2">
                                        {{ \App\Models\CurrencyExchangeRate::format($organizationInfo['guarantee_deadline'], 'RUB', 0, true) }}
                                    </td>
                                    <td class="text-danger text-end pe-2">
                                        {{ \App\Models\CurrencyExchangeRate::format($organizationInfo['avans'], 'RUB', 0, true) }}
                                    </td>

                                    @php
                                        $periodSum = array_sum($periodPivotData['data'][$organizationInfo['organization_name']] ?? []);
                                    @endphp
                                    <td class="text-danger text-end pe-2 {{ $periodSum !== 0 && $periodSum != $organizationInfo['amount'] ? 'period-warning' : '' }}">
                                        {{ \App\Models\CurrencyExchangeRate::format($organizationInfo['amount'], 'RUB', 0, true) }}
                                    </td>

                                    <td></td>

                                    @foreach($periodPivotData['dates'] as $date)
                                        <td
                                                class="text-danger text-end pe-2 collapse-col period {{ $loop->last ? 'pe-2' : '' }}"
                                                data-trigger="contractors-periods"
                                                style="display: none;"
                                        >
                                            {{ \App\Models\CurrencyExchangeRate::format($periodPivotData['data'][$organizationInfo['organization_name']][$date] ?? 0, 'RUB', 0, true) }}
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <p class="text-center text-dark fw-bolder d-block my-4 fs-6">
                                            Долги отсутствуют
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if(! auth()->user()->hasRole('finance-object-user-mini'))

        <div class="col-lg-12">
            <div class="card card-flush h-lg-100">
                <div class="card-header mt-6">
                    <div class="card-title flex-column">
                        <h3 class="fw-bolder mb-1">Долг поставщикам</h3>
                    </div>

                    <div class="card-toolbar">
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bolder text-danger">
                                    {{ \App\Models\CurrencyExchangeRate::format($providerDebts['total']['amount']) }}
                                </div>
                            </div>
                            <div class="fw-bold fs-6 text-gray-400">Итого долг</div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-9 pt-0">
                    <table class="table table-hover align-middle table-row-dashed fs-6">
                        <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="ps-2">Контрагент</th>
                            <th class="w-175px pe-2 text-end">Сумма (фикс)</th>
                            <th class="w-175px pe-2 text-end">Сумма (изм)</th>
                        </tr>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="ps-2 hl">ИТОГО</th>
                            <th class="w-175px pe-2 text-end hl text-danger">
                                {{ \App\Models\CurrencyExchangeRate::format($providerDebts['total']['amount_fix']) }}
                            </th>
                            <th class="w-175px pe-2 text-end hl text-danger">
                                {{ \App\Models\CurrencyExchangeRate::format($providerDebts['total']['amount_float']) }}
                            </th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                        @forelse($providerDebts['organizations'] as $organizationInfo)
                            <tr class="row-edit-debt-manual">
                                <td class="ps-2">
                                    @include('partials.check_organization', ['organizationName' => $organizationInfo['organization_name']])
                                </td>
                                <td class="text-danger text-end">
                                    {{ \App\Models\CurrencyExchangeRate::format($organizationInfo['amount_fix']) }}
                                </td>
                                <td class="text-danger text-end">
                                    {{ \App\Models\CurrencyExchangeRate::format($organizationInfo['amount_float']) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <p class="text-center text-dark fw-bolder d-block my-4 fs-6">
                                        Долги отсутствуют
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card card-flush h-lg-100">
                <div class="card-header mt-6 align-items-baseline">
                    <div class="card-title">
                        <div class="d-flex flex-column">
                            <h3 class="fw-bolder mb-1">Долг за услуги</h3>
                        </div>
                    </div>

                    <div class="card-toolbar">
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bolder text-danger">
                                    {{ \App\Models\CurrencyExchangeRate::format($serviceDebts['total']['amount']) }}
                                </div>
                            </div>
                            <div class="fw-bold fs-6 text-gray-400">Итого долг</div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-9 pt-0">
                    @php
                        $periodPivotData = [
                            'dates' => [],
                            'data' => [],
                        ];

                        foreach ($serviceDebts['organizations'] as $organizationInfo) {
                            foreach ($organizationInfo['details'] as $detail) {
                                $date = \Carbon\Carbon::parse($detail['date'])->format('F Y');

                                if (! isset($periodPivotData['data'][$organizationInfo['organization_name']][$date])) {
                                    $periodPivotData['data'][$organizationInfo['organization_name']][$date] = 0;
                                }

                                $periodPivotData['data'][$organizationInfo['organization_name']][$date] += $detail['amount'];

                                if (! in_array($date, $periodPivotData['dates'])) {
                                    $periodPivotData['dates'][] = $date;
                                }
                            }
                        }
                    @endphp

                    <table class="table table-hover align-middle table-row-dashed fs-6">
                        <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="ps-2">Контрагент</th>
                                <th class="w-175px text-end pe-2">Сумма</th>

                                @if (count($periodPivotData['dates']) > 0)
                                    <th class="w-40px ps-3 fs-1 fw-bold collapse-trigger cursor-pointer cell-center" data-trigger="service-periods">+</th>
                                @endif

                                @foreach($periodPivotData['dates'] as $date)
                                    <th class="w-150px text-end collapse-col period {{ $loop->last ? 'pe-2' : '' }}" data-trigger="service-periods" style="display: none;">{{ translate_year_month_word($date) }}</th>
                                @endforeach
                            </tr>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="ps-2 hl">ИТОГО</th>
                                <th class="w-175px text-end pe-2 hl text-danger">
                                    {{ \App\Models\CurrencyExchangeRate::format($serviceDebts['total']['amount']) }}
                                </th>

                                <th></th>

                                @foreach($periodPivotData['dates'] as $date)
                                    <th class="collapse-col period {{ $loop->last ? 'pe-2' : '' }}" data-trigger="service-periods" style="display: none;"></th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            @forelse($serviceDebts['organizations'] as $organizationInfo)
                                <tr>
                                    <td class="ps-2">
                                        @include('partials.check_organization', ['organizationName' => $organizationInfo['organization_name']])
                                    </td>

                                    @php
                                        $periodSum = array_sum($periodPivotData['data'][$organizationInfo['organization_name']] ?? []);
                                    @endphp
                                    <td class="text-danger text-end pe-2 {{ $periodSum !== 0 && $periodSum != $organizationInfo['amount'] ? 'period-warning' : '' }}">
                                        {{ \App\Models\CurrencyExchangeRate::format($organizationInfo['amount']) }}
                                    </td>

                                    <td></td>

                                    @foreach($periodPivotData['dates'] as $date)
                                        <td
                                                class="text-danger text-end pe-2 collapse-col period {{ $loop->last ? 'pe-2' : '' }}"
                                                data-trigger="service-periods"
                                                style="display: none;"
                                        >
                                            {{ \App\Models\CurrencyExchangeRate::format($periodPivotData['data'][$organizationInfo['organization_name']][$date] ?? 0, 'RUB', 0, true) }}
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2">
                                        <p class="text-center text-dark fw-bolder d-block my-4 fs-6">
                                            Долги отсутствуют
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('styles')
    <style>
        th.hl {
            background-color: #f7f7f7 !important;
            font-weight: bold !important;
            height: 50px;
            vertical-align: middle;
            font-size: 13px;
        }

        th.period, td.period {
            background-color: #f9f9f9 !important;
        }

        td.period-warning {
            background-color: #ffcece !important;
            font-weight: bolder;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $('.collapse-trigger').on('click', function() {
            const $tr = $(this);
            const trigger = $tr.data('trigger');
            const isCollapsed = $tr.hasClass('collapsed');

            if (isCollapsed) {
                $tr.text('+');
                $tr.removeClass('collapsed');
                $(`.collapse-col[data-trigger="${trigger}"]`).hide();
            } else {
                $tr.text('>');
                $tr.addClass('collapsed');
                $(`.collapse-col[data-trigger="${trigger}"]`).show();
            }
        })
    </script>
@endpush
