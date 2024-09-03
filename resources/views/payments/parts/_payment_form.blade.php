<div class="modal-header">
    <h4 class="modal-title">
        @if ($copyPayment)
            Новая оплата на основе оплаты <a  href="{{ route('payments.edit', $copyPayment) }}">#{{ $copyPayment->id }}</a>
        @else
            Новая оплата
        @endif
    </h4>
</div>

<div class="modal-body">
    <form class="form" action="{{ route('payments.store') }}" method="POST">
    @csrf

    <input type="hidden" name="return_url" value="{{ url()->previous() }}">
    <input type="hidden" name="import_id" value="{{ $copyPayment?->import_id }}">

    <div class="row mb-5">
        <div class="col-md-12 fv-row">

            <div class="row">
                <div class="col-md-4 mb-10 fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Объект</label>
                        <div class="position-relative mb-3">
                            <select name="object_id" data-control="select2" class="form-select form-select-solid form-select-lg" data-dropdown-parent="#createPaymentModal">
                                @foreach($objects as $objectId => $objectName)
                                    <option value="{{ $objectId }}" {{ ($copyPayment && $copyPayment->getObjectId() === $objectId) ? 'selected' : '' }}>{{ $objectName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-10 fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Компания</label>
                        <div class="position-relative mb-3">
                            <select name="company_id" data-control="select2" class="form-select form-select-solid form-select-lg" data-dropdown-parent="#createPaymentModal">
                                @foreach($companies as $company)
                                    @if ($copyPayment)
                                        <option value="{{ $company->id }}" {{ $copyPayment->company_id === $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                    @else
                                        <option value="{{ $company->id }}" {{ $company->id === 1 ? 'selected' : '' }}>{{ $company->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-10 fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Банк</label>
                        <div class="position-relative mb-3">
                            <select name="bank_id" data-control="select2" class="form-select form-select-solid form-select-lg" data-dropdown-parent="#createPaymentModal">
                                <option value="">Не указан</option>
                                @foreach($banks as $bankId => $bankName)
                                    <option value="{{ $bankId }}" {{ ($copyPayment && $copyPayment->bank_id === $bankId) ? 'selected' : '' }}>{{ $bankName }}</option>
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
                                value="{{ old('date', $copyPayment ? $copyPayment->date : \Carbon\Carbon::now()->format('Y-m-d')) }}"
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
                                        value="{{ old('amount', $copyPayment ? ($copyPayment->currency === 'RUB' ? $copyPayment->amount : $copyPayment->currency_amount) : '') }}"
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
                                            @php
                                                $paymentCurrency = $copyPayment ? $copyPayment->currency : '';
                                            @endphp
                                            <option value="{{ $currency }}" {{ $currency === $paymentCurrency ? 'selected' : '' }}>{{ $currency }}</option>
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
                            <select name="payment_type_id" data-control="select2" class="form-select form-select-solid form-select-lg" data-dropdown-parent="#createPaymentModal">
                                @foreach($paymentTypes as $typeId => $type)
                                    <option value="{{ $typeId }}" {{ ($copyPayment && $copyPayment->type_id === $typeId) ? 'selected' : '' }}>{{ $type }}</option>
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
                            <select name="category" data-control="select2" class="form-select form-select-solid form-select-lg" data-dropdown-parent="#createPaymentModal">
                                <option value="">Не указана</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ ($copyPayment && $copyPayment->category === $category) ? 'selected' : '' }}>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-10 fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Контрагент</label>
                        <div class="position-relative mb-3">
                            <select name="organization_id" data-control="select2" class="form-select form-select-solid form-select-lg" data-dropdown-parent="#createPaymentModal">
                                @foreach($organizations as $organization)
                                    <option value="{{ $organization->id }}" {{ ($copyPayment && $copyPayment->getOrganizationId() === $organization->id) ? 'selected' : '' }}>{{ $organization->name }}</option>
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
                                data-dropdown-parent="#createPaymentModal"
                        >
                            @foreach($codes as $codeL1)
                                <option value="{{ $codeL1['code'] }}" {{ $copyPayment && $copyPayment->code === $codeL1['code'] ? 'selected' : '' }}>{{ $codeL1['code'] . ' - ' . $codeL1['title'] }}</option>
                                @if (count($codeL1['children']) > 0)
                                    @foreach($codeL1['children'] as $codeL2)
                                        <option value="{{ $codeL2['code'] }}" {{ $copyPayment && $copyPayment->code === $codeL2['code'] ? 'selected' : '' }}>{{ $codeL2['code'] . ' - ' . $codeL2['title'] }}</option>
                                        @if (count($codeL2['children']) > 0)
                                            @foreach($codeL2['children'] as $codeL3)
                                                <option value="{{ $codeL3['code'] }}" {{ $copyPayment && $copyPayment->code === $codeL3['code'] ? 'selected' : '' }}>{{ $codeL3['code'] . ' - ' . $codeL3['title'] }}</option>
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
                                                    required
                                                >{{ old('description', $copyPayment ? $copyPayment->description : '') }}</textarea>
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
            <span class="indicator-label">Создать</span>
        </button>
        <button type="button" class="btn btn-light float-left" data-bs-dismiss="modal">Закрыть</button>
    </div>
</form>
</div>
