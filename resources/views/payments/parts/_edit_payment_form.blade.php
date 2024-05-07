<div class="modal-header">
    <h4 class="modal-title">Изменение оплаты #{{ $payment->id }}</h4>
</div>

<div class="modal-body">
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
                                <select name="object_id" data-control="select2" class="form-select form-select-solid form-select-lg" data-dropdown-parent="#editPaymentModal">
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
                                <select name="company_id" data-control="select2" class="form-select form-select-solid form-select-lg" data-dropdown-parent="#editPaymentModal">
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
                                <select name="bank_id" data-control="select2" class="form-select form-select-solid form-select-lg" data-dropdown-parent="#editPaymentModal">
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
                                            class="amount-mask form-control form-control-lg form-control-solid {{ $errors->has('amount') ? 'is-invalid' : '' }}"
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
                                <select name="payment_type_id" data-control="select2" class="form-select form-select-solid form-select-lg" data-dropdown-parent="#editPaymentModal">
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
                                <select name="category" data-control="select2" class="form-select form-select-solid form-select-lg" data-dropdown-parent="#editPaymentModal">
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
                                <select name="organization_id" data-control="select2" class="form-select form-select-solid form-select-lg" data-dropdown-parent="#editPaymentModal">
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
                            <label class="form-label fw-bolder text-dark fs-6">Статья затрат</label>
                            <select
                                    name="code"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#editPaymentModal"
                            >
                                @foreach($codes as $codeL1)
                                    <option value="{{ $codeL1['code'] }}" {{ $payment->code === $codeL1['code'] ? 'selected' : '' }}>{{ $codeL1['code'] . ' - ' . $codeL1['title'] }}</option>
                                    @if (count($codeL1['children']) > 0)
                                        @foreach($codeL1['children'] as $codeL2)
                                            <option value="{{ $codeL2['code'] }}" {{ $payment->code === $codeL2['code'] ? 'selected' : '' }}>{{ $codeL2['code'] . ' - ' . $codeL2['title'] }}</option>
                                            @if (count($codeL2['children']) > 0)
                                                @foreach($codeL2['children'] as $codeL3)
                                                    <option value="{{ $codeL3['code'] }}" {{ $payment->code === $codeL3['code'] ? 'selected' : '' }}>{{ $codeL3['code'] . ' - ' . $codeL3['title'] }}</option>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            </select>
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
            <button type="button" class="btn btn-light float-left" data-bs-dismiss="modal">Закрыть</button>
        </div>
    </form>
</div>
