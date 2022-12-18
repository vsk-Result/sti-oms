<div class="modal fade" tabindex="-1" id="debtCreateManualModal">
    <div class="modal-dialog">
        <form action="{{ route('debts.manual.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Добавьте новый долг</h4>
                </div>

                <input type="hidden" name="debt_manual_object_id" class="form-control" value="{{ $object->id }}" />

                <div class="modal-body">
                    <div class="form group mb-4">
                        <label class="form-label">Контрагент</label>
                        <select
                                id="organization-select"
                                name="debt_manual_organization_id"
                                class="form-select form-select-solid"
                                multiple
                        ></select>
                    </div>

                    <div class="form group mb-4">
                        <label class="form-label">Тип контрагента</label>
                        <select
                                name="debt_manual_organization_type_id"
                                class="form-select form-select-solid"
                        >
                            <option value="{{ \App\Models\Debt\Debt::TYPE_CONTRACTOR }}">Подрядчик</option>
                            <option value="{{ \App\Models\Debt\Debt::TYPE_PROVIDER }}">Поставщик</option>
                        </select>
                    </div>

                    <div class="form group">
                        <label class="form-label">Сумма долга</label>
                        <input type="text" name="debt_manual_amount" class="form-control" />
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
