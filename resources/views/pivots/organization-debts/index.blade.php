@extends('layouts.app')

@section('title', 'Долги по контрагентам')
@section('toolbar-title', 'Долги по контрагентам')
@section('breadcrumbs', Breadcrumbs::render('pivots.organization_debts.index'))

@section('content')
    @include('pivots.organization-debts.modals.filter')

    <div class="card mb-5 mb-xl-8 border-0">
        <div class="card-header border-0 pt-6 pe-0">
            <div class="card-title"></div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#filterOrganizationDebtsModal">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                            </svg>
                        </span>
                        Фильтр
                    </button>

{{--                    <form action="{{ route('pivots.debts.exports.store') }}" method="POST" class="hidden">--}}
{{--                        @csrf--}}
{{--                        <a--}}
{{--                                href="javascript:void(0);"--}}
{{--                                class="btn btn-light-primary"--}}
{{--                                onclick="event.preventDefault(); this.closest('form').submit();"--}}
{{--                        >--}}
{{--                            <span class="svg-icon svg-icon-2">--}}
{{--                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">--}}
{{--                                    <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black"></rect>--}}
{{--                                    <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black"></path>--}}
{{--                                    <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4"></path>--}}
{{--                                </svg>--}}
{{--                            </span>--}}
{{--                            Экспорт в Excel--}}
{{--                        </a>--}}
{{--                    </form>--}}
                </div>
            </div>
        </div>
        <div class="card-body p-0 ps-0">
            <div class="table-responsive freeze-table">
                <table class="table table-hover align-middle table-row-dashed fs-7">
                    <thead>
                        <tr class="text-center text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-200px ps-2" style="vertical-align: middle;">Контрагент</th>
                            <th class="min-w-200px hl" style="vertical-align: middle;">Итого</th>

                            @foreach($pivot['organizations'] as $organizationName => $organizationInfo)
{{--                                @continue(!is_valid_amount_in_range($organizationInfo['total']))--}}

                                <th class="min-w-150px" style="vertical-align: middle;">@include('partials.check_organization', ['organizationName' => $organizationName])</th>
                            @endforeach
                        </tr>
                        <tr class="fw-bolder">
                            <th class="ps-4 hl" style="vertical-align: middle;">Итого</th>
                            <th class="text-right hl">{{ \App\Models\CurrencyExchangeRate::format($pivot['total']['total']) }}</th>

                            @foreach($pivot['organizations'] as $organizationName => $organizationInfo)
{{--                                @continue(!is_valid_amount_in_range($organizationInfo['total']))--}}

                                <th class="min-w-150px text-right hl">
                                    <span class="">{{ \App\Models\CurrencyExchangeRate::format($organizationInfo['total']) }}</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        <tr class="fw-bolder">
                            <td class="ps-4">Долг по подрядчикам</td>
                            <td class="text-right hl">
                                {{ \App\Models\CurrencyExchangeRate::format($pivot['total']['contractors']['total']) }}
                            </td>

                            @foreach($pivot['organizations'] as $organizationName => $organizationInfo)
{{--                                @continue(!is_valid_amount_in_range($organizationInfo['total']))--}}

                                <th class="min-w-150px text-right">{{ \App\Models\CurrencyExchangeRate::format($organizationInfo['contractors']['total'], 'RUB', 0, true) }}</th>
                            @endforeach
                        </tr>

                        <tr>
                            <td class="ps-8 fst-italic">Неотработанный аванс</td>
                            <td class="text-right hl">
                                {{ \App\Models\CurrencyExchangeRate::format($pivot['total']['contractors']['unwork_avans']) }}
                            </td>

                            @foreach($pivot['organizations'] as $organizationName => $organizationInfo)
{{--                                @continue(!is_valid_amount_in_range($organizationInfo['total']))--}}

                                <th class="min-w-150px text-right">{{ \App\Models\CurrencyExchangeRate::format($organizationInfo['contractors']['unwork_avans'], 'RUB', 0, true) }}</th>
                            @endforeach
                        </tr>

                        <tr>
                            <td class="ps-8 fst-italic">Гарантийное удержание</td>
                            <td class="text-right hl">
                                {{ \App\Models\CurrencyExchangeRate::format($pivot['total']['contractors']['guarantee']) }}
                            </td>

                            @foreach($pivot['organizations'] as $organizationName => $organizationInfo)
{{--                                @continue(!is_valid_amount_in_range($organizationInfo['total']))--}}

                                <th class="min-w-150px text-right">{{ \App\Models\CurrencyExchangeRate::format($organizationInfo['contractors']['guarantee'], 'RUB', 0, true) }}</th>
                            @endforeach
                        </tr>

                        <tr>
                            <td class="ps-8 fst-italic">Гарантийное удержание (в т.ч. срок наступил)</td>
                            <td class="text-right hl">
                                {{ \App\Models\CurrencyExchangeRate::format($pivot['total']['contractors']['guarantee_deadline']) }}
                            </td>

                            @foreach($pivot['organizations'] as $organizationName => $organizationInfo)
{{--                                @continue(!is_valid_amount_in_range($organizationInfo['total']))--}}

                                <th class="min-w-150px text-right">{{ \App\Models\CurrencyExchangeRate::format($organizationInfo['contractors']['guarantee_deadline'], 'RUB', 0, true) }}</th>
                            @endforeach
                        </tr>

                        <tr>
                            <td class="ps-8 fst-italic">Авансы к оплате</td>
                            <td class="text-right hl">
                                {{ \App\Models\CurrencyExchangeRate::format($pivot['total']['contractors']['avans']) }}
                            </td>

                            @foreach($pivot['organizations'] as $organizationName => $organizationInfo)
{{--                                @continue(!is_valid_amount_in_range($organizationInfo['total']))--}}

                                <th class="min-w-150px text-right">{{ \App\Models\CurrencyExchangeRate::format($organizationInfo['contractors']['avans'], 'RUB', 0, true) }}</th>
                            @endforeach
                        </tr>

                        <tr>
                            <td class="ps-8 fst-italic">Долг за СМР</td>
                            <td class="text-right hl">
                                {{ \App\Models\CurrencyExchangeRate::format($pivot['total']['contractors']['amount']) }}
                            </td>

                            @foreach($pivot['organizations'] as $organizationName => $organizationInfo)
{{--                                @continue(!is_valid_amount_in_range($organizationInfo['total']))--}}

                                <th class="min-w-150px text-right">{{ \App\Models\CurrencyExchangeRate::format($organizationInfo['contractors']['amount'], 'RUB', 0, true) }}</th>
                            @endforeach
                        </tr>

                        <tr class="fw-bolder">
                            <td class="ps-4">Долг поставщикам</td>
                            <td class="text-right hl">
                                {{ \App\Models\CurrencyExchangeRate::format($pivot['total']['providers']['total']) }}
                            </td>

                            @foreach($pivot['organizations'] as $organizationName => $organizationInfo)
{{--                                @continue(!is_valid_amount_in_range($organizationInfo['total']))--}}

                                <th class="min-w-150px text-right">{{ \App\Models\CurrencyExchangeRate::format($organizationInfo['providers']['total'], 'RUB', 0, true) }}</th>
                            @endforeach
                        </tr>

                        <tr>
                            <td class="ps-8 fst-italic">Фиксированная сумма</td>
                            <td class="text-right hl">
                                {{ \App\Models\CurrencyExchangeRate::format($pivot['total']['providers']['amount_fix']) }}
                            </td>

                            @foreach($pivot['organizations'] as $organizationName => $organizationInfo)
{{--                                @continue(!is_valid_amount_in_range($organizationInfo['total']))--}}

                                <th class="min-w-150px text-right">{{ \App\Models\CurrencyExchangeRate::format($organizationInfo['providers']['amount_fix'], 'RUB', 0, true) }}</th>
                            @endforeach
                        </tr>

                        <tr>
                            <td class="ps-8 fst-italic">Изменяемая сумма</td>
                            <td class="text-right hl">
                                {{ \App\Models\CurrencyExchangeRate::format($pivot['total']['providers']['amount_float']) }}
                            </td>

                            @foreach($pivot['organizations'] as $organizationName => $organizationInfo)
{{--                                @continue(!is_valid_amount_in_range($organizationInfo['total']))--}}

                                <th class="min-w-150px text-right">{{ \App\Models\CurrencyExchangeRate::format($organizationInfo['providers']['amount_float'], 'RUB', 0, true) }}</th>
                            @endforeach
                        </tr>

                        <tr class="fw-bolder">
                            <td class="ps-4">Долг за услуги</td>
                            <td class="text-right hl">
                                {{ \App\Models\CurrencyExchangeRate::format($pivot['total']['service']['total']) }}
                            </td>

                            @foreach($pivot['organizations'] as $organizationName => $organizationInfo)
{{--                                @continue(!is_valid_amount_in_range($organizationInfo['total']))--}}

                                <th class="min-w-150px text-right">{{ \App\Models\CurrencyExchangeRate::format($organizationInfo['service']['total'], 'RUB', 0, true) }}</th>
                            @endforeach
                        </tr>

                        <tr>
                            <td class="ps-8 fst-italic">Сумма долга</td>
                            <td class="text-right hl">
                                {{ \App\Models\CurrencyExchangeRate::format($pivot['total']['service']['amount']) }}
                            </td>

                            @foreach($pivot['organizations'] as $organizationName => $organizationInfo)
{{--                                @continue(!is_valid_amount_in_range($organizationInfo['total']))--}}

                                <th class="min-w-150px text-right">{{ \App\Models\CurrencyExchangeRate::format($organizationInfo['service']['amount'], 'RUB', 0, true) }}</th>
                            @endforeach
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
            $('.organization-select').select2({
                sorter: function(data) {
                    return data.sort(function(a, b) {
                        return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
                    });
                },
                ajax: {
                    url: '/organizations?type=select',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            search: params.term,
                        };
                    },
                    processResults: function (data) {
                        const results = [];
                        $.each(data.organizations, function(id, text) {
                            results.push({id, text})
                        });
                        return {results};
                    }
                }
            });

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
    </style>
@endpush
