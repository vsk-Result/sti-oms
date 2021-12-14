<div class="modal fade" tabindex="-1" id="filterPaymentModal" data-crm-avanses-imports-list-url="{{ route('crm.avanses.imports.index') }}">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Настройте фильтр для получения более точной информации</h4>
            </div>

            <form action="{{ route('payments.index') }}" method="GET">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Период</label>
                                <input
                                    name="period"
                                    class="form-control form-control-solid date-range-picker"
                                    value="{{ request()->input('period', '') }}"
                                    autocomplete="off"/>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Описание</label>
                                <input name="description" class="form-control form-control-solid" value="{{ request()->input('description', '') }}" />
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Категория</label>
                                <select
                                    name="category[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterPaymentModal"
                                    multiple
                                >
                                    @foreach($categories as $categoryId => $category)
                                        <option value="{{ $categoryId }}" {{ in_array($categoryId, request()->input('category', [])) ? 'selected' : '' }}>{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Банк</label>
                                <select
                                    name="bank_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterPaymentModal"
                                    multiple
                                >
                                    @foreach($banks as $bankId => $bank)
                                        <option value="{{ $bankId }}" {{ in_array($bankId, request()->input('bank_id', [])) ? 'selected' : '' }}>{{ $bank }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Компания</label>
                                <select
                                    name="company_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterPaymentModal"
                                    multiple
                                >
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ in_array($company->id, request()->input('company_id', [])) ? 'selected' : '' }}>{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Отправитель</label>
                                <select
                                    name="organization_sender_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterPaymentModal"
                                    multiple
                                >
                                    @foreach($organizations as $organization)
                                        <option value="{{ $organization->id }}" {{ in_array($organization->id, request()->input('organization_sender_id', [])) ? 'selected' : '' }}>{{ $organization->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Получатель</label>
                                <select
                                    name="organization_receiver_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterPaymentModal"
                                    multiple
                                >
                                    @foreach($organizations as $organization)
                                        <option value="{{ $organization->id }}" {{ in_array($organization->id, request()->input('organization_receiver_id', [])) ? 'selected' : '' }}>{{ $organization->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Условие для суммы</label>
                                <input
                                    name="amount_expression"
                                    class="form-control form-control-solid"
                                    value="{{ request()->input('amount_expression', '') }}"
                                />
                                <p class="text-muted mt-2">
                                    Операторы:
                                    <code><=</code>
                                    <code><</code>
                                    <code>>=</code>
                                    <code>></code>
                                    <code>!=</code>
                                    <code>=</code>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Объект</label>
                                <select
                                    name="object_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterPaymentModal"
                                    multiple
                                >
                                    @foreach($objects as $object)
                                        <option value="{{ $object->id }}" {{ in_array($object->id, request()->input('object_id', [])) ? 'selected' : '' }}>{{ $object->code . ' ' . $object->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Вид работ</label>
                                <select
                                    name="object_worktype_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterPaymentModal"
                                    multiple
                                >
                                    @foreach($worktypes as $worktype)
                                        <option value="{{ $worktype['id'] }}" {{ in_array($worktype['id'], request()->input('object_worktype_id', [])) ? 'selected' : '' }}>{{ $worktype['code'] . ' ' . $worktype['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Источник</label>
                                <select
                                    name="import_type_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterPaymentModal"
                                    multiple
                                >
                                    @foreach($importTypes as $typeId => $type)
                                        <option value="{{ $typeId }}" {{ in_array($typeId, request()->input('import_type_id', [])) ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Количество записей на странице</label>
                                <input
                                    name="count_per_page"
                                    class="form-control form-control-solid"
                                    value="{{ request()->input('count_per_page', '30') }}"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light float-left" data-bs-dismiss="modal">Закрыть</button>
                    <a href="{{ route('payments.index') }}" class="btn btn-light">Сбросить</a>
                    <button id="filter-payment-submit" type="submit" class="btn btn-primary">Применить</button>
                </div>
            </form>
        </div>
    </div>
</div>
