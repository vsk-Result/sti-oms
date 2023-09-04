@extends('layouts.app')

@section('toolbar-title', 'Изменение депозита')
@section('breadcrumbs', Breadcrumbs::render('deposits.edit', $deposit))

@section('content')
    <div class="post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Изменение депозита</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('deposits.update', $deposit) }}?return_url={{ request()->get('return_url', '') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-5">
                            <div class="col-md-12 fv-row">

                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Договор</label>
                                            <div class="position-relative mb-3">
                                                <select name="contract_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    <option value="null" {{ $deposit->contract_id === null ? 'selected' : '' }}>Отсутствует</option>
                                                    @foreach($contracts as $contract)
                                                        <option value="{{ $contract->id }}" {{ $contract->id === $deposit->contract_id ? 'selected' : '' }}>{{ $contract->object->code }} | {{ $contract->getName() }}</option>

                                                        @foreach($contract->children as $subContract)
                                                            <option value="{{ $subContract->id }}" {{ $subContract->id === $deposit->contract_id ? 'selected' : '' }}>{{ $subContract->object->code }} | {{ $contract->getName() . ' | ' . $subContract->getName() }}</option>
                                                        @endforeach
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Контрагент</label>
                                            <div class="position-relative mb-3">
                                                <select name="organization_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    <option value="null" {{ $deposit->organization_id === null ? 'selected' : '' }}>Отсутствует</option>
                                                    @foreach($organizations as $organization)
                                                        <option value="{{ $organization->id }}" {{ $organization->id === $deposit->organization_id ? 'selected' : '' }}>{{ $organization->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Объект</label>
                                            <div class="position-relative mb-3">
                                                <select name="object_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($objects as $object)
                                                        <option value="{{ $object->id }}" {{ $deposit->object_id === $object->id ? 'selected' : '' }}>{{ $object->getName() }}</option>
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
                                                        <option value="{{ $company->id }}" {{ $company->id === $deposit->company_id ? 'selected' : '' }}>{{ $company->name }}</option>
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
                                                        <option value="{{ $bankId }}" {{ $bankId === $deposit->bank_id ? 'selected' : '' }}>{{ $bankName }}</option>
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
                                                    value="{{ old('start_date', $deposit->start_date) }}"
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
                                                    value="{{ old('end_date', $deposit->end_date) }}"
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
                                                        <option value="{{ $currency }}" {{ $deposit->currency === $currency ? 'selected' : '' }}>{{ $currency }}</option>
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
                                                    value="{{ old('amount', $deposit->amount) }}"
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
                            </div>
                        </div>

                        <div class="mb-10 fv-row">
                            <label class="form-label fw-bolder text-dark fs-6">Статус</label>
                            <div class="position-relative mb-3">
                                <select name="status_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                    @foreach($statuses as $statusId => $status)
                                        <option value="{{ $statusId }}" {{ $statusId === $deposit->status_id ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
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

                        <div class="d-flex flex-center py-3">
                            <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Сохранить</span>
                            </button>
                            <a href="{{ request()->get('return_url') ?? route('deposits.index') }}" class="btn btn-light">Отменить</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
