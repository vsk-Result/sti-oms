<div class="modal-header">
    <h4 class="modal-title">
        @if ($copyPayment)
            Новая оплата на основе оплаты #{{ $copyPayment->id }}
        @else
            Новая оплата
        @endif
    </h4>
</div>

<div class="modal-body">
    <form class="form" action="{{ route('cash_accounts.payments.store', $cashAccount) }}" method="POST" enctype="multipart/form-data">
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
                                    value="{{ old('date', $copyPayment ? $copyPayment->date : \Carbon\Carbon::now()->format('Y-m-d')) }}"
                                    readonly
                            />
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-10 fv-row">
                    @if ($errors->has('amount'))
                        <div class="fv-plugins-message-container invalid-feedback">
                            <div>{{ implode(' ', $errors->get('amount')) }}</div>
                        </div>
                    @endif
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Объект</label>
                        <div class="position-relative mb-3">
                            <select name="object_id" data-control="select2" class="form-select form-select-solid form-select-lg" data-dropdown-parent="#createPaymentModal">
                                @foreach($objects as $objectId => $objectName)
                                    <option value="{{ $objectId }}" {{ ($copyPayment && $copyPayment->object_id === $objectId) ? 'selected' : '' }}>{{ $objectName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-10 fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Контрагент</label>
                        <div class="position-relative mb-3">
                            <select name="organization_id" data-control="select2" class="organization-select form-select form-select-solid form-select-lg" data-dropdown-parent="#createPaymentModal">
                                @if ($copyPayment)
                                    <option value="{{ $copyPayment->organization_id }}" selected>{{ \App\Models\Organization::find($copyPayment->organization_id)?->name }}</option>
                                @endif
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
                                value="{{ old('amount', $copyPayment ? $copyPayment->amount : '') }}"
                                required
                            />
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-10 fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Категория</label>
                        <div class="position-relative mb-3">
                            <select name="category" data-control="select2" class="form-select form-select-solid form-select-lg" data-dropdown-parent="#createPaymentModal">
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ ($copyPayment && $copyPayment->category === $category) ? 'selected' : '' }}>{{ $category }}</option>
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
                                class="form-control form-control-lg form-control-solid"
                                rows="3"
                                name="description"
                                required
                            >{{ old('description', $copyPayment ? $copyPayment->description : '') }}</textarea>
                        </div>
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
                            accept=".jpg, .jpeg, .png, .pdf, .doc, .docx, .xls, .xlsx"
                        />
                        <div class="form-text">Доступные форматы:
                            <code>jpg, jpeg, png, pdf, doc, docx, xls, xlsx</code>
                        </div>
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
