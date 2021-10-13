@extends('layouts.app')

@section('toolbar-title', 'Изменение выписки')
@section('breadcrumbs', Breadcrumbs::render('statements.edit', $statement))

@section('content')
    @include('sidebars.cost_codes')
    @include('statements.modals.split_payment_modal')

    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid">

            <div class="card mb-5 mb-xl-10">
                <div class="card-body pt-9 pb-0">
                    <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center mb-4">
                                        <span class="text-gray-900 fs-2 fw-bolder me-1">Выписка за {{ $statement->getDateFormatted() }}</span>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <a href="/storage/{{ $statement->file }}" class="btn btn-sm btn-primary me-3" download="Выписка за {{ $statement->getDateFormatted() }}">Скачать оригинал</a>
                                    <form action="{{ route('statements.exports.store', $statement) }}" method="POST" class="hidden">
                                        @csrf
                                        <a
                                            href="{{ route('statements.exports.store', $statement) }}"
                                            class="btn btn-sm btn-primary me-3"
                                            onclick="event.preventDefault(); this.closest('form').submit();"
                                        >
                                            Экспорт
                                        </a>
                                    </form>
                                    <form action="{{ route('statements.destroy', $statement) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                        <a
                                            href="{{ route('statements.destroy', $statement) }}"
                                            class="btn btn-sm btn-danger me-3"
                                            onclick="event.preventDefault(); if (confirm('Вы действительно хотите удалить выписку?')) {this.closest('form').submit();}"
                                        >
                                            Удалить
                                        </a>
                                    </form>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Дата</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $statement->getDateFormatted() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Компания</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $statement->company->name }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Банк</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $statement->getBankName() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Оплат</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $statement->payments_count }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Входящий остаток</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="{{ $statement->incoming_balance >= 0 ? 'text-success' : 'text-danger' }} fw-bold fs-6">{{ $statement->getIncomingBalance() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Расход</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="text-danger fw-bold fs-6">{{ $statement->getAmountPay() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Приход</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="text-success fw-bold fs-6">{{ $statement->getAmountReceive() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Исходящий остаток</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="{{ $statement->outgoing_balance >= 0 ? 'text-success' : 'text-danger' }} fw-bold fs-6">{{ $statement->getOutgoingBalance() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Загрузил</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $statement->createdBy->name }}</span>
                                    <span class="text-muted fw-bold text-muted fs-7">({{ $statement->created_at->format('d/m/Y H:i') }})</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Обновил</label>
                                <div class="col-lg-10 fv-row">
                                    @if ($statement->updatedBy)
                                        <span class="fw-bold text-gray-800 fs-6">{{ $statement->updatedBy->name }}</span>
                                        <span class="text-muted fw-bold text-muted fs-7">({{ $statement->updated_at->format('d/m/Y H:i') }})</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Статус</label>
                                <div class="col-lg-10 fv-row">
                                    @include('partials.status', ['status' => $statement->getStatus()])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-center justify-content-between flex-row w-100">
                        <span class="card-label fw-bolder fs-3 mb-1">Оплаты</span>

                        <label class="form-check form-switch form-check-custom form-check-solid">
                            <span class="form-check-label fs-6 me-2">
                                Все
                            </span>
                            <input id="filter-payment" class="form-check-input h-20px w-40px" type="checkbox" value=""/>
                            <span class="form-check-label fs-6">
                                Без объекта
                            </span>
                        </label>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <div class="table-responsive">
                        <table data-payment-store-url="{{ route('payments.store') }}" class="table-payments table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-150px">Объект</th>
                                <th class="min-w-70px">Кост код</th>
                                <th class="min-w-100px">Организация</th>
                                <th class="min-w-400px">Описание</th>
                                <th class="min-w-150px">Сумма</th>
                                <th class="min-w-150px">Категория</th>
                                <th class="min-w-180px">Статус</th>
                                <th class="min-w-120px">Действие</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @foreach($statement->payments()->orderByDesc('amount')->get() as $payment)
                                    @include('statements.partials._edit_payment_table_row')
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>

        let $selectedRow = null;

        $(document).on('td select2:open', function() {
            document.querySelector('.select2-search__field').focus();
        });

        $(document).on('select2:clear', 'td select', function() {
            const $that = $(this);
            updatePayment($that.closest('tr'), $that.attr('name'), $that.val());
        });

        $(document).on('select2:select', 'td select', function() {
            const $that = $(this);
            const name = $that.attr('name');
            const $tr = $that.closest('tr');
            const $next = $tr.next().find('select[name=' + name + ']');

            updatePayment($tr, name, $that.val());

            if ($next && ($next.val() === '' || $next.val() == null)) {
                $next.select2('open');
            }

            if (name === 'object_id') {
                if ($('#filter-payment').prop('checked')) {
                    $tr.hide();
                }
            }
        });

        $(document).on('click', '#filter-payment', function() {
            if ($(this).prop('checked')) {
                $('.table-payments tbody tr').each(function () {
                    if ($(this).find('td:first-child select').first().val() != 0) {
                        $(this).hide();
                    }
                });
            } else {
                $('.table-payments tbody tr').each(function () {
                    $(this).show();
                });
            }
        });

        $(document).on('click', '.clone-payment', function() {

            const $that = $(this);
            $.post(
                $('.table-payments').data('payment-store-url'),
                {
                    'base_payment_id': $that.data('payment-id')
                }
            )
            .done(function(data) {
                if (data.status === 'success') {

                    $('select.form-select').select2('destroy');

                    const $tr = $that.closest('tr');
                    const $cloneTr = $tr.clone();

                    $cloneTr.data('update-payment-url', data.payment.update_url);
                    $cloneTr.find('.destroy-payment').data('payment-destroy-url', data.payment.destroy_url);
                    $cloneTr.find('.clone-payment').data('payment-id', data.payment.id);
                    $cloneTr.insertBefore($tr);

                    $cloneTr.addClass('new-row');

                    KTApp.initSelect2();

                    $cloneTr.find('select').trigger('change');

                    toastr.success(data.message);
                } else if (data.status === 'error') {
                    toastr.error('Ошибка. ' + data.message);
                }
            })
            .fail(function(xhr) {
                console.log('Ошибка. [' + xhr.status + '] ' + xhr.responseJSON.message);
                if (xhr.status === 419) {
                    toastr.error('Ошибка сессии. Автоматическая перезагрузка страницы через 1 сек.');
                    setTimeout(() => {
                        window.location.reload(false);
                    }, 1000);
                }
            });
        });

        $(document).on('click', '.split-payment', function() {
            updateCRMAvansesImportsList();
            $selectedRow = $(this);
        });

        $(document).on('click', '#split-payment-submit', function() {
            const url = $selectedRow.data('split-payment-url');
            $selectedRow = $selectedRow.closest('tr');
            $.post(
                url,
                {
                    'crm_avans_import_id': $('#crm-avans-import-id').val()
                }
            )
            .done(function(data) {
                if (data.status === 'success') {
                    toastr.success(data.message);
                    $('select.form-select').select2('destroy');
                    $.each(data.view_render, (key, value) => {
                        $(value).insertAfter($selectedRow)
                    });
                    $selectedRow.remove();
                    KTApp.initSelect2();
                } else if (data.status === 'error') {
                    toastr.error('Ошибка. ' + data.message);
                }
            })
            .fail(function(xhr) {
                console.log('Ошибка. [' + xhr.status + '] ' + xhr.responseJSON.message);
                if (xhr.status === 419) {
                    toastr.error('Ошибка сессии. Автоматическая перезагрузка страницы через 1 сек.');
                    setTimeout(() => {
                        window.location.reload(false);
                    }, 1000);
                }
            })
            .always(function() {
                $('#splitPaymentModal').modal('hide');
            });
        });

        $(document).on('click', '.destroy-payment', function() {
            if (confirm('Вы действительно хотите удалить запись об оплате?')) {
                const $that = $(this);
                $.ajax({
                    url: $that.data('payment-destroy-url'),
                    type: 'DELETE'
                })
                .done(function(data) {
                    if (data.status === 'success') {
                        const $tr = $that.closest('tr');
                        $tr.find('select').select2('destroy');
                        $tr.remove();
                        toastr.success(data.message);
                    } else if (data.status === 'error') {
                        toastr.error('Ошибка. ' + data.message);
                    }
                })
                .fail(function(xhr) {
                    console.log('Ошибка. [' + xhr.status + '] ' + xhr.responseJSON.message);
                    if (xhr.status === 419) {
                        toastr.error('Ошибка сессии. Автоматическая перезагрузка страницы через 1 сек.');
                        setTimeout(() => {
                            window.location.reload(false);
                        }, 1000);
                    }
                });
            }
        });

        $(document).on('focus', '.db-field', function() {
            $(this).data('initial-text', $(this).val());
        });

        $(document).on('focus', '.db-field[name=code]', function() {
            if (! $('#kt_explore').hasClass('drawer-on')) {
                $('#kt_explore_toggle').trigger('click');
            }
        });

        $(document).on('blur', '.db-field[name=code]', function() {
            if ($('#kt_explore').hasClass('drawer-on')) {
                $('#kt_explore_toggle').trigger('click');
            }
        });

        $(document).on('keyup', '.db-field', function(e) {
            const field = $(this).attr('name');
            if (e.keyCode === 13) {
                const $next = $(this).closest('tr').next().find('.db-field[name=' + field + ']');
                if ($next && ($next.val() === '' || $next.val() == null)) {
                    $next.focus();
                    return false;
                }
            }
            if (field === 'amount' || field === 'code') {
                $(this).val($(this).val().replace(/[^-.,0-9]/, ''));
                $(this).val($(this).val().replace(',', '.'));
            }
        });

        $(document).on('blur', '.db-field', function() {
            const $that = $(this);
            const field = $that.attr('name');
            const text = $that.val();

            if (field === 'amount') {
                if (text.indexOf('-') !== -1) {
                    $that.removeClass('text-success').addClass('text-danger');
                } else {
                    $that.removeClass('text-danger').addClass('text-success');
                }

                if (text === '') {
                    $that.val('0.00');
                } else if (text.indexOf('.') === -1) {
                    $that.val(text + '.00');
                }
            } else if (field === 'code') {
                if (text.indexOf(',') !== -1) {
                    $that.val(text.replace(',', '.'));
                }
            }

            if ($that.data('initial-text') !== text) {
                updatePayment($that.closest('tr'), field, text);
            }
        });

        function updateCRMAvansesImportsList() {
            const $select = $('#crm-avans-import-id');

            $.ajax({
                url: $('#splitPaymentModal').data('crm-avanses-imports-list-url'),
                type: 'GET'
            })
                .done(function(data) {
                    if (data.status === 'success') {
                        const config = $select.data('select2').options.options;
                        $select
                            .select2('destroy')
                            .html('')
                            .append(
                                $.map(data.imports, (value, key) => "<option value=\"" + key + "\">" + value + "</option>")
                            )
                            .select2(config);

                        toastr.success(data.message);
                    } else if (data.status === 'error') {
                        toastr.error('Ошибка. ' + data.message);
                    }
                })
                .fail(function(xhr) {
                    console.log('Ошибка. [' + xhr.status + '] ' + xhr.responseJSON.message);
                    if (xhr.status === 419) {
                        toastr.error('Ошибка сессии. Автоматическая перезагрузка страницы через 1 сек.');
                        setTimeout(() => {
                            window.location.reload(false);
                        }, 1000);
                    }
                })
                .always(function() {
                    $('#splitPaymentModal').modal('show');
                });
        }

        function updatePayment($row, $field, $value) {
            $.post(
                $row.data('payment-update-url'),
                {[$field]: $value}
            )
            .done(function(data) {
                if (data.status === 'success') {
                    toastr.success(data.message);
                } else if (data.status === 'error') {
                    toastr.error('Ошибка. ' + data.message);
                }
            })
            .fail(function(xhr) {
                console.log('Ошибка. [' + xhr.status + '] ' + xhr.responseJSON.message);
                if (xhr.status === 419) {
                    toastr.error('Ошибка сессии. Автоматическая перезагрузка страницы через 1 сек.');
                    setTimeout(() => {
                        window.location.reload(false);
                    }, 1000);
                }
            });
        }
    </script>
@endpush

@push('styles')
    <style>
        table.table-hover tr:hover .form-select.form-select-solid {
            background-color: #e3e4e4 !important;
        }
    </style>
@endpush
