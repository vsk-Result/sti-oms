@extends('layouts.app')

@section('title', 'Распределение услуг по трансферу')
@section('toolbar-title', 'Распределение услуг по трансферу')
@section('breadcrumbs', Breadcrumbs::render('distribution_transfer_service.index'))

@section('content')
    @php
        $info = Illuminate\Support\Facades\Cache::get('distribution_transfer_service', function() use ($objects) {
            $transferInfo = [];
            $transferTotalAmount = 0;

            $years = [];
            for ($i = 2021; $i <= \Carbon\Carbon::now()->year; $i++) {
                $years[] = $i;
            }
            $years = array_reverse($years, true);

            foreach ($years as $year) {
                $datesBetween = [$year . '-01-01', $year . '-12-31'];
                $cashAmount = \App\Models\Payment::query()
                                        ->whereBetween('date', $datesBetween)
                                        ->where('payment_type_id', \App\Models\Payment::PAYMENT_TYPE_CASH)
                                        ->where('amount', '<=', 0)
                                        ->whereIn('company_id', [1, 5])
                                        ->whereIn('object_id', $objects->pluck('id'))
                                        ->sum('amount');
                $transferAmount = \App\Models\Payment::query()
                                        ->whereBetween('date', $datesBetween)
                                        ->whereIn('company_id', [1, 5])
                                        ->whereIn('code', ['7.11.1'])
                                        ->where('type_id', \App\Models\Payment::TYPE_GENERAL)
                                        ->sum('amount');

                $transferInfo[$year] = [
                    'cash_amount' => $cashAmount,
                    'transfer_amount' => $transferAmount,
                    'info' => \App\Services\ObjectService::getDistributionTransferServiceByPeriod($datesBetween),
                ];

                $transferTotalAmount += $transferAmount;
            }

            return [
                'transferInfo' => $transferInfo,
                'transferTotalAmount' => $transferTotalAmount,
            ];
        });
    @endphp
    <div class="card mb-5 mb-xl-8 p-0 border-0">
        <div class="card-header border-0 pt-6 pe-0">
            <div class="card-title"></div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <form action="{{ route('distribution_transfer_service.exports.store') }}" method="POST" class="hidden">
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

        <div class="card-body p-0">
            <div class="table-responsive freeze-table">
                <table class="table table-hover align-middle table-row-dashed fs-7">
                    <thead class="text-gray-600">
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-250px bt bl ps-2">Разбивка услуг по трансферу по годам</th>

                            <th class="min-w-125px bt bl hl">Итого</th>
                            <th class="bt bl hl percent"></th>
                            <th class="min-w-125px text-danger bt br hl text-right">{{ \App\Models\CurrencyExchangeRate::format($info['transferTotalAmount'], 'RUB') }}</th>

                            @foreach($info['transferInfo'] as $year => $infoArray)
                                <th class="min-w-125px bt grouped toggle-grouped-by-year">
                                    {{ $year }}
                                    <br>
                                    <span class="text-danger fs-8">{{ \App\Models\CurrencyExchangeRate::format($infoArray['cash_amount'], 'RUB') }}</span>
                                </th>
                                <th class="min-w-125px text-danger bt br text-right grouped">{{ \App\Models\CurrencyExchangeRate::format($infoArray['transfer_amount'], 'RUB') }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="min-w-250px bl ps-2">Объект</th>

                            <th class="min-w-125px bl hl text-center">Наличные расходы</th>
                            <th class="bl hl text-center percent" >%</th>
                            <th class="min-w-125px br hl text-center">Услуги по трансферу</th>

                            @foreach($info['transferInfo'] as $year => $infoArray)
                                <th class="min-w-125px grouped">Наличные расходы</th>
                                <th class="min-w-125px br grouped">Услуги по трансферу</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @php
                            $averagePercents = [];
                            foreach($objects as $object) {
                                $percentSum = 0;
                                $percentCount = 0;

                                foreach($info['transferInfo'] as $infoArray) {
                                    if (isset($infoArray['info'][$object->id])) {
                                        $percent = ($infoArray['info'][$object->id]['cash_amount'] < 0 ? abs($infoArray['info'][$object->id]['transfer_amount'] / $infoArray['info'][$object->id]['cash_amount']) : 0) * 100;
                                        $percentSum += $percent;
                                        $percentCount++;
                                    }
                                }

                                $averagePercents[$object->id] = $percentCount > 0 ? $percentSum / $percentCount : 0;
                            }
                        @endphp

                        @php
                            $activeObjects = [];
                            $closedObjects = [];

                            foreach ($objects as $object) {
                                if ($object->isBlocked()) {
                                    $closedObjects[] = $object;
                                } else {
                                    $activeObjects[] = $object;
                                }
                            }
                        @endphp

                        @foreach($activeObjects as $object)
                            <tr>
                                <td class="bl ps-2"><a href="{{ route('objects.show', $object) }}" class="text-gray-800 text-hover-primary fs-7 me-3">{{ $object->getName() }}</a></td>

                                @php
                                    $totalCash = 0;
                                    $totalTransfer = 0;

                                    foreach($info['transferInfo'] as $year => $infoArray) {
                                        $totalCash += ($infoArray['info'][$object->id]['cash_amount'] ?? 0);
                                        $totalTransfer += ($infoArray['info'][$object->id]['transfer_amount'] ?? 0);
                                    }

                                    \App\Services\ObjectGeneralCostService::updateDistributionTransferService($object, $totalTransfer);
                                @endphp

                                <td class="text-danger bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalCash, 'RUB', 0, true) }}</td>
                                <td class="bl hl text-center percent" >{{ number_format(($totalCash < 0 ? abs($totalTransfer / $totalCash) : 0) * 100, 2) }}%</td>
                                <td class="text-danger bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalTransfer, 'RUB', 0, true) }}</td>

                                @foreach($info['transferInfo'] as $year => $infoArray)
                                    @if (isset($infoArray['info'][$object->id]))
                                        <td class="text-danger bl text-right grouped">{{ \App\Models\CurrencyExchangeRate::format($infoArray['info'][$object->id]['cash_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right grouped">{{ \App\Models\CurrencyExchangeRate::format($infoArray['info'][$object->id]['transfer_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl grouped">-</td>
                                        <td class="br grouped">-</td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach

                        @php
                            $closedInfo = [];
                            foreach ($closedObjects as $object) {
                                foreach($info['transferInfo'] as $year => $infoArray) {
                                    if (!isset($closedInfo[$year]['cash_amount'])) {
                                        $closedInfo[$year]['cash_amount'] = 0;
                                    }
                                    if (!isset($closedInfo[$year]['transfer_amount'])) {
                                        $closedInfo[$year]['transfer_amount'] = 0;
                                    }
                                    if (!isset($closedInfo['total']['cash_amount'])) {
                                        $closedInfo['total']['cash_amount'] = 0;
                                    }
                                    if (!isset($closedInfo['total']['transfer_amount'])) {
                                        $closedInfo['total']['transfer_amount'] = 0;
                                    }

                                    $closedInfo['total']['cash_amount'] += $infoArray['info'][$object->id]['cash_amount'] ?? 0;
                                    $closedInfo['total']['transfer_amount'] += $infoArray['info'][$object->id]['transfer_amount'] ?? 0;

                                    $closedInfo[$year]['cash_amount'] += $infoArray['info'][$object->id]['cash_amount'] ?? 0;
                                    $closedInfo[$year]['transfer_amount'] += $infoArray['info'][$object->id]['transfer_amount'] ?? 0;
                                }
                            }
                        @endphp

                        <tr class="toggle-closed-object">
                            <td class="bl ps-2">Закрытые объекты</td>
                            <td class="text-danger bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($closedInfo['total']['cash_amount'], 'RUB', 0, true) }}</td>
                            <td class="bl hl text-center percent" >{{ number_format(($closedInfo['total']['cash_amount'] < 0 ? abs($closedInfo['total']['transfer_amount'] / $closedInfo['total']['cash_amount']) : 0) * 100, 2) }}%</td>
                            <td class="text-danger bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($closedInfo['total']['transfer_amount'], 'RUB', 0, true) }}</td>

                            @foreach($info['transferInfo'] as $year => $infoArray)
                                <td class="text-danger bl text-right grouped">{{ \App\Models\CurrencyExchangeRate::format($closedInfo[$year]['cash_amount'], 'RUB', 0, true) }}</td>
                                <td class="text-danger br text-right grouped">{{ \App\Models\CurrencyExchangeRate::format($closedInfo[$year]['transfer_amount'], 'RUB', 0, true) }}</td>
                            @endforeach
                        </tr>

                        @foreach($closedObjects as $object)
                            <tr class="closed-object" style="display: none;">
                                <td class="bl ps-2">{{ $object->getName() }}</td>

                                @php
                                    $totalCash = 0;
                                    $totalTransfer = 0;

                                    foreach($info['transferInfo'] as $year => $infoArray) {
                                        $totalCash += ($infoArray['info'][$object->id]['cash_amount'] ?? 0);
                                        $totalTransfer += ($infoArray['info'][$object->id]['transfer_amount'] ?? 0);
                                    }

                                    \App\Services\ObjectGeneralCostService::updateDistributionTransferService($object, $totalTransfer);
                                @endphp

                                <td class="text-danger bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalCash, 'RUB', 0, true) }}</td>
                                <td class="bl hl text-center percent" >{{ number_format(($totalCash < 0 ? abs($totalTransfer / $totalCash) : 0) * 100, 2) }}%</td>
                                <td class="text-danger bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalTransfer, 'RUB', 0, true) }}</td>

                                @foreach($info['transferInfo'] as $year => $infoArray)
                                    @if (isset($infoArray['info'][$object->id]))
                                        <td class="text-danger bl text-right grouped">{{ \App\Models\CurrencyExchangeRate::format($infoArray['info'][$object->id]['cash_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right grouped">{{ \App\Models\CurrencyExchangeRate::format($infoArray['info'][$object->id]['transfer_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl grouped">-</td>
                                        <td class="br grouped">-</td>
                                    @endif
                                @endforeach
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

        $('.toggle-closed-object').on('click', function () {
            $('.closed-object').toggle();
        });

        $('.toggle-grouped-by-year').on('click', function () {
            const year = $(this).data('year');
            $(`.grouped-by-year[data-year=${year}]`).toggle();
        });
    </script>
@endpush

@push('styles')
    <style>
        .bl {
            border-left: 1px solid #eee !important;
        }

        .bt {
            border-top: 1px solid #eee !important;
        }

        .br {
            border-right: 1px solid #eee !important;
        }

        .hl, .table tbody tr:last-child td.hl {
            background-color: #f7f7f7 !important;
            font-weight: bold !important;
            border: 1px dashed #ccc !important;
        }

        .hl:not(.percent), .table tbody tr:last-child td.hl:not(.percent) {
            min-width: 150px !important;
        }

        .text-right {
            text-align: right !important;
        }

        .percent {
            min-width: 100px !important;
            width: 100px !important;
            text-align: center !important;
        }

        .toggle-closed-object {
            cursor: pointer !important;
            --bs-table-accent-bg: #f7f7f7 !important;
            font-weight: bold !important;
            border: 1px dashed #ccc !important;
        }

        .toggle-closed-object > td {
            border: 1px dashed #ccc !important;
        }

        .toggle-closed-object > td:first-child {
            color: red !important;
        }

        th.grouped, td.grouped {
            /*background-color: #f7f7f799;*/
        }

        .toggle-grouped-by-year {
            font-weight: bold !important;
        }
    </style>
@endpush
