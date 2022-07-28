<div class="modal fade" tabindex="-1" id="filterPaymentImportModal">
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
                                <label class="form-label">Период</label>
                                <input
                                    name="period"
                                    class="form-control form-control-solid date-range-picker"
                                    value="{{ request()->input('period', '') }}"
                                    autocomplete="off"
                                />
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Банк</label>
                                <select
                                    name="bank_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterPaymentImportModal"

                                    multiple
                                >
                                    @foreach($banks as $bankId => $bank)
                                        <option value="{{ $bankId }}" {{ in_array($bankId, request()->input('bank_id', [])) ? 'selected' : '' }}>{{ $bank }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Валюта</label>
                                <select
                                    name="currency[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterPaymentImportModal"

                                    multiple
                                >
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency }}" {{ in_array($currency, request()->input('currency', [])) ? 'selected' : '' }}>{{ $currency }}</option>
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
                                    data-dropdown-parent="#filterPaymentImportModal"

                                    multiple
                                >
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ in_array($company->id, request()->input('company_id', [])) ? 'selected' : '' }}>{{ $company->name }}</option>
                                    @endforeach
                                </select>
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

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Тип</label>
                                <select
                                    name="type_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterPaymentImportModal"
                                    multiple
                                >
                                    @foreach($types as $typeId => $type)
                                        <option value="{{ $typeId }}" {{ in_array($typeId, request()->input('type_id', [])) ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Статус</label>
                                <select
                                    name="status_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterPaymentImportModal"
                                    multiple
                                >
                                    @foreach($statuses as $statusId => $status)
                                        <option value="{{ $statusId }}" {{ in_array($statusId, request()->input('status_id', [])) ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light float-left" data-bs-dismiss="modal">Закрыть</button>
                    <a href="{{ request()->url() }}" class="btn btn-light">Сбросить</a>
                    <button id="filter-payment-import-submit" type="submit" class="btn btn-primary">Применить</button>
                </div>
            </form>
        </div>
    </div>
</div>
