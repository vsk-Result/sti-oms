@extends('layouts.app')

@section('title', 'Изменение гарантийного удержания')
@section('toolbar-title', 'Изменение гарантийного удержания')
@section('breadcrumbs', Breadcrumbs::render('guarantees.edit', $guarantee))

@section('content')
    <div class="post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Изменение гарантийного удержания</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('guarantees.update', $guarantee) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="return_url" value="{{ url()->previous() }}">

                        <div class="row mb-5">
                            <div class="col-md-12 fv-row">

                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Договор</label>
                                            <div class="position-relative mb-3">
                                                <select name="contract_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($contracts as $contract)
                                                        <option value="{{ $contract->id }}" {{ $contract->id === $guarantee->contract_id ? 'selected' : '' }}>{{ $contract->object->code }} | {{ $contract->getName() }}</option>

                                                        @foreach($contract->children as $subContract)
                                                            <option value="{{ $subContract->id }}" {{ $subContract->id === $guarantee->contract_id ? 'selected' : '' }}>{{ $subContract->object->code }} | {{ $contract->getName() . ' | ' . $subContract->getName() }}</option>
                                                        @endforeach
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Заказчик</label>
                                            <div class="position-relative mb-3">
                                                <select name="organization_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($organizations as $organization)
                                                        <option value="{{ $organization->id }}" {{ $organization->id === $guarantee->organization_id ? 'selected' : '' }}>{{ $organization->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Объект</label>
                                            <div class="position-relative mb-3">
                                                <select name="object_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($objects as $object)
                                                        <option value="{{ $object->id }}" {{ $guarantee->object_id === $object->id ? 'selected' : '' }}>{{ $object->getName() }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Компания</label>
                                            <div class="position-relative mb-3">
                                                <select name="company_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($companies as $company)
                                                        <option value="{{ $company->id }}" {{ $company->id === $guarantee->company_id ? 'selected' : '' }}>{{ $company->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Валюта</label>
                                            <div class="position-relative mb-3">
                                                <select name="currency" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($currencies as $currency)
                                                        <option value="{{ $currency }}" {{ $guarantee->currency === $currency ? 'selected' : '' }}>{{ $currency }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-5 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Сумма ГУ (по договору)</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="amount-mask form-control form-control-lg form-control-solid {{ $errors->has('amount') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="amount"
                                                    value="{{ old('amount', $guarantee->amount) }}"
                                                    required
                                                />
                                            </div>
                                            @if ($errors->has('amount'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('amount')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-5 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Сумма ГУ (по факту)</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="amount-mask form-control form-control-lg form-control-solid {{ $errors->has('fact_amount') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="fact_amount"
                                                    value="{{ old('fact_amount', $guarantee->fact_amount) }}"
                                                    required
                                                />
                                            </div>
                                            @if ($errors->has('fact_amount'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('fact_amount')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">БГ по условиям договора</label>
                                            <div class="position-relative mb-3">
                                                <select name="has_bank_guarantee" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach(['Нет', 'Есть'] as $key => $value)
                                                        <option value="{{ $key }}" {{ $key === $guarantee->has_bank_guarantee ? 'selected' : '' }}>{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Итоговый акт</label>
                                            <div class="position-relative mb-3">
                                                <select name="has_final_act" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    <option value="{{ null }}" {{ is_null($guarantee->has_final_act) ? 'selected' : '' }}>Не указано</option>
                                                    @foreach(['Нет', 'Есть'] as $key => $value)
                                                        <option value="{{ $key }}" {{ $key === $guarantee->has_final_act ? 'selected' : '' }}>{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Статус</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('state') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="state"
                                                    value="{{ old('state', $guarantee->state) }}"
                                                />
                                            </div>
                                            @if ($errors->has('state'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('state')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Условия оплаты гар.удержания и комментарии</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('conditions') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="conditions"
                                                    value="{{ old('conditions', $guarantee->conditions) }}"
                                                />
                                            </div>
                                            @if ($errors->has('conditions'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('conditions')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-10 fv-row">
                                    <div class="mb-1">
                                        <label class="fs-5 fw-bold mb-2">Файлы</label>
                                        <input
                                            type="file"
                                            multiple
                                            class="form-control form-control-solid {{ $errors->has('files.*') ? 'is-invalid' : '' }}"
                                            placeholder=""
                                            name="files[]"
                                            accept=".jpg, .jpeg, .png, .pdf, .doc, .docx"
                                        />
                                        <div class="form-text">Доступные форматы:
                                            <code>jpg, jpeg, png, pdf, doc, docx</code>
                                        </div>
                                        @if ($errors->has('files.*'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                @foreach($errors->get('files.*') as $message)
                                                    <div>{{ implode(' ', $message) }}</div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-10 fv-row">
                                    <label class="form-label fw-bolder text-dark fs-6">Статус</label>
                                    <div class="position-relative mb-3">
                                        <select name="status_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                            @foreach($statuses as $statusId => $status)
                                                <option value="{{ $statusId }}" {{ $statusId === $guarantee->status_id ? 'selected' : '' }}>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-5">
                            <h3 class="fw-bolder">Оплаты</h3>

                            <div id="payment-template" class="col-md-3 mb-10 fv-row" style="display: none;">
                                <table>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <input
                                                    class="date-range-picker-single form-control form-control-lg form-control-solid"
                                                    type="text"
                                                    name="payments_date[]"
                                                    value=""
                                                    readonly
                                            />
                                        </td>
                                        <td>
                                            <input
                                                    class="amount-mask form-control form-control-lg form-control-solid"
                                                    type="text"
                                                    name="payments_amount[]"
                                                    value=""
                                                    autocomplete="off"
                                            />
                                        </td>
                                        <td>
                                            <button
                                                    type="button"
                                                    class="destroy-payment btn btn-outline btn-outline-dashed btn-outline-danger btn-active-light-danger"
                                            >
                                                Удалить
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mb-4 d-flex flex-left">
                                <button
                                        type="button"
                                        id="create-payment"
                                        class="mt-4 btn btn-outline btn-outline-dashed btn-outline-success btn-active-light-success me-2 mb-2"
                                >
                                    Добавить запись
                                </button>
                            </div>

                            <div class="d-flex flex-left mb-4">
                                <table id="payments-table" class="table align-middle table-row-dashed fs-6">
                                    <thead>
                                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                        <th class="min-w-150px">Дата оплаты</th>
                                        <th class="min-w-150px">Сумма оплаты</th>
                                        <th class="min-w-150px rounded-end pe-4">Действие</th>
                                    </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-bold">
                                    @foreach($guarantee->payments as $payment)
                                        <tr>
                                            <td>
                                                <input
                                                        class="date-range-picker-single form-control form-control-lg form-control-solid"
                                                        type="text"
                                                        name="isset_payments_date[{{ $payment->id }}]"
                                                        value="{{ $payment->date }}"
                                                        readonly
                                                />
                                            </td>
                                            <td>
                                                <input
                                                        class="amount-mask form-control form-control-lg form-control-solid"
                                                        type="text"
                                                        name="isset_payments_amount[{{ $payment->id }}]"
                                                        value="{{ $payment->amount }}"
                                                        autocomplete="off"
                                                />
                                            </td>
                                            <td>
                                                <button
                                                        type="button"
                                                        class="destroy-payment btn btn-outline btn-outline-dashed btn-outline-danger btn-active-light-danger"
                                                >
                                                    Удалить
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Сохранить</span>
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-light">Отменить</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#create-payment').on('click', function () {
                const $payment = $('#payment-template').clone();
                $('#payments-table tbody').append($payment.find('tr'));

                mainApp.init();
            });

            $(document).on('click', '.destroy-payment', function() {
                $(this).closest('tr').remove();
            });
        });
    </script>
@endpush
