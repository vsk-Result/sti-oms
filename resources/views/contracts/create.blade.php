@extends('layouts.app')

@section('toolbar-title', 'Новый договор')
@section('breadcrumbs', Breadcrumbs::render('contracts.create'))

@section('content')
    <div class="post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Новый договор</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('contracts.store') }}" method="POST" enctype="multipart/form-data">
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
                                                        <option value="{{ $company->id }}" {{ $company->short_name === 'СТИ' ? 'selected' : '' }}>{{ $company->name }}</option>
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
                                                        <option value="{{ $object->id }}" {{ $object->id == $objectId ? 'selected' : '' }}>{{ $object->getName() }}</option>
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
                                                        <option value="{{ $typeId }}">{{ $typeName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-1 mt-10 main-contract-container" style="display:none;">
                                            <label class="form-label fw-bolder text-dark fs-6">Основной договор</label>
                                            <div class="position-relative mb-3">
                                                <select name="parent_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($mainContracts as $contractId => $contractName)
                                                        <option value="{{ $contractId }}">{{ $contractName }}</option>
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
                                                    value="{{ old('name') }}"
                                                    required
                                                />
                                            </div>
                                            @if ($errors->has('name'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('name')) }}</div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mb-1 mt-10 main-contract-container" style="display:none;">
                                            <label class="form-label fw-bolder text-dark fs-6">Тип суммы</label>
                                            <div class="position-relative mb-3">
                                                <select name="amount_type_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($amountTypes as $typeId => $typeName)
                                                        <option value="{{ $typeId }}">{{ $typeName }}</option>
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
                                                    value="{{ old('start_date') }}"
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
                                                    value="{{ old('end_date') }}"
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
                                                        <option value="{{ $currency }}">{{ $currency }}</option>
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
                                                    class="amount-formatted form-control form-control-lg form-control-solid {{ $errors->has('amount') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="amount"
                                                    value="{{ old('amount') }}"
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
                                                >{{ old('description') }}</textarea>
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
                            </div>
                        </div>

                        <div class="row mb-5">
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
                                            <th class="min-w-150px rounded-end pe-4">Действие</th>
                                        </tr>
                                        </thead>
                                        <tbody class="text-gray-600 fw-bold">
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
                            </div>
                        </div>

                        <div class="row mb-5">
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
                        </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Создать</span>
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

            $('#create-avans').on('click', function () {
                const $avans = $('#avans-template').clone();
                $('#avanses-table tbody').append($avans.find('tr'));
            });

            $(document).on('click', '.destroy-avans', function() {
                $(this).closest('tr').remove();
            });

            $('#create-received-avans').on('click', function () {
                const $avans = $('#received-avans-template').clone();
                $('#avanses-received-table tbody').append($avans.find('tr'));

                mainApp.init();
            });

            // $('.amount-formatted').on('input', function() {
            //     let value = $(this).val().replace(/[^-.,0-9]/, '').replace(',', '.');
            //     const currency = $('[name=currency]').val();
            //     const formatter = new Intl.NumberFormat('en-US', {
            //         style: 'currency',
            //         currency: currency,
            //
            //         // These options are needed to round to whole numbers if that's what you want.
            //         minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
            //         maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
            //     });
            //
            //     $(this).val(formatter.format(value));
            // });
        });
    </script>
@endpush
