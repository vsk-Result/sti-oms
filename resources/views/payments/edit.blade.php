@extends('layouts.app')

@section('toolbar-title', 'Изменение оплаты')
@section('breadcrumbs', Breadcrumbs::render('payments.edit', $payment))

@section('content')
    <div class="post">
        <div class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Изменение оплаты</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('payments.update', $payment) }}" method="POST">
                        @csrf

                        <input type="hidden" name="return_url" value="{{ url()->previous() }}">

                        <div class="row mb-5">
                            <div class="col-md-12 fv-row">

                                <div class="row">
                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Объект</label>
                                            <div class="position-relative mb-3">
                                                <select name="object_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($objects as $objectId => $objectName)
                                                        <option value="{{ $objectId }}" {{ $payment->getObjectId() == $objectId ? 'selected' : '' }}>{{ $objectName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Компания</label>
                                            <div class="position-relative mb-3">
                                                <select name="company_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($companies as $company)
                                                        <option value="{{ $company->id }}" {{ $company->id === $payment->company_id ? 'selected' : '' }}>{{ $company->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Банк</label>
                                            <div class="position-relative mb-3">
                                                <select name="bank_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    <option value="" {{ is_null($payment->bank_id) ? 'selected' : '' }}>Не указан</option>
                                                    @foreach($banks as $bankId => $bankName)
                                                        <option value="{{ $bankId }}" {{ $bankId === $payment->bank_id ? 'selected' : '' }}>{{ $bankName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Дата</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('date') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="date"
                                                    value="{{ old('date', $payment->date) }}"
                                                    readonly
                                                />
                                            </div>
                                            @if ($errors->has('date'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('date')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="mb-1">
                                                    <label class="form-label fw-bolder text-dark fs-6">Сумма</label>
                                                    <div class="position-relative mb-3">
                                                        <input
                                                            class="form-control form-control-lg form-control-solid {{ $errors->has('amount') ? 'is-invalid' : '' }}"
                                                            type="text"
                                                            name="amount"
                                                            value="{{ old('amount', $payment->currency === 'RUB' ? $payment->amount : $payment->currency_amount) }}"
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
                                            <div class="col-md-4">
                                                <div class="mb-1">
                                                    <label class="form-label fw-bolder text-dark fs-6">Валюта</label>
                                                    <div class="position-relative mb-3">
                                                        <select name="currency" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                            @foreach($currencies as $currency)
                                                                <option value="{{ $currency }}" {{ $currency === $payment->currency ? 'selected' : '' }}>{{ $currency }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Тип</label>
                                            <div class="position-relative mb-3">
                                                <select name="payment_type_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($paymentTypes as $typeId => $type)
                                                        <option value="{{ $typeId }}" {{ $typeId === $payment->payment_type_id ? 'selected' : '' }}>{{ $type }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Категория</label>
                                            <div class="position-relative mb-3">
                                                <select name="category" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    <option value="" {{ is_null($payment->category) ? 'selected' : '' }}>Не указана</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category }}" {{ $category === $payment->category ? 'selected' : '' }}>{{ $category }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Контрагент</label>
                                            <div class="position-relative mb-3">
                                                <select name="organization_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @php
                                                        if ($payment->amount < 0) {
                                                            $paymentOrganizationId = $payment->organization_receiver_id;
                                                        } else {
                                                            $paymentOrganizationId = $payment->organization_sender_id;
                                                        }
                                                    @endphp
                                                    @foreach($organizations as $organization)
                                                        <option value="{{ $organization->id }}" {{ $organization->id === $paymentOrganizationId ? 'selected' : '' }}>{{ $organization->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Кост код</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('code') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="code"
                                                    value="{{ old('code', $payment->code) }}"
                                                />
                                            </div>
                                            @if ($errors->has('code'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('code')) }}</div>
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
                                                    rows="3"
                                                    name="description"
                                                >{{ old('description', $payment->description) }}</textarea>
                                            </div>
                                            @if ($errors->has('description'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('description')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
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
