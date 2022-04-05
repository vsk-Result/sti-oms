@extends('layouts.app')

@section('toolbar-title', 'Новая банковская гарантия')
@section('breadcrumbs', Breadcrumbs::render('bank_guarantees.create'))

@section('content')
    <div class="post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Новая банковская гарантия</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('bank_guarantees.store') }}?return_url={{ request()->get('return_url', '') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-5">
                            <div class="col-md-12 fv-row">

                                <div class="mb-10 fv-row">
                                    <div class="mb-1">
                                        <label class="form-label fw-bolder text-dark fs-6">Номер</label>
                                        <div class="position-relative mb-3">
                                            <input
                                                class="form-control form-control-lg form-control-solid {{ $errors->has('number') ? 'is-invalid' : '' }}"
                                                type="text"
                                                name="number"
                                                value="{{ old('number') }}"
                                                required
                                            />
                                        </div>
                                        @if ($errors->has('number'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('number')) }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Договор</label>
                                            <div class="position-relative mb-3">
                                                <select name="contract_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($contracts as $contract)
                                                        <option value="{{ $contract->id }}">{{ $contract->object->code }} | {{ $contract->getName() }}</option>

                                                        @foreach($contract->children as $subContract)
                                                            <option value="{{ $subContract->id }}">{{ $subContract->object->code }} | {{ $contract->getName() . ' | ' . $subContract->getName() }}</option>
                                                        @endforeach
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Организация</label>
                                            <div class="position-relative mb-3">
                                                <select name="organization_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($organizations as $organization)
                                                        <option value="{{ $organization->id }}">{{ $organization->name }}</option>
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
                                                        <option value="{{ $object->id }}" {{ $object->id == $objectId ? 'selected' : '' }}>{{ $object->getName() }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Обеспечение</label>
                                            <div class="position-relative mb-3">
                                                <select name="target" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($targets as $target)
                                                        <option value="{{ $target }}">{{ $target }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Компания</label>
                                            <div class="position-relative mb-3">
                                                <select name="company_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($companies as $company)
                                                        <option value="{{ $company->id }}" {{ $company->id === 1 ? 'selected' : '' }}>{{ $company->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Банк</label>
                                            <div class="position-relative mb-3">
                                                <select name="bank_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($banks as $bankId => $bankName)
                                                        <option value="{{ $bankId }}">{{ $bankName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row" data-kt-password-meter="true">
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

                                    <div class="col-md-6 mb-10 fv-row" data-kt-password-meter="true">
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

                                <div class="mb-10 fv-row">
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

                                <div class="mb-10 fv-row">
                                    <div class="mb-1">
                                        <label class="form-label fw-bolder text-dark fs-6">Комиссия</label>
                                        <div class="position-relative mb-3">
                                            <input
                                                class="form-control form-control-lg form-control-solid {{ $errors->has('commission') ? 'is-invalid' : '' }}"
                                                type="text"
                                                name="commission"
                                                value="{{ old('commission') }}"
                                                required
                                            />
                                        </div>
                                        @if ($errors->has('commission'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('commission')) }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row" data-kt-password-meter="true">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Дата начала депозита</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('start_date_deposit') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="start_date_deposit"
                                                    value="{{ old('start_date_deposit') }}"
                                                    readonly
                                                />
                                            </div>
                                            @if ($errors->has('start_date_deposit'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('start_date_deposit')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-10 fv-row" data-kt-password-meter="true">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Дата окончания депозита</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('end_date_deposit') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="end_date_deposit"
                                                    value="{{ old('end_date_deposit') }}"
                                                    readonly
                                                />
                                            </div>
                                            @if ($errors->has('end_date_deposit'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('end_date_deposit')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-10 fv-row">
                                    <div class="mb-1">
                                        <label class="form-label fw-bolder text-dark fs-6">Сумма депозита</label>
                                        <div class="position-relative mb-3">
                                            <input
                                                class="form-control form-control-lg form-control-solid {{ $errors->has('amount_deposit') ? 'is-invalid' : '' }}"
                                                type="text"
                                                name="amount_deposit"
                                                value="{{ old('amount_deposit') }}"
                                            />
                                        </div>
                                        @if ($errors->has('amount_deposit'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('amount_deposit')) }}</div>
                                            </div>
                                        @endif
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
                            </div>
                        </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Создать</span>
                            </button>
                            <a href="{{ request()->get('return_url') ?? route('bank_guarantees.index') }}" class="btn btn-light">Отменить</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
