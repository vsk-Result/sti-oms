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
                                    placeholder="Выберите период"
                                    value="{{ $filterPeriod }}"
                                    autocomplete="off"/>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Описание</label>
                                <input name="description" class="form-control form-control-solid" value="{{ $filterDescription }}" />
                            </div>

                            <div class="form-group">
                                <label class="form-label">Категория</label>
                                <select name="category" class="form-select form-select-solid" data-control="select2">
                                    <option value="all" {{ $filterCategory === 'all' ? 'selected' : '' }}>Все</option>
                                    @foreach($categories as $categoryId => $category)
                                        <option value="{{ $categoryId }}" {{ $filterCategory === $categoryId ? 'selected' : '' }}>{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Компания</label>
                                <select name="company_id" class="form-select form-select-solid" data-control="select2">
                                    <option value="all" {{ $filterCompanyId === 'all' ? 'selected' : '' }}>Все</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ $filterCompanyId === $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Организация отправитель</label>
                                <select name="organization_sender_id" class="form-select form-select-solid" data-control="select2">
                                    <option value="all" {{ $filterOrganizationSenderId === 'all' ? 'selected' : '' }}>Все</option>
                                    @foreach($organizations as $organization)
                                        <option value="{{ $organization->id }}" {{ $filterOrganizationSenderId === $organization->id ? 'selected' : '' }}>{{ $organization->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Организация получатель</label>
                                <select name="organization_receiver_id" class="form-select form-select-solid" data-control="select2">
                                    <option value="all" {{ $filterOrganizationReceiverId === 'all' ? 'selected' : '' }}>Все</option>
                                    @foreach($organizations as $organization)
                                        <option value="{{ $organization->id }}" {{ $filterOrganizationReceiverId === $organization->id ? 'selected' : '' }}>{{ $organization->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Объект</label>
                                <select name="object_id" class="form-select form-select-solid" data-control="select2">
                                    <option value="all" {{ $filterObjectId === 'all' ? 'selected' : '' }}>Все</option>
                                    @foreach($objects as $object)
                                        <option value="{{ $object->id }}" {{ $filterObjectId === $object->id ? 'selected' : '' }}>{{ $object->code . ' ' . $object->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Вид работ</label>
                                <select name="object_worktype_id" class="form-select form-select-solid" data-control="select2">
                                    <option value="all" {{ $filterObjectWorktypeId === 'all' ? 'selected' : '' }}>Все</option>
                                    @foreach($worktypes as $worktype)
                                        <option value="{{ $worktype['id'] }}" {{ $filterObjectWorktypeId === $worktype['id'] ? 'selected' : '' }}>{{ $worktype['code'] . ' ' . $worktype['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Источник</label>
                                <select name="import_type_id" class="form-select form-select-solid" data-control="select2">
                                    <option value="all" {{ $filterImportTypeId === 'all' ? 'selected' : '' }}>Все</option>
                                    @foreach($importTypes as $typeId => $type)
                                        <option value="{{ $typeId }}" {{ $filterImportTypeId === $typeId ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
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
