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
                                            <label class="form-label fw-bolder text-dark fs-6">Номер</label>
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
                                    <div class="col-md-12 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Сумма</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('amount') ? 'is-invalid' : '' }}"
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
                            <h3 class="fw-bolder mb-8">Авансы</h3>

                            <div id="avans-template" class="col-md-3 mb-10 fv-row" style="display: none;">
                                <div class="mb-1">
                                    <label class="form-label fw-bolder text-dark fs-6">Сумма</label>
                                    <div class="position-relative mb-3">
                                        <input
                                            class="form-control form-control-lg form-control-solid"
                                            type="text"
                                            name="avanses[]"
                                            value=""
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 mb-10 fv-row">
                                <button type="button" id="create-avans" class="mt-9 btn btn-outline btn-outline-dashed btn-outline-success btn-active-light-success me-2 mb-2">Добавить</button>
                            </div>
                        </div>

                        <div class="row mb-5">
                            <h3 class="fw-bolder mb-8">Полученные авансы</h3>

                            <div id="received-avans-template" class="col-md-3 mb-10 fv-row" style="display: none;">
                                <div class="mb-1">
                                    <label class="form-label fw-bolder text-dark fs-6">Дата</label>
                                    <div class="position-relative mb-3">
                                        <input
                                            class="date-range-picker-single form-control form-control-lg form-control-solid"
                                            type="text"
                                            name="received_avanses_date[]"
                                            value=""
                                            readonly
                                        />
                                    </div>
                                </div>

                                <div class="mb-1 mt-10">
                                    <label class="form-label fw-bolder text-dark fs-6">Сумма</label>
                                    <div class="position-relative mb-3">
                                        <input
                                            class="form-control form-control-lg form-control-solid"
                                            type="text"
                                            name="received_avanses_amount[]"
                                            value=""
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 mb-10 fv-row">
                                <button type="button" id="create-received-avans" class="mt-9 btn btn-outline btn-outline-dashed btn-outline-success btn-active-light-success me-2 mb-2">Добавить</button>
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
                $avans.attr('id', '');
                $avans.show();
                $(this).parent().before($avans);
            });

            $('#create-received-avans').on('click', function () {
                const $avans = $('#received-avans-template').clone();
                $avans.attr('id', '');
                $avans.show();
                $(this).parent().before($avans);

                mainApp.init();
            });
        });
    </script>
@endpush
