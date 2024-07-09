<div class="modal fade" tabindex="-1" id="filterGeneralCostsModal">
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
                                <label class="form-label">Год</label>
                                <select
                                    name="year[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterGeneralCostsModal"
                                    multiple
                                >
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ in_array($year, request()->input('year', [])) ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Объект</label>
                                <select
                                        id="filter-object"
                                        name="object_id[]"
                                        class="form-select form-select-solid"
                                        data-control="select2"
                                        data-dropdown-parent="#filterGeneralCostsModal"
                                        multiple
                                >
                                    @foreach($objects as $obj)
                                        <option value="{{ $obj->id }}" {{ in_array($obj->id, request()->input('object_id', [])) ? 'selected' : '' }}>{{ $obj->code . ' ' . $obj->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Группировать закрытые объекты</label>
                                <select
                                    name="group_closed_objects[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterGeneralCostsModal"
                                >
                                    <option value="true" {{ request()->input('group_closed_objects', true)[0] == 'true' ? 'selected' : '' }}>Да</option>
                                    <option value="false" {{ request()->input('group_closed_objects', true)[0] == 'false' ? 'selected' : '' }}>Нет</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light float-left" data-bs-dismiss="modal">Закрыть</button>
                    <a href="{{ request()->url() }}" class="btn btn-light">Сбросить</a>
                    <button id="filter-deposit-submit" type="submit" class="btn btn-primary">Применить</button>
                </div>
            </form>
        </div>
    </div>
</div>
