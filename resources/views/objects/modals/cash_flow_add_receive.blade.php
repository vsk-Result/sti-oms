<div class="modal fade" tabindex="-1" id="cashFlowAddReceiveModal">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('objects.cash_flow.receives.store', $object) }}" method="POST">
            @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Запланировать приход</h4>
            </div>

            <div class="modal-body">

                <div class="mb-4">
                    <label class="form-label fw-bolder text-dark fs-6">Дата</label>
                    <div class="position-relative">
                        <input
                            class="date-range-picker-single-receive form-control form-control-lg form-control-solid"
                            type="text"
                            name="date"
                            value="{{ Carbon\Carbon::now()->addMonthsNoOverflow(3)->endOfWeek()->addDay()->format('Y-m-d') }}"
                            readonly
                            required
                        />
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bolder text-dark fs-6">Основание</label>
                    <div class="position-relative">
                        <select name="reason_id" data-control="select2" data-dropdown-parent="#cashFlowAddReceiveModal" class="form-select form-select-solid form-select-lg">
                            <option value="{{ App\Models\Object\ReceivePlan::REASON_AVANS }}">Аванс</option>
                            <option value="{{ App\Models\Object\ReceivePlan::REASON_TARGET_AVANS }}">Целевой аванс</option>
                            <option value="{{ App\Models\Object\ReceivePlan::REASON_COMPLETED_WORKS }}">Выполненные работы</option>
                            <option value="{{ App\Models\Object\ReceivePlan::REASON_GU }}">Гарантийное удержание</option>
                            <option value="{{ App\Models\Object\ReceivePlan::REASON_OTHER }}">Прочие поступления (ген.подрядные работы и прочие)</option>
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
