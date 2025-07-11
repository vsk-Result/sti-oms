<div class="modal-header">
    <h4 class="modal-title">Изменение оплаты #{{ $payment->id }}</h4>
</div>

<div class="modal-body">
    <form class="form" action="{{ route('cash_accounts.payments.update', [$cashAccount, $payment]) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row mb-5">
            <div class="col-md-12 fv-row">
                <div class="row">
                    <div class="col-md-4 mb-10 fv-row">
                        <div class="mb-1">
                            <label class="form-label fw-bolder text-dark fs-6">Дата</label>
                            <div class="position-relative mb-3">
                                <input
                                    class="date-range-picker-single form-control form-control-lg form-control-solid"
                                    type="text"
                                    name="date"
                                    value="{{ old('date', $payment->date) }}"
                                    readonly
                                />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-10 fv-row">
                        <div class="mb-1">
                            <label class="form-label fw-bolder text-dark fs-6">Объект</label>
                            <div class="position-relative mb-3">
                                <select name="object_id" data-control="select2" class="form-select form-select-solid form-select-lg" data-dropdown-parent="#editPaymentModal">
                                    @foreach($objects as $objectId => $objectName)
                                        <option value="{{ $objectId }}" {{ $payment->object_id == $objectId ? 'selected' : '' }}>{{ $objectName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-10 fv-row">
                        <div class="mb-1">
                            <label class="form-label fw-bolder text-dark fs-6">Контрагент</label>
                            <div class="position-relative mb-3">
                                <select name="organization_id" data-control="select2" class="organization-select form-select form-select-solid form-select-lg" data-dropdown-parent="#editPaymentModal">
                                    <option value="{{ $payment->organization_id }}" selected>{{ \App\Models\Organization::find($payment->organization_id)?->name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-4 mb-10 fv-row">
                        <div class="mb-1">
                            <label class="form-label fw-bolder text-dark fs-6">Сумма</label>
                            <div class="position-relative mb-3">
                                <input
                                    class="amount-mask form-control form-control-lg form-control-solid"
                                    type="text"
                                    name="amount"
                                    value="{{ old('amount', $payment->amount) }}"
                                    required
                                />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-10 fv-row">
                        <div class="mb-1">
                            <label class="form-label fw-bolder text-dark fs-6">Категория</label>
                            <div class="position-relative mb-3">
                                <select name="category" data-control="select2" class="form-select form-select-solid form-select-lg" data-dropdown-parent="#editPaymentModal">
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ $category === $payment->category ? 'selected' : '' }}>{{ $category }}</option>
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
                                    class="form-control form-control-lg form-control-solid"
                                    rows="3"
                                    name="description"
                                >{{ old('description', $payment->description) }}</textarea>
                            </div>
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
