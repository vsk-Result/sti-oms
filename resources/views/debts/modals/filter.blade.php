<div class="modal fade" tabindex="-1" id="filterDebtModal">
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
                                <label class="form-label">
                                    Номер счета
                                    <button type="button" class="btn btn-sm btn-icon h-20px" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="right" title="Часть фразы или фразу целиком">
                                        <i class="las la-info-circle fs-3 me-2"></i>
                                    </button>
                                </label>
                                <input name="invoice_number" class="form-control form-control-solid" value="{{ request()->input('invoice_number', '') }}" autocomplete="off" />
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Тип</label>
                                <select
                                    name="type_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterDebtModal"
                                    multiple
                                >
                                    @foreach($types as $typeId => $typeName)
                                        <option value="{{ $typeId }}" {{ in_array($typeId, request()->input('type_id', [])) ? 'selected' : '' }}>{{ $typeName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Загрузка</label>
                                <select
                                    name="import_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterDebtModal"
                                    multiple
                                >
                                    @foreach($imports as $import)
                                        <option value="{{ $import->id }}" {{ in_array($import->id, request()->input('import_id', [])) ? 'selected' : '' }}>
                                            {{ 'За ' . $import->getDateFormatted() . ' (' . $import->getType() . ')' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">
                                    Описание
                                    <button type="button" class="btn btn-sm btn-icon h-20px" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="right" title="Часть фразы или фразу целиком">
                                        <i class="las la-info-circle fs-3 me-2"></i>
                                    </button>
                                </label>
                                <input name="description" class="form-control form-control-solid" value="{{ request()->input('description', '') }}" autocomplete="off" />
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Объект</label>
                                <select
                                    id="filter-object"
                                    name="object_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterDebtModal"

                                    multiple
                                    {{ isset($object) ? 'disabled' : ''}}
                                >
                                    @foreach($objects as $obj)
                                        <option value="{{ $obj->id }}" {{ (in_array($obj->id, request()->input('object_id', [])) || (isset($object) && $obj->id === $object->id)) ? 'selected' : '' }}>{{ $obj->code . ' ' . $obj->name }}</option>
                                    @endforeach
                                </select>

                                @if (isset($object))
                                    <input type="hidden" name="object_id[]" value="{{ $object->id }}">
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Контрагент</label>
                                <select
                                    name="organization_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterDebtModal"
                                    multiple
                                >
                                    @foreach($organizations as $organization)
                                        <option value="{{ $organization->id }}" {{ in_array($organization->id, request()->input('organization_id', [])) ? 'selected' : '' }}>{{ $organization->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Категория</label>
                                <select
                                    name="category[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterDebtModal"
                                    multiple
                                >
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ in_array($category, request()->input('category', [])) ? 'selected' : '' }}>{{ $category }}</option>
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

                            <div class="form-group mb-3">
                                <label class="form-label">Вид работы</label>
                                <select
                                    name="object_worktype_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterDebtModal"
                                    multiple
                                >
                                    @foreach($workTypes as $worktype)
                                        <option value="{{ $worktype['id'] }}" {{ in_array($worktype['id'], request()->input('object_worktype_id', [])) ? 'selected' : '' }}>{{ $worktype['code'] . ' ' . $worktype['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light float-left" data-bs-dismiss="modal">Закрыть</button>
                    <a href="{{ request()->url() }}" class="btn btn-light">Сбросить</a>
                    <button id="filter-debt-submit" type="submit" class="btn btn-primary">Применить</button>
                </div>
            </form>
        </div>
    </div>
</div>
