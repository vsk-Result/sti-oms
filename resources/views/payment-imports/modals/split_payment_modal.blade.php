<div class="modal fade" tabindex="-1" id="splitPaymentModal" data-crm-avanses-imports-list-url="{{ route('crm.avanses.imports.index') }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Выберите запись для разбивки оплаты</h4>
            </div>

            <div class="modal-body">
                <select
                    id="crm-avans-import-id"
                    name="crm_avans_import_id"
                    class="form-select form-select-solid"
                    data-control="select2"
                    data-dropdown-parent="#splitPaymentModal"
                >
                </select>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Закрыть</button>
                <button id="split-payment-submit" type="button" class="btn btn-primary">Разбить оплату</button>
            </div>
        </div>
    </div>
</div>
