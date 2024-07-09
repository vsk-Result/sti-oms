@extends('layouts.app')

@section('toolbar-title', 'Распределение общих затрат')
@section('breadcrumbs', Breadcrumbs::render('general_costs.index'))

@section('content')
    @include('general-costs.modals.filter')

    @php
        $requestYears = request()->input('year', []);
        $requestObjects = request()->input('object_id', []);
        $isGroupClosed = request()->input('group_closed_objects', true)[0] == 'true';



            $object27_1 = \App\Models\Object\BObject::where('code', '27.1')->first();

            $periodsByYears = [
                '2017' => [
                    [
                        'start_date' => '2017-01-01',
                        'end_date' => '2017-12-31',
                        'bonus' => 0,
                    ],
                ],
                '2018' => [
                    [
                        'start_date' => '2018-01-01',
                        'end_date' => '2018-12-31',
                        'bonus' => 21421114,
                    ],
                ],
                '2019' => [
                    [
                         'start_date' => '2019-01-01',
                        'end_date' => '2019-12-31',
                        'bonus' => (39760000 + 692048),
                    ],
                ],
                '2020' => [
                    [
                        'start_date' => '2020-01-01',
                        'end_date' => '2020-12-31',
                        'bonus' => (2000000 + 418000 + 1615000),
                    ],
                ],
                '2021' => [
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
                ],
                '2022' => [
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
                ],
                '2023' => [
                    [
                        'start_date' => '2023-01-01',
                        'end_date' => '2023-07-20',
                        'bonus' => 0,
                    ],
                    [
                        'start_date' => '2023-07-21',
                        'end_date' => '2023-11-28',
                        'bonus' => 0,
                    ],
                    [
                        'start_date' => '2023-11-29',
                        'end_date' => '2023-12-31',
                        'bonus' => 0,
                    ]
                ],
                '2024' => [
                    [
                        'start_date' => '2024-01-01',
                        'end_date' => '2024-12-31',
                        'bonus' => 0,
                    ],
                ],
            ];

            $periodsByYears = array_reverse($periodsByYears, true);

            $generalCostsInfo = Illuminate\Support\Facades\Cache::get('general_costs', function() use ($periodsByYears, $object27_1) {
                $generalInfo = [];
                $groupedByYearsInfo = [];
                $generalTotalAmount = 0;

                foreach ($periodsByYears as $year => $periods) {
                    $groupedByYearsInfo[$year]['total'] = [
                        'cuming_amount' => 0,
                        'general_amount' => 0,
                    ];
                    foreach ($periods as $index => $period) {
                        $datesBetween = [$period['start_date'], $period['end_date']];
                        $paymentQuery = \App\Models\Payment::query()->whereBetween('date', $datesBetween)->whereIn('company_id', [1, 5]);
                        $generalAmount = (clone $paymentQuery)->where('code', '!=', '7.15')->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount')
                                        + (clone $paymentQuery)->where('object_id', $object27_1->id)->sum('amount')
                                        + $period['bonus'];

                        $generalInfo[$year][$index] = [
                            'start_date' => $period['start_date'],
                            'end_date' => $period['end_date'],
                            'general_amount' => $generalAmount,
                            'info' => \App\Services\ObjectService::getGeneralCostsByPeriod($period['start_date'], $period['end_date'], $period['bonus']),
                        ];

                        $generalTotalAmount += $generalAmount;

                        foreach ($generalInfo[$year][$index]['info'] as $objectId => $i) {
                            if (!isset($groupedByYearsInfo[$year][$objectId]['cuming_amount'])) {
                                $groupedByYearsInfo[$year][$objectId]['cuming_amount'] = 0;
                            }
                            if (!isset($groupedByYearsInfo[$year][$objectId]['general_amount'])) {
                                $groupedByYearsInfo[$year][$objectId]['general_amount'] = 0;
                            }

                            $groupedByYearsInfo[$year][$objectId]['cuming_amount'] += $i['cuming_amount'];
                            $groupedByYearsInfo[$year][$objectId]['general_amount'] += $i['general_amount'];
                        }

                        foreach ($generalInfo[$year][$index]['info'] as $i) {
                            $groupedByYearsInfo[$year]['total']['cuming_amount'] += $i['cuming_amount'];
                        }

                        $groupedByYearsInfo[$year]['total']['general_amount'] += $generalAmount;
                    }
                }

                return [
                    'generalInfo' => $generalInfo,
                    'groupedByYearsInfo' => $groupedByYearsInfo,
                    'generalTotalAmount' => $generalTotalAmount,
                ];
            });


    @endphp

    <div class="card mb-5 mb-xl-8 p-0 border-0">
        <div class="card-header border-0 pt-6 pe-0">
            <div class="card-title"></div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#filterGeneralCostsModal">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                            </svg>
                        </span>
                        Фильтр
                    </button>

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
                            <th class="bt bl hl percent"></th>
                            <th class="min-w-125px text-danger bt br hl text-right">{{ \App\Models\CurrencyExchangeRate::format($generalCostsInfo['generalTotalAmount'], 'RUB') }}</th>

                            @foreach($generalCostsInfo['generalInfo'] as $year => $infoArray)
                                @php
                                    if (count($requestYears) > 0 && !in_array($year, $requestYears)) {
                                        continue;
                                    }
                                @endphp
                                <th class="min-w-125px bt grouped toggle-grouped-by-year" data-year="{{ $year }}">
                                    {{ $year }}
                                    <br>
                                    <span class="text-success fs-8">{{ \App\Models\CurrencyExchangeRate::format($generalCostsInfo['groupedByYearsInfo'][$year]['total']['cuming_amount'], 'RUB') }}</span>
                                </th>
                                <th class="min-w-125px text-danger bt br text-right grouped">{{ \App\Models\CurrencyExchangeRate::format($generalCostsInfo['groupedByYearsInfo'][$year]['total']['general_amount'], 'RUB') }}</th>

                                @foreach($infoArray as $info)
                                    <th style="display: none;" class="min-w-125px bt grouped-by-year" data-year="{{ $year }}">
                                        с {{ \Carbon\Carbon::parse($info['start_date'])->format('d.m.Y') }} по {{ \Carbon\Carbon::parse($info['end_date'])->format('d.m.Y') }}
                                    </th>
                                    <th style="display: none;" class="bt percent grouped-by-year" data-year="{{ $year }}"></th>
                                    <th style="display: none;" class="min-w-125px text-danger bt br text-right grouped-by-year" data-year="{{ $year }}">{{ \App\Models\CurrencyExchangeRate::format($info['general_amount'], 'RUB') }}</th>
                                @endforeach
                            @endforeach
                        </tr>
                        <tr>
                            <th class="min-w-250px bl ps-2">Объект</th>

                            <th class="min-w-125px bl hl text-center">Получено</th>
                            <th class="bl hl text-center percent" >%</th>
                            <th class="min-w-125px br hl text-center">Общие расходы</th>

                            @foreach($generalCostsInfo['generalInfo'] as $year => $infoArray)
                                @php
                                    if (count($requestYears) > 0 && !in_array($year, $requestYears)) {
                                        continue;
                                    }
                                @endphp

                                <th class="min-w-125px grouped">Получено</th>
                                <th class="min-w-125px br grouped">Общие расходы</th>

                                @foreach($infoArray as $objectId => $info)
                                    <th style="display: none;" class="min-w-125px grouped-by-year" data-year="{{ $year }}">Получено</th>
                                    <th style="display: none;" class="text-center percent grouped-by-year" data-year="{{ $year }}">%</th>
                                    <th style="display: none;" class="min-w-125px br grouped-by-year" data-year="{{ $year }}">Общие расходы</th>
                                @endforeach
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @php
                            $averagePercents = [];
                            foreach($objects as $object) {
                                $percentSum = 0;
                                $percentCount = 0;

                                foreach($generalCostsInfo['generalInfo'] as $info) {
                                    if (isset($info['info'][$object->id])) {
                                        $percent = ($info['info'][$object->id]['cuming_amount'] > 0 ? abs($info['info'][$object->id]['general_amount'] / $info['info'][$object->id]['cuming_amount']) : 0) * 100;
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
                                if ($object->isBlocked() && $isGroupClosed) {
                                    $closedObjects[] = $object;
                                } else {
                                    $activeObjects[] = $object;
                                }
                            }
                        @endphp

                        @foreach($activeObjects as $object)
                            @php
                                if (count($requestObjects) > 0 && !in_array($object->id, $requestObjects)) {
                                    continue;
                                }
                            @endphp
                            <tr>
                                <td class="bl ps-2"><a href="{{ route('objects.show', $object) }}" class="text-gray-800 text-hover-primary fs-7 me-3">{{ $object->getName() }}</a></td>

                                @php
                                    $totalCuming = 0;
                                    $totalGeneral = 0;

                                    foreach($generalCostsInfo['generalInfo'] as $year => $infoArray) {
                                        if (count($requestYears) > 0 && !in_array($year, $requestYears)) {
                                            continue;
                                        }

                                        foreach($infoArray as $info) {
                                            $totalCuming += ($info['info'][$object->id]['cuming_amount'] ?? 0);
                                            $totalGeneral += ($info['info'][$object->id]['general_amount'] ?? 0);
                                        }
                                    }

                                    \App\Services\ObjectGeneralCostService::updateGeneralCost($object, $totalGeneral);
                                @endphp

                                <td class="text-success bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalCuming, 'RUB', 0, true) }}</td>
                                <td class="bl hl text-center percent" >{{ number_format(($totalCuming > 0 ? abs($totalGeneral / $totalCuming) : 0) * 100, 2) }}%</td>
                                <td class="text-danger bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalGeneral, 'RUB', 0, true) }}</td>

                                @foreach($generalCostsInfo['generalInfo'] as $year => $infoArray)
                                    @php
                                        if (count($requestYears) > 0 && !in_array($year, $requestYears)) {
                                            continue;
                                        }
                                    @endphp

                                    @if (isset($generalCostsInfo['groupedByYearsInfo'][$year][$object->id]))
                                        <td class="text-success bl text-right grouped">{{ \App\Models\CurrencyExchangeRate::format($generalCostsInfo['groupedByYearsInfo'][$year][$object->id]['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right grouped">{{ \App\Models\CurrencyExchangeRate::format($generalCostsInfo['groupedByYearsInfo'][$year][$object->id]['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl grouped">-</td>
                                        <td class="br grouped">-</td>
                                    @endif


                                    @foreach($infoArray as $info)
                                        @if (isset ($info['info'][$object->id]))
                                            <td style="display: none;" class="text-success bl text-right grouped-by-year" data-year="{{ $year }}">{{ \App\Models\CurrencyExchangeRate::format($info['info'][$object->id]['cuming_amount'], 'RUB', 0, true) }}</td>
                                            <td style="display: none;" class="text-center percent grouped-by-year" data-year="{{ $year }}">{{ number_format(($info['info'][$object->id]['cuming_amount'] > 0 ? abs($info['info'][$object->id]['general_amount'] / $info['info'][$object->id]['cuming_amount']) : 0) * 100, 2) }}%</td>
                                            <td style="display: none;" class="text-danger br text-right grouped-by-year" data-year="{{ $year }}">{{ \App\Models\CurrencyExchangeRate::format($info['info'][$object->id]['general_amount'], 'RUB', 0, true) }}</td>
                                        @else
                                            <td style="display: none;" class="bl grouped-by-year" data-year="{{ $year }}">-</td>
                                            <td style="display: none;" class="grouped-by-year" data-year="{{ $year }}">-</td>
                                            <td style="display: none;" class="br grouped-by-year" data-year="{{ $year }}">-</td>
                                        @endif
                                    @endforeach
                                @endforeach
                            </tr>
                        @endforeach

                        @php
                            $closedInfo = [];
                            foreach ($closedObjects as $object) {
                                if (!$isGroupClosed) {
                                    continue;
                                }

                                if (count($requestObjects) > 0 && !in_array($object->id, $requestObjects)) {
                                    continue;
                                }

                                foreach($generalCostsInfo['generalInfo'] as $year => $infoArray) {
                                    if (count($requestYears) > 0 && !in_array($year, $requestYears)) {
                                        continue;
                                    }

                                    if (!isset($closedInfo[$year]['cuming_amount'])) {
                                        $closedInfo[$year]['cuming_amount'] = 0;
                                    }
                                    if (!isset($closedInfo[$year]['general_amount'])) {
                                        $closedInfo[$year]['general_amount'] = 0;
                                    }

                                    foreach($infoArray as $index => $info) {
                                        if (!isset($closedInfo[$index]['cuming_amount'])) {
                                            $closedInfo[$index]['cuming_amount'] = 0;
                                        }
                                        if (!isset($closedInfo[$index]['general_amount'])) {
                                            $closedInfo[$index]['general_amount'] = 0;
                                        }
                                        if (!isset($closedInfo['total']['cuming_amount'])) {
                                            $closedInfo['total']['cuming_amount'] = 0;
                                        }
                                        if (!isset($closedInfo['total']['general_amount'])) {
                                            $closedInfo['total']['general_amount'] = 0;
                                        }
                                        $closedInfo[$index]['cuming_amount'] += $info['info'][$object->id]['cuming_amount'] ?? 0;
                                        $closedInfo[$index]['general_amount'] += $info['info'][$object->id]['general_amount'] ?? 0;

                                        $closedInfo['total']['cuming_amount'] += $info['info'][$object->id]['cuming_amount'] ?? 0;
                                        $closedInfo['total']['general_amount'] += $info['info'][$object->id]['general_amount'] ?? 0;

                                         $closedInfo[$year]['cuming_amount'] += $info['info'][$object->id]['cuming_amount'] ?? 0;
                                         $closedInfo[$year]['general_amount'] += $info['info'][$object->id]['general_amount'] ?? 0;
                                    }
                                }
                            }
                        @endphp

                        @if ($isGroupClosed)
                            <tr class="toggle-closed-object">
                                <td class="bl ps-2">Закрытые объекты</td>
                                <td class="text-success bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($closedInfo['total']['cuming_amount'], 'RUB', 0, true) }}</td>
                                <td class="bl hl text-center percent" >{{ number_format(($closedInfo['total']['cuming_amount'] > 0 ? abs($closedInfo['total']['general_amount'] / $closedInfo['total']['cuming_amount']) : 0) * 100, 2) }}%</td>
                                <td class="text-danger bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($closedInfo['total']['general_amount'], 'RUB', 0, true) }}</td>

                                @foreach($generalCostsInfo['generalInfo'] as $year => $infoArray)
                                    @php
                                        if (count($requestYears) > 0 && !in_array($year, $requestYears)) {
                                            continue;
                                        }
                                    @endphp

                                    <td class="text-success bl text-right grouped">{{ \App\Models\CurrencyExchangeRate::format($closedInfo[$year]['cuming_amount'], 'RUB', 0, true) }}</td>
                                    <td class="text-danger br text-right grouped">{{ \App\Models\CurrencyExchangeRate::format($closedInfo[$year]['general_amount'], 'RUB', 0, true) }}</td>

                                    @foreach($infoArray as $index => $info)
                                        <td style="display: none;" class="text-success bl text-right grouped-by-year" data-year="{{ $year }}">{{ \App\Models\CurrencyExchangeRate::format($closedInfo[$index]['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td style="display: none;" class="text-center percent grouped-by-year" data-year="{{ $year }}">{{ number_format(($closedInfo[$index]['cuming_amount'] > 0 ? abs($closedInfo[$index]['general_amount'] / $closedInfo[$index]['cuming_amount']) : 0) * 100, 2) }}%</td>
                                        <td style="display: none;" class="text-danger br text-right grouped-by-year" data-year="{{ $year }}">{{ \App\Models\CurrencyExchangeRate::format($closedInfo[$index]['general_amount'], 'RUB', 0, true) }}</td>
                                    @endforeach
                                @endforeach
                            </tr>
                        @endif

                        @foreach($closedObjects as $object)
                            @php
                                if (count($requestObjects) > 0 && !in_array($object->id, $requestObjects)) {
                                    continue;
                                }

                                if (! $isGroupClosed) {
                                    continue;
                                }
                            @endphp
                            <tr class="closed-object" style="display: none;">
                                <td class="bl ps-2">{{ $object->getName() }}</td>

                                @php
                                    $totalCuming = 0;
                                    $totalGeneral = 0;

                                    foreach($generalCostsInfo['generalInfo'] as $year => $infoArray) {

                                        if (count($requestYears) > 0 && !in_array($year, $requestYears)) {
                                            continue;
                                        }

                                        foreach($infoArray as $info) {
                                            $totalCuming += ($info['info'][$object->id]['cuming_amount'] ?? 0);
                                            $totalGeneral += ($info['info'][$object->id]['general_amount'] ?? 0);
                                        }
                                    }

                                    \App\Services\ObjectGeneralCostService::updateGeneralCost($object, $totalGeneral);
                                @endphp

                                <td class="text-success bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalCuming, 'RUB', 0, true) }}</td>
                                <td class="bl hl text-center percent" >{{ number_format(($totalCuming > 0 ? abs($totalGeneral / $totalCuming) : 0) * 100, 2) }}%</td>
                                <td class="text-danger bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalGeneral, 'RUB', 0, true) }}</td>

                                @foreach($generalCostsInfo['generalInfo'] as $year => $infoArray)
                                    @php
                                        if (count($requestYears) > 0 && !in_array($year, $requestYears)) {
                                            continue;
                                        }
                                    @endphp

                                    @if (isset($generalCostsInfo['groupedByYearsInfo'][$year][$object->id]))
                                        <td class="text-success bl text-right grouped">{{ \App\Models\CurrencyExchangeRate::format($generalCostsInfo['groupedByYearsInfo'][$year][$object->id]['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right grouped">{{ \App\Models\CurrencyExchangeRate::format($generalCostsInfo['groupedByYearsInfo'][$year][$object->id]['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl grouped">-</td>
                                        <td class="br grouped">-</td>
                                    @endif

                                    @foreach($infoArray as $info)
                                        @if (isset ($info['info'][$object->id]))
                                            <td style="display: none;" class="text-success bl text-right grouped-by-year" data-year="{{ $year }}">{{ \App\Models\CurrencyExchangeRate::format($info['info'][$object->id]['cuming_amount'], 'RUB', 0, true) }}</td>
                                            <td style="display: none;" class="text-center percent grouped-by-year" data-year="{{ $year }}">{{ number_format(($info['info'][$object->id]['cuming_amount'] > 0 ? abs($info['info'][$object->id]['general_amount'] / $info['info'][$object->id]['cuming_amount']) : 0) * 100, 2) }}%</td>
                                            <td style="display: none;" class="text-danger br text-right grouped-by-year" data-year="{{ $year }}">{{ \App\Models\CurrencyExchangeRate::format($info['info'][$object->id]['general_amount'], 'RUB', 0, true) }}</td>
                                        @else
                                            <td style="display: none;" class="bl grouped-by-year" data-year="{{ $year }}">-</td>
                                            <td style="display: none;" class="grouped-by-year" data-year="{{ $year }}">-</td>
                                            <td style="display: none;" class="br grouped-by-year" data-year="{{ $year }}">-</td>
                                        @endif
                                    @endforeach
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
            background-color: #f7f7f799;
        }

        .toggle-grouped-by-year {
            font-weight: bold !important;
            cursor: pointer !important;
        }
    </style>
@endpush
