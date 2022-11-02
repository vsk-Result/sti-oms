@extends('layouts.app')

@section('toolbar-title', 'Изменение договора')
@section('breadcrumbs', Breadcrumbs::render('contracts.edit', $contract))

@section('content')
    <div class="post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Изменение договора {{ $contract->getName() }}</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('contracts.update', $contract) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="return_url" value="{{ url()->previous() }}">

                        <div class="row mb-5">
                            <div class="col-md-12 fv-row">

                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Компания</label>
                                            <div class="position-relative mb-3">
                                                <select name="company_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($companies as $company)
                                                        <option value="{{ $company->id }}" {{ $company->id === $contract->company_id ? 'selected' : '' }}>{{ $company->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Объект</label>
                                            <div class="position-relative mb-3">
                                                <select name="object_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($objects as $object)
                                                        <option value="{{ $object->id }}" {{ $object->id == $contract->object_id ? 'selected' : '' }}>{{ $object->getName() }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Тип договора</label>
                                            <div class="position-relative mb-3">
                                                <select name="type_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($types as $typeId => $typeName)
                                                        <option value="{{ $typeId }}" {{ $typeId === $contract->type_id ? 'selected' : '' }}>{{ $typeName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-1 mt-10 main-contract-container" style="{{ $contract->isMain() ? 'display:none;' : '' }}">
                                            <label class="form-label fw-bolder text-dark fs-6">Основной договор</label>
                                            <div class="position-relative mb-3">
                                                <select name="parent_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($mainContracts as $contractId => $contractName)
                                                        <option value="{{ $contractId }}" {{ $contractId === $contract->parent_id ? 'selected' : '' }}>{{ $contractName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Номер договора</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="name"
                                                    value="{{ old('name', $contract->name) }}"
                                                    required
                                                />
                                            </div>
                                            @if ($errors->has('name'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('name')) }}</div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mb-1 mt-10 main-contract-container" style="{{ $contract->isMain() ? 'display:none;' : '' }}">
                                            <label class="form-label fw-bolder text-dark fs-6">Тип суммы</label>
                                            <div class="position-relative mb-3">
                                                <select name="amount_type_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($amountTypes as $typeId => $typeName)
                                                        <option value="{{ $typeId }}" {{ $typeId === $contract->amount_type_id ? 'selected' : '' }}>{{ $typeName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Дата начала</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('start_date') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="start_date"
                                                    value="{{ old('start_date', $contract->start_date) }}"
                                                    readonly
                                                />
                                            </div>
                                            @if ($errors->has('start_date'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('start_date')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Дата окончания</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('end_date') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="end_date"
                                                    value="{{ old('end_date', $contract->end_date) }}"
                                                    readonly
                                                />
                                            </div>
                                            @if ($errors->has('end_date'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('end_date')) }}</div>
                                                </div>
                                            @endif
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
                                                        <option value="{{ $currency }}" {{ $contract->currency === $currency ? 'selected' : '' }}>{{ $currency }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-10 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Сумма</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('amount') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="amount"
                                                    value="{{ old('amount', $contract->amount) }}"
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
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Описание</label>
                                            <div class="position-relative mb-3">
                                                <textarea
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('description') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="description"
                                                >{{ old('description', $contract->description) }}</textarea>
                                            </div>
                                            @if ($errors->has('description'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('description')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Файлы</label>
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
                                </div>

                                <div class="mb-10 fv-row">
                                    <label class="form-label fw-bolder text-dark fs-6">Статус</label>
                                    <div class="position-relative mb-3">
                                        <select name="status_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                            @foreach($statuses as $statusId => $status)
                                                <option value="{{ $statusId }}" {{ $statusId === $contract->status_id ? 'selected' : '' }}>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-check form-check-custom form-check-solid mb-6 fw-bold fs-6 container-check" data-target="#avanses-container">
                            <input class="form-check-input" type="checkbox" value="" id="showAvansesCheckbox" {{ $contract->avanses->count() > 0 ? 'checked' : '' }}/>
                            <label class="form-check-label" for="showAvansesCheckbox">
                                Добавить авансы
                            </label>
                        </div>

                        <div class="form-check form-check-custom form-check-solid fw-bold fs-6 container-check" data-target="#avanses-received-container">
                            <input class="form-check-input" type="checkbox" value="" id="showAvansesReceivedCheckbox" {{ $contract->avansesReceived->count() > 0 ? 'checked' : '' }}/>
                            <label class="form-check-label" for="showAvansesReceivedCheckbox">
                                Добавить полученные авансы
                            </label>
                        </div>

                        <div id="avanses-container" class="row mb-5 mt-10" style="{{ $contract->avanses->count() === 0 ? 'display: none;' : '' }}">
                            <h3 class="fw-bolder">Авансы по договору</h3>

                            <div id="avans-template" class="col-md-3 mb-10 fv-row" style="display: none;">
                                <table>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <input
                                                class="form-control form-control-lg form-control-solid"
                                                type="text"
                                                name="avanses[]"
                                                value=""
                                                autocomplete="off"
                                            />
                                        </td>
                                        <td>
                                            <input
                                                class="date-range-picker-single form-control form-control-lg form-control-solid"
                                                type="text"
                                                name="avanses_planned_payment_date[]"
                                                value=""
                                                readonly
                                            />
                                        </td>
                                        <td>
                                            <button
                                                type="button"
                                                class="destroy-avans btn btn-outline btn-outline-dashed btn-outline-danger btn-active-light-danger"
                                            >
                                                Удалить
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex flex-left mb-4">
                                <button
                                    type="button"
                                    id="create-avans"
                                    class="mt-4 btn btn-outline btn-outline-dashed btn-outline-success btn-active-light-success me-2 mb-2"
                                >
                                    Добавить запись
                                </button>
                            </div>

                            <div class="d-flex flex-left mb-4">
                                <div class="col-md-6">
                                    <table id="avanses-table" class="table align-middle table-row-dashed fs-6">
                                        <thead>
                                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                            <th class="min-w-150px">Сумма аванса</th>
                                            <th class="min-w-150px">Планируемая дата оплаты</th>
                                            <th class="min-w-150px rounded-end pe-4">Действие</th>
                                        </tr>
                                        </thead>
                                        <tbody class="text-gray-600 fw-bold">
                                            @foreach($contract->avanses as $avans)
                                                <tr>
                                                    <td>
                                                        <input
                                                            class="form-control form-control-lg form-control-solid"
                                                            type="text"
                                                            name="isset_avanses[{{ $avans->id }}]"
                                                            value="{{ $avans->amount }}"
                                                            autocomplete="off"
                                                        />
                                                    </td>
                                                    <td>
                                                        <input
                                                            class="date-range-picker-single form-control form-control-lg form-control-solid"
                                                            type="text"
                                                            name="isset_avanses_planned_payment_date[{{ $avans->id }}]"
                                                            value="{{ $avans->planned_payment_date }}"
                                                            readonly
                                                        />
                                                    </td>
                                                    <td>
                                                        <button
                                                            type="button"
                                                            class="destroy-avans btn btn-outline btn-outline-dashed btn-outline-danger btn-active-light-danger"
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
                        </div>

                        <div id="avanses-received-container" class="row mb-5 mt-10" style="{{ $contract->avansesReceived->count() === 0 ? 'display: none;' : '' }}">
                            <h3 class="fw-bolder">Полученные авансы</h3>

                            <div id="received-avans-template" class="col-md-3 mb-10 fv-row" style="display: none;">
                                <table>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <input
                                                class="date-range-picker-single form-control form-control-lg form-control-solid"
                                                type="text"
                                                name="received_avanses_date[]"
                                                value=""
                                                readonly
                                            />
                                        </td>
                                        <td>
                                            <input
                                                class="form-control form-control-lg form-control-solid"
                                                type="text"
                                                name="received_avanses_amount[]"
                                                value=""
                                                autocomplete="off"
                                            />
                                        </td>
                                        <td>
                                            <input
                                                class="form-control form-control-lg form-control-solid"
                                                type="text"
                                                name="received_avanses_description[]"
                                                value=""
                                                autocomplete="off"
                                            />
                                        </td>
                                        <td>
                                            <button
                                                type="button"
                                                class="destroy-avans btn btn-outline btn-outline-dashed btn-outline-danger btn-active-light-danger"
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
                                    id="create-received-avans"
                                    class="mt-4 btn btn-outline btn-outline-dashed btn-outline-success btn-active-light-success me-2 mb-2"
                                >
                                    Добавить запись
                                </button>
                            </div>

                            <div class="d-flex flex-left mb-4">
                                <table id="avanses-received-table" class="table align-middle table-row-dashed fs-6">
                                    <thead>
                                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                        <th class="min-w-150px">Дата аванса</th>
                                        <th class="min-w-150px">Сумма аванса</th>
                                        <th class="min-w-150px">Описание</th>
                                        <th class="min-w-150px rounded-end pe-4">Действие</th>
                                    </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-bold">
                                        @foreach($contract->avansesReceived as $avans)
                                            <tr>
                                                <td>
                                                    <input
                                                        class="date-range-picker-single form-control form-control-lg form-control-solid"
                                                        type="text"
                                                        name="isset_received_avanses_date[{{ $avans->id }}]"
                                                        value="{{ $avans->date }}"
                                                        readonly
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        class="form-control form-control-lg form-control-solid"
                                                        type="text"
                                                        name="isset_received_avanses_amount[{{ $avans->id }}]"
                                                        value="{{ $avans->amount }}"
                                                        autocomplete="off"
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        class="form-control form-control-lg form-control-solid"
                                                        type="text"
                                                        name="isset_received_avanses_description[{{ $avans->id }}]"
                                                        value="{{ $avans->description }}"
                                                        autocomplete="off"
                                                    />
                                                </td>
                                                <td>
                                                    <button
                                                        type="button"
                                                        class="destroy-avans btn btn-outline btn-outline-dashed btn-outline-danger btn-active-light-danger"
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

                        <h3 class="fw-bolder mb-5">Банковская гарантия</h3>

                        @foreach ($contract->bankGuarantees as $bankGuarantee)
                            <a target="_blank" class="mb-5 fs-6" href="{{ route('bank_guarantees.edit', $bankGuarantee) }}">{{ $bankGuarantee->number }}</a>
                            <br/>
                        @endforeach

                        <a target="_blank" class="mt-5 btn btn-outline btn-outline-dashed btn-outline-success btn-active-light-success me-2 mb-2" href="{{ route('bank_guarantees.create') }}?current_contract_id={{ $contract->id }}&current_object_id={{ $contract->object_id }}">Добавить банковскую гарантию</a>

                        <h3 class="fw-bolder mb-5 mt-7">Гарантийное удержание</h3>

                        @foreach ($contract->guarantees as $guarantee)
                            <a target="_blank" class="mb-5 fs-6" href="{{ route('guarantees.edit', $guarantee) }}">{{ 'Гарантийное удержание #' . $guarantee->id }}</a>
                            <br/>
                        @endforeach

                        <a target="_blank" class="mt-5 btn btn-outline btn-outline-dashed btn-outline-success btn-active-light-success me-2 mb-2" href="{{ route('guarantees.create') }}?current_contract_id={{ $contract->id }}&current_object_id={{ $contract->object_id }}">Добавить гарантийное удержание</a>

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
            $('select[name=type_id]').on('change', function() {
                if ($(this).val() != 0) {
                    $('.main-contract-container').show();
                } else {
                    $('.main-contract-container').each(function() {
                        $(this).css('display', 'none');
                    })
                }
            });

            $('.container-check input').on('click', function () {
                const checked = $(this).prop('checked');
                $($(this).parent().data('target')).toggle(checked);
            });

            $('#create-avans').on('click', function () {
                const $avans = $('#avans-template').clone();
                $('#avanses-table tbody').append($avans.find('tr'));

                mainApp.init();
            });

            $(document).on('click', '.destroy-avans', function() {
                $(this).closest('tr').remove();
            });

            $('#create-received-avans').on('click', function () {
                const $avans = $('#received-avans-template').clone();
                $('#avanses-received-table tbody').append($avans.find('tr'));

                mainApp.init();
            });
        });
    </script>
@endpush
