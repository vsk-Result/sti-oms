<div class="modal fade" tabindex="-1" id="filterLoanModal">
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
                                <label class="form-label">Тип</label>
                                <select
                                    name="type_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterLoanModal"
                                    multiple
                                >
                                    @foreach($types as $typeId => $typeName)
                                        <option value="{{ $typeId }}" {{ in_array($typeId, request()->input('type_id', [])) ? 'selected' : '' }}>
                                            {{ $typeName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Организация</label>
                                <select
                                    name="organization_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterLoanModal"
                                    multiple
                                >
                                    @foreach($organizations as $organization)
                                        <option value="{{ $organization->id }}" {{ in_array($organization->id, request()->input('organization_id', [])) ? 'selected' : '' }}>
                                            {{ $organization->name }}
                                        </option>
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
                                    data-dropdown-parent="#filterLoanModal"
                                    multiple
                                >
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ in_array($company->id, request()->input('company_id', [])) ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
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
                                <label class="form-label">Банк</label>
                                <select
                                    name="bank_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterLoanModal"
                                    multiple
                                >
                                    @foreach($banks as $bankId => $bankName)
                                        <option value="{{ $bankId }}" {{ in_array($bankId, request()->input('bank_id', [])) ? 'selected' : '' }}>
                                            {{ $bankName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light float-left" data-bs-dismiss="modal">Закрыть</button>
                    <a href="{{ request()->url() }}" class="btn btn-light">Сбросить</a>
                    <button id="filter-loan-submit" type="submit" class="btn btn-primary">Применить</button>
                </div>
            </form>
        </div>
    </div>
</div>
