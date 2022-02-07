<div class="modal-header">
    <h4 class="modal-title">
        @if ($copyPayment)
            Новая оплата на основе оплаты <a target="_blank" href="{{ route('payments.edit', $copyPayment) }}">#{{ $copyPayment->id }}</a>
        @else
            Новая оплата
        @endif
    </h4>
</div>

<div class="modal-body">
    <form class="form" action="{{ route('payments.store') }}" method="POST">
    @csrf

    <input type="hidden" name="return_url" value="{{ url()->previous() }}">

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
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Сумма</label>
                        <div class="position-relative mb-3">
                            <input
                                class="form-control form-control-lg form-control-solid {{ $errors->has('amount') ? 'is-invalid' : '' }}"
                                type="text"
                                name="amount"
                                value="{{ old('amount', $copyPayment ? $copyPayment->amount : '') }}"
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
                        <label class="form-label fw-bolder text-dark fs-6">Организация</label>
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
                        <label class="form-label fw-bolder text-dark fs-6">Кост код</label>
                        <div class="position-relative mb-3">
                            <input
                                class="form-control form-control-lg form-control-solid {{ $errors->has('code') ? 'is-invalid' : '' }}"
                                type="text"
                                name="code"
                                value="{{ old('code', $copyPayment ? $copyPayment->code : '') }}"
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
