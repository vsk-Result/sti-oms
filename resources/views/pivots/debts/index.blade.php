@extends('layouts.app')

@section('toolbar-title', 'Долги от СТИ')
@section('breadcrumbs', Breadcrumbs::render('pivots.debts.index'))

@section('content')
    <div class="card mb-5 mb-xl-8">
        <div class="card-body py-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle table-row-dashed fs-7">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-200px br bb ps-2">Контрагент</th>
                            <th class="min-w-200px hl">Итого</th>
                            @php $i = 0; @endphp
                            @foreach($pivot['objects'] as $object)
                                <th data-index="{{ $i++ }}" class="min-w-150px bb {{ $loop->first ? 'bl' : '' }}">{{ $object->getName() }}</th>
                            @endforeach
                        </tr>
                        <tr class="fw-bolder" style="background-color: #f7f7f7;">
                            <th class="ps-4 br hl" style="vertical-align: middle;">Итого</th>
                            <th class="hl"></th>
                            @php $i = 0; @endphp
                            @foreach($pivot['total'] as $objectId => $amount)
                                <th class="text-danger min-w-150px hl" data-index="{{ $i++ }}">
                                    <a target="_blank" href="{{ route('debts.index') }}?object_id%5B%5D={{ $objectId }}" class="text-danger bb cursor-pointer show-rows">{{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB') }}</a>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @forelse($pivot['organizations'] as $organization)
                            <tr>
                                <td class="ps-4 br {{ $loop->first ? 'bt' : '' }}">
                                    {{ $organization->name }}
                                </td>
                                <td class="text-danger hl {{ $loop->first ? 'bt' : '' }}">
                                    <a target="_blank" href="{{ route('debts.index') }}?organization_id%5B%5D={{ $organization->id }}" class="text-danger bb cursor-pointer show-columns">{{ \App\Models\CurrencyExchangeRate::format(array_sum($pivot['entries'][$organization->id]), 'RUB') }}</a>
                                </td>
                                @php $i = 0; @endphp
                                @foreach($pivot['objects'] as $object)
                                    <td class="text-danger {{ $loop->parent->first ? 'bt' : '' }} {{ $loop->first ? ' bl' : '' }}" data-index="{{ $i++ }}">
                                        @if (($pivot['entries'][$organization->id][$object->id] ?? 0) < 0)
                                            <a target="_blank" href="{{ route('debts.index') }}?organization_id%5B%5D={{ $organization->id }}&object_id%5B%5D={{ $object->id }}" class="text-danger bb cursor-pointer">{{ \App\Models\CurrencyExchangeRate::format($pivot['entries'][$organization->id][$object->id] ?? 0, 'RUB', 0, true) }}</a>
                                        @else
                                            {{ \App\Models\CurrencyExchangeRate::format($pivot['entries'][$organization->id][$object->id] ?? 0, 'RUB', 0, true) }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ (count($pivot['objects']) + 2) }}">
                                    <p class="text-left text-dark fw-bolder d-block my-4 fs-6">
                                        Данные отсутствуют
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            // $('.show-columns').on('click', function() {
            //     const $tr = $(this).parent().parent();
            //     const index = $(this).parent().index();
            //     $tr.find('td:gt(' + index + ')').each(function() {
            //         if ($(this).text().indexOf('₽') === -1) {
            //             $('[data-index=' + $(this).data('index') + ']').toggle();
            //         }
            //     });
            // });
            // $('.show-rows').on('click', function() {
            //
            // });
        });
    </script>
@endpush

@push('styles')
    <style>
        .table td, .table th {
            border: 1px solid #eee;
        }
        .bl {
            border-left: 1px dashed #ccc !important;
        }
        .br {
            border-right: 1px dashed #ccc !important;
        }
        .bb {
            border-bottom: 1px dashed #ccc !important;
        }
        .bt {
            border-top: 1px dashed #ccc !important;
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
    </style>
@endpush
