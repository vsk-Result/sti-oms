@extends('layouts.app')

@section('title', 'Разбивка налогов')
@section('toolbar-title', 'Разбивка налогов')
@section('breadcrumbs', Breadcrumbs::render('tax_split.index'))

@section('content')
    <div class="post" id="fetch-info" data-url="{{ route('tax_split.split_info.index') }}">
        <div class="content-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 mb-1">Разбивка налогов по данным из 1С</span>
                            </h3>
                        </div>
                        <div class="card-body py-3">
                            @if (session()->has('status_success'))
                                <div class="alert alert-dismissible bg-light-success border border-dashed border-success d-flex flex-column flex-sm-row p-5 mb-10">
                                    <div class="d-flex flex-column pe-0 pe-sm-10">
                                        <h5 class="mb-1 fs-6">{{ session()->get('status_success') }}</h5>
                                    </div>
                                </div>
                            @endif

                            @if (session()->has('status_error'))
                                <div class="alert alert-dismissible bg-light-danger border border-dashed border-danger d-flex flex-column flex-sm-row p-5 mb-10">
                                    <div class="d-flex flex-column pe-0 pe-sm-10">
                                        <h5 class="mb-1 fs-6">{{ session()->get('status_error') }}</h5>
                                    </div>
                                </div>
                            @endif

                            <form class="form" action="{{ route('tax_split.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="col-md-12 mb-6 fv-row">
                                    <div class="mb-1">
                                        <label class="form-label fw-bolder text-dark fs-6">Оплаты для разбивки</label>
                                        <div class="position-relative mb-3">
                                            <select required multiple name="payment_ids[]" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                @foreach($payments as $paymentId => $paymentName)
                                                    <option value="{{ $paymentId }}">{{ $paymentName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-10 fv-row" id="loading-status">
                                    <div class="mb-1">
                                        <div class="alert alert-dismissible bg-light-primary border border-dashed border-primary d-flex flex-column flex-sm-row p-5">
                                            <div class="d-flex flex-column pe-0 pe-sm-10">
                                                <span class="fs-6">Загрузка данных из 1С...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-10 fv-row" id="split-info-result" style="display: none">
                                    Данные получены!
                                </div>

                                <div class="d-flex flex-center py-3">
                                    <button type="submit" class="btn btn-primary me-3" disabled>
                                        <span class="indicator-label">Разбить</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="post">
        <div class="content-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 mb-1">История</span>
                            </h3>
                        </div>
                        <div class="card-body py-3">
                            <div class="table-responsive freeze-table">
                                <table class="table table-hover align-middle table-row-dashed fs-6" id="kt_table_users">
                                    <thead>
                                        <tr class="text-start fw-bolder fs-7 text-uppercase gs-0">
                                            <th class="ps-3 min-w-50px">+/-</th>
                                            <th class="min-w-150px">Тип</th>
                                            <th class="min-w-125px">Дата разбивки</th>
                                            <th class="min-w-125px">Сумма</th>
                                            <th class="min-w-125px text-end rounded-end pe-4">Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="ps-3 fs-4">+</td>
                                            <td>НДФЛ</td>
                                            <td>30.07.2026</td>
                                            <td>5 000 000 ₽</td>
                                            <td class="text-end text-dark fw-bolder">
                                                <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Действия
                                                    <span class="svg-icon svg-icon-5 m-0">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                                        </svg>
                                                    </span>
                                                </a>
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="ps-3 fs-4">+</td>
                                            <td>Страховые взносы</td>
                                            <td>29.07.2026</td>
                                            <td>2 400 000 ₽</td>
                                            <td class="text-end text-dark fw-bolder">
                                                <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Действия
                                                    <span class="svg-icon svg-icon-5 m-0">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                                        </svg>
                                                    </span>
                                                </a>
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const $fetchInfo = $('#fetch-info');

        const $loader = $('#loading-status');
        const $loaderAlert = $('#loading-status .alert');
        const $loaderText = $('#loading-status span');

        $splitInfoResult = $('#split-info-result');

        const splitInformationUrl = $fetchInfo.data('url');

        const fetchSplitInformation = () => {
            $loaderAlert
                .removeClass('cursor-pointer')
                .removeClass('bg-light-danger')
                .removeClass('border-danger')
                .addClass('bg-light-primary')
                .addClass('border-primary')
                .off('click');

            $loaderText.html('Загрузка данных из 1С...');

            $loader.show();

            mainApp.sendAJAX(
                splitInformationUrl,
                'GET',
                {},
                (data) => {
                    $loader.hide();
                    $splitInfoResult.html(data.split_info_view).show();
                },
                () => {
                    $loaderAlert
                        .removeClass('bg-light-primary')
                        .removeClass('border-primary')
                        .addClass('bg-light-danger')
                        .addClass('border-danger')
                        .addClass('cursor-pointer')
                        .on('click', fetchSplitInformation);

                    $loaderText.html('Не удалось загрузить данные из 1С. Нажмите для повторной попытки.');
                },
                () => {
                    // KTApp.init();
                    // mainApp.init();
                }
            );
        }

        $(function() {
            fetchSplitInformation();
        });
    </script>
@endpush
