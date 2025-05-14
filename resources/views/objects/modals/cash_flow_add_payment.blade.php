<div class="modal fade" tabindex="-1" id="cashFlowAddPaymentModal">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('objects.cash_flow.payments.store', $object) }}" method="POST">
            @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Добавить расход</h4>
            </div>


            <div class="modal-body">

                <div class="mb-4">
                    <label class="form-label fw-bolder text-dark fs-6">Дата</label>
                    <div class="position-relative">
                        <input
                            class="date-range-picker-single form-control form-control-lg form-control-solid"
                            type="text"
                            name="date"
                            value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                            readonly
                            required
                        />
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bolder text-dark fs-6">Контрагент</label>
                    <div class="position-relative">
                        <select
                            id="organization-select"
                            name="organization_id"
                            class="form-select form-select-solid"
                            data-dropdown-parent="#cashFlowAddPaymentModal"
                        >
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bolder text-dark fs-6">Категория</label>
                    <div class="position-relative">
                        <select name="category_id" data-control="select2" data-dropdown-parent="#cashFlowAddPaymentModal" class="form-select form-select-solid form-select-lg">
                            <option value="{{ App\Models\Object\CashFlowPayment::CATEGORY_RAD }}">Работы</option>
                            @if (in_array($object->code, ['360', '361', '363']))
                                <option value="{{ App\Models\Object\CashFlowPayment::CATEGORY_MATERIAL_FIX }}">Материалы - фиксированная часть</option>
                                <option value="{{ App\Models\Object\CashFlowPayment::CATEGORY_MATERIAL_FLOAT }}">Материалы - изменяемая часть</option>
                            @else
                                <option value="{{ App\Models\Object\CashFlowPayment::CATEGORY_MATERIAL_FIX }}">Материалы</option>
                            @endif
                            <option value="{{ App\Models\Object\CashFlowPayment::CATEGORY_SERVICE }}">Накладные/Услуги</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bolder text-dark fs-6">Сумма</label>
                    <div class="position-relative">
                        <input
                            class="amount-mask form-control form-control-lg form-control-solid"
                            type="text"
                            name="amount"
                            value="0"
                            autocomplete="off"
                        />
                    </div>
                </div>
            </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Закрыть</button>
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
        </form>
    </div>
</div>
