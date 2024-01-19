<div class="modal fade" tabindex="-1" id="filterTaxPlanModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Настройте фильтр для получения более точной информации</h4>
            </div>

            <form action="{{ request()->url() }}" method="GET">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Наименование</label>
                                <input name="name" class="form-control form-control-solid" value="{{ request()->input('name', '') }}" autocomplete="off" />
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Период</label>
                                <input name="period" class="form-control form-control-solid" value="{{ request()->input('period', '') }}" autocomplete="off" />
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Срок оплаты</label>
                                <input
                                    name="due_date"
                                    class="form-control form-control-solid date-range-picker"
                                    value="{{ request()->input('due_date', '') }}"
                                    autocomplete="off"
                                />
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Платежка в 1С</label>
                                <select
                                    name="in_one_c[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterTaxPlanModal"
                                    multiple
                                >
                                    @foreach(['Нет', 'Да'] as $index => $value)
                                        <option value="{{ $index }}" {{ in_array($index, request()->input('in_one_c', [])) ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Статус</label>
                                <select
                                        name="paid[]"
                                        class="form-select form-select-solid"
                                        data-control="select2"
                                        data-dropdown-parent="#filterTaxPlanModal"
                                        multiple
                                >
                                    @foreach(['Не оплачено', 'Оплачено'] as $index => $value)
                                        <option value="{{ $index }}" {{ in_array($index, request()->input('paid', [])) ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Дата оплаты</label>
                                <input
                                    name="payment_date"
                                    class="form-control form-control-solid date-range-picker"
                                    value="{{ request()->input('payment_date', '') }}"
                                    autocomplete="off"
                                />
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Количество записей на странице</label>
                                <input
                                    name="count_per_page"
                                    class="form-control form-control-solid"
                                    value="{{ request()->input('count_per_page', '30') }}"
                                    autocomplete="off"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light float-left" data-bs-dismiss="modal">Закрыть</button>
                    <a href="{{ request()->url() }}" class="btn btn-light">Сбросить</a>
                    <button id="filter-taxPlan-submit" type="submit" class="btn btn-primary">Применить</button>
                </div>
            </form>
        </div>
    </div>
</div>
