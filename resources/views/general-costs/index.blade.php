@extends('layouts.app')

@section('toolbar-title', 'Распределение общих затрат')
@section('breadcrumbs', Breadcrumbs::render('general_costs.index'))

@section('content')
    @php
        $object27_1 = \App\Models\Object\BObject::where('code', '27.1')->first();
        $object27_8 = \App\Models\Object\BObject::where('code', '27.8')->first();

        $periods = [
            [
                'start_date' => '2017-01-01',
                'end_date' => '2017-12-31',
                'bonus' => 0,
            ],
            [
                'start_date' => '2018-01-01',
                'end_date' => '2018-12-31',
                'bonus' => 21421114,
            ],
            [
                'start_date' => '2019-01-01',
                'end_date' => '2019-12-31',
                'bonus' => (39760000 + 692048),
            ],
            [
                'start_date' => '2020-01-01',
                'end_date' => '2020-12-31',
                'bonus' => (2000000 + 418000 + 1615000),
            ],
            [
                'start_date' => '2021-01-01',
                'end_date' => '2021-03-02',
                'bonus' => 600000,
            ],
            [
                'start_date' => '2021-03-03',
                'end_date' => '2021-12-31',
                'bonus' => (600000 + 68689966),
            ],
            [
                'start_date' => '2022-01-01',
                'end_date' => '2022-10-11',
                'bonus' => 0,
            ],
            [
                'start_date' => '2022-10-12',
                'end_date' => '2022-12-31',
                'bonus' => 0,
            ],
            [
                'start_date' => '2023-01-01',
                'end_date' => '2023-07-20',
                'bonus' => 0,
            ],
            [
                'start_date' => '2023-07-21',
                'end_date' => '2023-12-31',
                'bonus' => 0,
            ]
        ];

        $periods = array_reverse($periods);

        $generalTotalAmount = 0;
        $generalInfo = [];
        foreach ($periods as $index => $period) {
            $datesBetween = [$period['start_date'], $period['end_date']];
            $paymentQuery = \App\Models\Payment::query()->whereBetween('date', $datesBetween)->where('company_id', 1);
            $generalAmount = (clone $paymentQuery)->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount')
                            + (clone $paymentQuery)->where('object_id', $object27_1->id)->sum('amount')
                            + ((clone $paymentQuery)->where('object_id', $object27_8->id)->sum('amount') * 0.7)
                            + $period['bonus'];

            $generalInfo[$index] = [
                'start_date' => $period['start_date'],
                'end_date' => $period['end_date'],
                'general_amount' => $generalAmount,
                'info' => \App\Services\ObjectService::getGeneralCostsByPeriod($period['start_date'], $period['end_date'], $period['bonus']),
            ];

            $generalTotalAmount += $generalAmount;
        }
    @endphp
    <div class="card mb-5 mb-xl-8 p-0 border-0">
        <div class="card-header border-0 pt-6 pe-0">
            <div class="card-title"></div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <form action="{{ route('general_costs.exports.store') }}" method="POST" class="hidden">
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
                            <th class="min-w-250px bt bl ps-2">Разбивка общих затрат по годам</th>

                            <th class="min-w-125px bt bl hl">Итого</th>
                            <th class="bt bl hl percent" ></th>
                            <th class="min-w-125px text-danger bt br hl text-right">{{ \App\Models\CurrencyExchangeRate::format($generalTotalAmount, 'RUB') }}</th>

                            @foreach($generalInfo as $info)
                                <th class="min-w-125px bt">с {{ $info['start_date'] }} по {{ $info['end_date'] }}</th>
                                <th class="bt percent" ></th>
                                <th class="min-w-125px text-danger bt br text-right">{{ \App\Models\CurrencyExchangeRate::format($info['general_amount'], 'RUB') }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="min-w-250px bl ps-2">Объект</th>

                            <th class="min-w-125px bl hl text-center">Получено</th>
                            <th class="bl hl text-center percent" >%</th>
                            <th class="min-w-125px br hl text-center">Общие расходы</th>

                            @foreach($generalInfo as $info)
                                <th class="min-w-125px">Получено</th>
                                <th class="text-center percent" >%</th>
                                <th class="min-w-125px br">Общие расходы на объект</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @php
                            $averagePercents = [];
                            foreach($objects as $object) {
                                $percentOneSum = 0;
                                $percentOneCount = 0;

                                $percentTwoSum = 0;
                                $percentTwoCount = 0;

                                $percentSum = 0;
                                $percentCount = 0;

                                foreach($generalInfo as $info) {
                                    if ($object->code == 288) {
                                        $percentOne = 0;
                                        $percentTwo = 0;

                                        if (isset($info['info'][$object->id.'|1'])) {
                                            $percentOne = ($info['info'][$object->id.'|1']['cuming_amount'] > 0 ? abs($info['info'][$object->id.'|1']['general_amount'] / $info['info'][$object->id.'|1']['cuming_amount']) : 0) * 100;
                                            $percentOneSum += $percent;
                                            $percentOneCount++;
                                        }

                                        if (isset($info['info'][$object->id.'|24'])) {
                                            $percentTwo = ($info['info'][$object->id.'|24']['cuming_amount'] > 0 ? abs($info['info'][$object->id.'|24']['general_amount'] / $info['info'][$object->id.'|24']['cuming_amount']) : 0) * 100;
                                            $percentTwoSum += $percent;
                                            $percentTwoCount++;
                                        }
                                    } else {
                                        $percent = 0;

                                        if (isset($info['info'][$object->id])) {
                                            $percent = ($info['info'][$object->id]['cuming_amount'] > 0 ? abs($info['info'][$object->id]['general_amount'] / $info['info'][$object->id]['cuming_amount']) : 0) * 100;
                                            $percentSum += $percent;
                                            $percentCount++;
                                        }
                                    }
                                }

                                if ($object->code == 288) {
                                    $averagePercents[$object->id.'|1'] = $percentOneCount > 0 ? $percentOneSum / $percentOneCount : 0;
                                    $averagePercents[$object->id.'|24'] = $percentTwoCount > 0 ? $percentTwoSum / $percentTwoCount : 0;
                                } else {
                                    $averagePercents[$object->id] = $percentCount > 0 ? $percentSum / $percentCount : 0;
                                }
                            }
                        @endphp

                        @foreach($objects as $object)
                            @if ($object->code == 288)
                                <tr>
                                    <td class="bl ps-2">{{ $object->getName() . ' | 1 (Строительство)' }}</td>

                                    @php
                                        $totalCuming = 0;
                                        $totalGeneral = 0;

                                        foreach($generalInfo as $info) {
                                            $totalCuming += ($info['info'][$object->id.'|1']['cuming_amount'] ?? 0);
                                            $totalGeneral += ($info['info'][$object->id.'|1']['general_amount'] ?? 0);
                                        }

                                        \App\Services\ObjectGeneralCostService::updateGeneralCost($object, $totalGeneral);
                                    @endphp

                                    <td class="text-success bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalCuming, 'RUB', 0, true) }}</td>
                                    <td class="bl hl text-center percent" >{{ number_format($averagePercents[$object->id.'|1'], 2) }}%</td>
                                    <td class="text-danger bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalGeneral, 'RUB', 0, true) }}</td>

                                    @foreach($generalInfo as $info)
                                        @if (isset ($info['info'][$object->id.'|1']))
                                            <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info['info'][$object->id.'|1']['cuming_amount'], 'RUB', 0, true) }}</td>
                                            <td class="text-center percent" >{{ number_format(($info['info'][$object->id.'|1']['cuming_amount'] > 0 ? abs($info['info'][$object->id.'|1']['general_amount'] / $info['info'][$object->id.'|1']['cuming_amount']) : 0) * 100, 2) }}%</td>
                                            <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info['info'][$object->id.'|1']['general_amount'], 'RUB', 0, true) }}</td>
                                        @else
                                            <td class="bl">-</td>
                                            <td>-</td>
                                            <td class="br">-</td>
                                        @endif
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="bl ps-2">{{ $object->getName() . ' | 2+4 (Инженерия)' }}</td>

                                    @php
                                        $totalCuming = 0;
                                        $totalGeneral = 0;

                                        foreach($generalInfo as $info) {
                                            $totalCuming += ($info['info'][$object->id.'|24']['cuming_amount'] ?? 0);
                                            $totalGeneral += ($info['info'][$object->id.'|24']['general_amount'] ?? 0);
                                        }

                                        \App\Services\ObjectGeneralCostService::updateGeneralCost($object, $totalGeneral, true, false);
                                    @endphp

                                    <td class="text-success bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalCuming, 'RUB', 0, true) }}</td>
                                    <td class="bl hl text-center" >{{ number_format($averagePercents[$object->id.'|24'], 2) }}%</td>
                                    <td class="text-danger bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalGeneral, 'RUB', 0, true) }}</td>

                                    @foreach($generalInfo as $info)
                                        @if (isset ($info['info'][$object->id.'|24']))
                                            <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info['info'][$object->id.'|24']['cuming_amount'], 'RUB', 0, true) }}</td>
                                            <td class="text-center percent" >{{ number_format(($info['info'][$object->id.'|24']['cuming_amount'] > 0 ? abs($info['info'][$object->id.'|24']['general_amount'] / $info['info'][$object->id.'|24']['cuming_amount']) : 0) * 100, 2) }}%</td>
                                            <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info['info'][$object->id.'|24']['general_amount'], 'RUB', 0, true) }}</td>
                                        @else
                                            <td class="bl">-</td>
                                            <td>-</td>
                                            <td class="br">-</td>
                                        @endif
                                    @endforeach
                                </tr>
                            @else
                                <tr>
                                    <td class="bl ps-2">{{ $object->getName() }}</td>

                                    @php
                                            $totalCuming = 0;
                                            $totalGeneral = 0;

                                            foreach($generalInfo as $info) {
                                                $totalCuming += ($info['info'][$object->id]['cuming_amount'] ?? 0);
                                                $totalGeneral += ($info['info'][$object->id]['general_amount'] ?? 0);
                                            }

                                            \App\Services\ObjectGeneralCostService::updateGeneralCost($object, $totalGeneral);
                                    @endphp

                                    <td class="text-success bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalCuming, 'RUB', 0, true) }}</td>
                                    <td class="bl hl text-center percent" >{{ number_format($averagePercents[$object->id], 2) }}%</td>
                                    <td class="text-danger bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalGeneral, 'RUB', 0, true) }}</td>

                                    @foreach($generalInfo as $info)
                                        @if (isset ($info['info'][$object->id]))
                                            <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info['info'][$object->id]['cuming_amount'], 'RUB', 0, true) }}</td>
                                            <td class="text-center percent" >{{ number_format(($info['info'][$object->id]['cuming_amount'] > 0 ? abs($info['info'][$object->id]['general_amount'] / $info['info'][$object->id]['cuming_amount']) : 0) * 100, 2) }}%</td>
                                            <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info['info'][$object->id]['general_amount'], 'RUB', 0, true) }}</td>
                                        @else
                                            <td class="bl">-</td>
                                            <td>-</td>
                                            <td class="br">-</td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endif
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
    </style>
@endpush
