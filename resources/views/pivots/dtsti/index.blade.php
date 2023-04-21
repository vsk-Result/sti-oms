@extends('layouts.app')

@section('toolbar-title', 'Долги ДТ - СТИ')
@section('breadcrumbs', Breadcrumbs::render('pivots.dtsti.index'))

@section('content')
    <div class="card mb-5 mb-xl-8 border-0">
        <div class="card-header border-0 pt-6 pe-0">
            <div class="card-title"></div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <form action="{{ route('pivots.dtsti.exports.store') }}" method="POST" class="hidden">
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
                <table class="table table-hover align-middle table-row-dashed fs-7">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-100px ps-4 br bb">Объект</th>
                            <th class="total-column hl text-right">Сумма долга</th>
                            <th class="min-w-150px bl bb text-right">Договор СТИ</th>
                            <th class="min-w-150px bb text-right pe-4">Договор ДТ</th>
                        </tr>
                        <tr class="fw-bolder" style="background-color: #f7f7f7;">
                            <th class="ps-4 br hl" style="vertical-align: middle;">Итого</th>
                            <th class="hl text-right">
                                <span class="{{ $pivot['total'] > 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($pivot['total'], 'RUB') }}</span>
                            </th>
                            <th class="hl text-right"></th>
                            <th class="hl text-right pe-4"></th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @forelse($pivot['entries'] as $entry)
                            <tr style="{{ $loop->last ? 'border-bottom: 1px solid #eee ! important;' : '' }}">
                                <td class="ps-4 br {{ $loop->first ? 'bt' : '' }} {{ $loop->last ? 'bb' : '' }}">
                                    @if(auth()->user()->can('show objects'))
                                        <a target="_blank" href="{{ route('objects.show', $entry['object']['id']) }}/contracts?object_id%5B%5D={{ $entry['object']['id'] }}" class="show-link">{{ $entry['object']['name'] }}</a>
                                    @else
                                        {{ $entry['object']['name'] }}
                                    @endif
                                </td>
                                <td class="hl text-right {{ $loop->first ? 'bt' : '' }} {{ $loop->last ? 'bb' : '' }}">
                                    <span class="{{ $pivot['total'] > 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($entry['amount'], 'RUB', 0, true) }}</span>
                                </td>
                                <td class="bl text-right {{ $loop->first ? 'bt' : '' }} {{ $loop->last ? 'bb' : '' }}">
                                    {{ $entry['sti'] }}
                                </td>
                                <td class="bl text-right {{ $loop->first ? 'bt' : '' }} {{ $loop->last ? 'bb' : '' }} pe-4">
                                    {{ $entry['dt'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <p class="text-center text-dark fw-bolder d-block my-4 fs-6">
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
            mainApp.initFreezeTable(2);
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

        .total-column {
            min-width: 150px !important;
            width: 150px !important;
            max-width: 150px !important;
        }
    </style>
@endpush
