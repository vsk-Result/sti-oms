@extends('layouts.app')

@section('title', 'Записи кассы ' . $cashAccount->name)
@section('toolbar-title', 'Записи кассы ' . $cashAccount->name)
@section('breadcrumbs', Breadcrumbs::render('cash_accounts.payments.index', $cashAccount))

@section('content')
    @include('cash-accounts.payments.modals.filter')
    @include('cash-accounts.payments.modals.create')
    @include('cash-accounts.payments.modals.edit')

    @include('cash-accounts.modals.request_cash')
    @include('cash-accounts.modals.transfer_cash')

    @php
        $balance = $cashAccount->getBalance();
        $balanceWithTransferApprove = $cashAccount->getBalanceWithTransferApprove();
    @endphp

    <div class="card mb-5 mb-xl-8">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6">
                    <div class="d-flex align-items-center">
                        <div class="fs-4 fw-bolder {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($balance, 2, '.', ' ') }}</div>
                    </div>
                    <div class="fw-bold fs-6 text-gray-400">Баланс</div>
                </div>

                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6">
                    <div class="d-flex align-items-center">
                        <div class="fs-4 fw-bolder {{ $balanceWithTransferApprove >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($balanceWithTransferApprove, 2, '.', ' ') }}</div>
                    </div>
                    <div class="fw-bold fs-6 text-gray-400">Баланс с подтвержденными трансферами</div>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
{{--                    <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#filterPaymentModal">--}}
{{--                    <span class="svg-icon svg-icon-2">--}}
{{--                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">--}}
{{--                            <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>--}}
{{--                        </svg>--}}
{{--                    </span>--}}
{{--                        Фильтр--}}
{{--                    </button>--}}

                    @if ($cashAccount->isCurrentResponsible())
                        <a href="javascript:void(0);" data-create-payment-url="{{ route('cash_accounts.payments.create', $cashAccount) }}" class="create-payment btn btn-light-primary me-3">
                            <span class="svg-icon svg-icon-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                    <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>
                                    <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                                </svg>
                            </span>
                            Новая запись
                        </a>
                    @endif


                    <form action="{{ route('cash_accounts.payments.exports.store', $cashAccount) . (strpos(request()->fullUrl(), '?') !== false ? substr(request()->fullUrl(), strpos(request()->fullUrl(), '?')) : '') }}" method="POST" class="hidden">
                        @csrf
                        <a
                                href="javascript:void(0);"
                                class="btn btn-light-success me-3"
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

                    @if ($cashAccount->isCurrentResponsible())
                        <a href="#" class="btn btn-light-dark me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                            <span class="svg-icon svg-icon-3">
                                <span class="svg-icon svg-icon-5 m-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                    </svg>
                                </span>
                            </span>
                             Касса
                        </a>

                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-200px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#cashAccountTransferCashModal" class="menu-link px-3">Передать средства</a>
                            </div>

                            <div class="menu-item px-3">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#cashAccountRequestCashModal" class="menu-link px-3">Запросить средства</a>
                            </div>
                            @if (auth()->user()->hasRole('super-admin'))
                                <div class="menu-item px-3">
                                    <a href="{{ route('cash_accounts.edit', $cashAccount) }}" class="menu-link px-3">Изменить</a>
                                </div>

                                <div class="menu-item px-3">
                                    <form action="{{ route('cash_accounts.destroy', $cashAccount) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                        <a
                                                href="#"
                                                class="menu-link px-3 text-danger"
                                                onclick="event.preventDefault(); if (confirm('Вы действительно хотите удалить кассу?')) {this.closest('form').submit();}"
                                        >
                                            Удалить
                                        </a>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body py-3 ps-0">
            <div class="table-responsive freeze-table">
                <table class="table table-hover align-middle table-row-dashed fs-7">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th data-sort-by="type" class="ps-3 sortable-row min-w-125px">Тип</th>
                            <th data-sort-by="date" class="ps-3 sortable-row min-w-125px">Дата</th>
                            <th data-sort-by="object_id" class="sortable-row min-w-100px">Объект</th>
                            <th data-sort-by="code" class="sortable-row min-w-70px">Статья затрат</th>
                            <th data-sort-by="organization_receiver_id" class="sortable-row min-w-125px">Контрагент</th>
                            <th data-sort-by="description" class="sortable-row min-w-300px">Описание</th>
                            <th data-sort-by="amount" class="sortable-row min-w-150px">Сумма</th>
                            <th data-sort-by="category" class="sortable-row min-w-100px">Категория</th>
                            <th class="min-w-200px">Вложения</th>
                            <th class="min-w-100px">Проверка</th>
                            <th class="min-w-125px text-end rounded-end pe-4"></th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                    @forelse($payments as $payment)
                        <tr data-payment-update-url="{{ route('cash_accounts.payments.update', [$cashAccount, $payment]) }}">
                            <td class="ps-3">
                                {{ $payment->getType() }}

                                @if (! is_null($payment->getCrmAvansData()['employee_id']))
                                    <span class="text-warning">(CRM)</span>
                                @endif

                                @if ($payment->isRequest())
                                    <span class="text-{{ $payment->getRequestStatusColor() }}">({{ $payment->getRequestStatus() }})</span>
                                @endif

                                @if ($payment->isTransfer())
                                    <span class="text-{{ $payment->getTransferStatusColor() }}">({{ $payment->getTransferStatus() }})</span>
                                @endif
                            </td>
                            <td class="position-relative">
                                {{ $payment->getDateFormatted() }}
                            </td>
                            <td>
                                @if ($payment->object)
                                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $payment->object->name }}">{{ $payment->getObjectCode() }}</span>
                                @else
                                    {{ $payment->getObjectCode() }}
                                @endif
                            </td>
                            <td>
                                <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ \App\Models\KostCode::getTitleByCode($payment->code) }}">{{ $payment->code }}</span>
                            </td>
                            <td>
                                @include('partials.check_organization', ['organizationName' => $payment->organization?->name, 'organizationInn' => $payment->organization?->inn])
                            </td>
                            <td>{{ $payment->getDescription() }}</td>
                            <td>
                                <span class="{{ $payment->amount >= 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($payment->amount) }}</span>
                            </td>
                            <td>{{ $payment->category }}</td>
                            <td>
                                @foreach($payment->getMedia() as $media)
                                    <div class="d-flex align-items-center mb-3">
                                        <a target="_blank" href="{{ $media->getUrl() }}" class="text-gray-800 text-hover-primary">{{ $media->file_name . '      (' . $media->human_readable_size . ')' }}</a>
                                    </div>
                                @endforeach
                            </td>
                            <td>
                                @if ($payment->isObjectType())
                                    <label class="form-check form-check-custom form-check-solid form-check-inline">
                                        <input
                                            {{ $payment->canValidate() ? '' : 'disabled' }}
                                            {{ $payment->isValid() ? 'checked' : '' }}
                                            data-validate-ca-payment-url="{{ route('cash_accounts.payments.validate.update', [$cashAccount, $payment]) }}"
                                            class="ca-payment-valid-check form-check-input"
                                            type="checkbox"
                                            name="validate"
                                        />
                                    </label>
                                @endif
                            </td>
                            <td class="text-end text-dark fw-bolder">
                                @if ($payment->hasActions())
                                    <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Действия
                                        <span class="svg-icon svg-icon-5 m-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                            </svg>
                                        </span>
                                    </a>
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-150px py-4" data-kt-menu="true">
                                        @if ($payment->isRequest())
                                            <div class="menu-item px-3">
                                                <a href="{{ route('cash_accounts.request_cash.update', [$cashAccount->id, $payment->id]) }}?status_id={{ \App\Models\CashAccount\CashAccountPayment::TRANSFER_STATUS_APPROVE }}" class="text-success menu-link px-3">Подтвердить</a>
                                            </div>

                                            <div class="menu-item px-3">
                                                <a href="{{ route('cash_accounts.request_cash.update', [$cashAccount->id, $payment->id]) }}?status_id={{ \App\Models\CashAccount\CashAccountPayment::TRANSFER_STATUS_DECLINE }}" class="text-danger menu-link px-3">Отклонить</a>
                                            </div>

                                            @if (auth()->user()->hasRole('super-admin'))
                                                <div class="menu-item px-3">
                                                    <form action="{{ route('cash_accounts.payments.destroy', [$cashAccount, $payment]) }}" method="POST" class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a
                                                                href="javascript:void(0)"
                                                                class="menu-link px-3 text-danger"
                                                                onclick="event.preventDefault(); if (confirm('Вы действительно хотите удалить запись?')) {this.closest('form').submit();}"
                                                        >
                                                            Удалить
                                                        </a>
                                                    </form>
                                                </div>
                                            @endif
                                        @elseif ($payment->isTransfer())
                                            <div class="menu-item px-3">
                                                <a href="{{ route('cash_accounts.transfer_cash.update', [$cashAccount->id, $payment->id]) }}?status_id={{ \App\Models\CashAccount\CashAccountPayment::TRANSFER_STATUS_APPROVE }}" class="text-success menu-link px-3">Подтвердить</a>
                                            </div>

                                            <div class="menu-item px-3">
                                                <a href="{{ route('cash_accounts.transfer_cash.update', [$cashAccount->id, $payment->id]) }}?status_id={{ \App\Models\CashAccount\CashAccountPayment::TRANSFER_STATUS_DECLINE }}" class="text-danger menu-link px-3">Отклонить</a>
                                            </div>

                                            @if (auth()->user()->hasRole('super-admin'))
                                                <div class="menu-item px-3">
                                                    <form action="{{ route('cash_accounts.payments.destroy', [$cashAccount, $payment]) }}" method="POST" class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a
                                                                href="javascript:void(0)"
                                                                class="menu-link px-3 text-danger"
                                                                onclick="event.preventDefault(); if (confirm('Вы действительно хотите удалить запись?')) {this.closest('form').submit();}"
                                                        >
                                                            Удалить
                                                        </a>
                                                    </form>
                                                </div>
                                            @endif
                                        @else
                                            <div class="menu-item px-3">
                                                <a href="javascript:void(0);" data-edit-payment-url="{{ route('cash_accounts.payments.edit', [$cashAccount, $payment]) }}" class="edit-payment menu-link px-3">Изменить</a>
                                            </div>

                                            <div class="menu-item px-3">
                                                <a href="javascript:void(0);" data-create-payment-url="{{ route('cash_accounts.payments.create', $cashAccount) }}?copy_payment_id={{ $payment->id }}" class="copy-payment menu-link px-3">Сделать копию</a>
                                            </div>

                                            <div class="menu-item px-3">
                                                <form action="{{ route('cash_accounts.payments.destroy', [$cashAccount, $payment]) }}" method="POST" class="hidden">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a
                                                            href="javascript:void(0)"
                                                            class="menu-link px-3 text-danger"
                                                            onclick="event.preventDefault(); if (confirm('Вы действительно хотите удалить запись?')) {this.closest('form').submit();}"
                                                    >
                                                        Удалить
                                                    </a>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <p class="text-center text-dark fw-bolder d-block my-4 fs-6">
                                    Записи отсутствуют
                                </p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                {{ $payments->links() }}
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

            const url = new URL(document.location.href);
            const sortByField = url.searchParams.get('sort_by');
            const sortByDirection = url.searchParams.get('sort_direction');

            if (sortByField && sortByDirection) {
                const sortRow = $('th[data-sort-by=' + sortByField + ']');
                sortRow.removeClass('sorting-asc').removeClass('sorting-desc');
                sortRow.addClass('sorting-' + sortByDirection);
            }
        });

        $('.sortable-row').on('click', function(e) {
            e.preventDefault();
            const field = $(this).data('sort-by');
            const url = new URL(document.location.href);

            if (url.searchParams.has('sort_by')) {
                url.searchParams.set('sort_by', field);
            } else {
                url.searchParams.append('sort_by', field);
            }

            if (url.searchParams.has('sort_direction')) {
                url.searchParams.set('sort_direction', url.searchParams.get('sort_direction') === 'asc' ? 'desc' : 'asc');
            } else {
                url.searchParams.append('sort_direction', 'asc');
            }

            document.location = url.toString();
        });

        function updatePayment($row, field, value) {
            const url =  $row.data('payment-update-url');

            mainApp.sendAJAX(
                url,
                'POST',
                {[field]: value}
            );
        }

        $('.create-payment').on('click', function() {
            $('#createPaymentModal .modal-content').html('');
            const url = $(this).data('create-payment-url');
            mainApp.sendAJAX(
                url,
                'GET',
                {},
                (data) => {
                    $('#createPaymentModal .modal-content').html(data.payment_form);
                },
                {},
                () => {
                    KTApp.init();
                    mainApp.init();
                    $('[name=return_url]').val(window.location.href);
                    $('#createPaymentModal').modal('show');
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

                    $('#create-code').on('change', function() {
                        if ($(this).val() === '7.8.2' || $(this).val() === '7.9.2') {
                            $('#create-employee-crm').show();
                            $('#create-itr').hide();
                            $('#create-organization').hide();
                        } else if ($(this).val() === '7.8.1' || $(this).val() === '7.9.1') {
                            $('#create-itr').show();
                            $('#create-employee-crm').hide();
                            $('#create-organization').hide();
                        } else {
                            $('#create-employee-crm').hide();
                            $('#create-itr').hide();
                            $('#create-organization').show();
                        }
                    });

                    $('#create-code').trigger('change');
                }
            );
        });

        $('.copy-payment').on('click', function() {
            if (confirm('Вы действительно создать запись на основе данной?')) {
                $('#createPaymentModal .modal-content').html('');
                const url = $(this).data('create-payment-url');
                mainApp.sendAJAX(
                    url,
                    'GET',
                    {},
                    (data) => {
                        $('#createPaymentModal .modal-content').html(data.payment_form);
                    },
                    {},
                    () => {
                        KTApp.init();
                        mainApp.init();
                        $('[name=return_url]').val(window.location.href);
                        $('#createPaymentModal').modal('show');
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

                        $('#create-code').on('change', function() {
                            if ($(this).val() === '7.8.2' || $(this).val() === '7.9.2') {
                                $('#create-employee-crm').show();
                                $('#create-itr').hide();
                                $('#create-organization').hide();
                            } else if ($(this).val() === '7.8.1' || $(this).val() === '7.9.1') {
                                $('#create-itr').show();
                                $('#create-employee-crm').hide();
                                $('#create-organization').hide();
                            } else {
                                $('#create-employee-crm').hide();
                                $('#create-itr').hide();
                                $('#create-organization').show();
                            }
                        });

                        $('#create-code').trigger('change');
                    }
                );
            }
        });

        $('.edit-payment').on('click', function() {
            $('#editPaymentModal .modal-content').html('');
            const url = $(this).data('edit-payment-url');
            mainApp.sendAJAX(
                url,
                'GET',
                {},
                (data) => {
                    $('#editPaymentModal .modal-content').html(data.payment_form);
                },
                {},
                () => {
                    KTApp.init();
                    mainApp.init();
                    $('[name=return_url]').val(window.location.href);
                    $('#editPaymentModal').modal('show');
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

                    $('#edit-code').on('change', function() {
                        if ($(this).val() === '7.8.2' || $(this).val() === '7.9.2') {
                            $('#edit-employee-crm').show();
                            $('#edit-itr').hide();
                            $('#edit-organization').hide();
                        } else if ($(this).val() === '7.8.1' || $(this).val() === '7.9.1') {
                            $('#edit-itr').show();
                            $('#edit-employee-crm').hide();
                            $('#edit-organization').hide();
                        } else {
                            $('#edit-employee-crm').hide();
                            $('#edit-itr').hide();
                            $('#edit-organization').show();
                        }
                    });

                    $('#edit-code').trigger('change');
                }
            );
        });

        $('.amount-expression-quick').on('click', function() {
            $('select[name=amount_expression_operator]').val($(this).data('operator')).trigger('change');
            $('input[name=amount_expression]').val(0);

            if ($(this).is('[data-operator-force]')) {
                $('#filter-payment-submit').trigger('click');
            }
        });

        $('.period-quick').on('click', function() {
            const year = $(this).text();
            $('input[name=period]').val('01.01.' + year + ' - 31.12.' + year);
        });

        $('.description-or').on('click', function() {
            const $input = $('input[name=description]');
            $input .val($input.val() + '%%');
        });

        $('.description-and').on('click', function() {
            const $input = $('input[name=description]');
            $input .val($input.val() + '^^');
        });

        $('.ca-payment-valid-check').on('change', function() {
            const isValid = $(this).prop('checked');
            const url = $(this).data('validate-ca-payment-url');

            mainApp.sendAJAX(
                url,
                'POST',
                {
                    valid: isValid
                }
            );
        });

        mainApp.initFreezeTable(1);
    </script>
@endpush


@push('styles')
<style>
    ul.select2-results__options {
        max-height: none!important;
        overflow-y: unset!important;
    }

    ul.select2-results__options[role="listbox"] {
        max-height: 250px!important;
        overflow-y: auto!important;
    }
</style>
@endpush


