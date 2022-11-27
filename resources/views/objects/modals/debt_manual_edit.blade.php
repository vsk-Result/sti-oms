<div class="modal fade" tabindex="-1" id="debtManualModal">
    <div class="modal-dialog">
        <form action="{{ route('debts.manual.update') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Укажите сумму долга вручную для</h4>
                </div>

                <div class="modal-body">
                    <div class="form group">
                        <label class="form-label">Сумма долга</label>
                        <input type="text" name="debt_manual_amount" id="debt-manual-amount" class="form-control" />
                        <input type="hidden" name="debt_manual_id" id="debt-manual-id" class="form-control" />
                        <input type="hidden" name="debt_manual_type_id" id="debt-manual-type-id" class="form-control" />
                        <input type="hidden" name="debt_manual_object_id" id="debt-manual-object-id" class="form-control" />
                        <input type="hidden" name="debt_manual_object_worktype_id" id="debt-manual-object-worktype-id" class="form-control" />
                        <input type="hidden" name="debt_manual_organization_id" id="debt-manual-organization-id" class="form-control" />
                        <span id="debt-manual-comment" class="text-muted fs-7"></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </div>
        </form>
    </div>
</div>
