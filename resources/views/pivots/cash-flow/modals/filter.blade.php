<div class="modal fade" tabindex="-1" id="filterCashFlowModal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Настройте фильтр для получения более точной информации</h4>
            </div>

            <form action="{{ request()->url() }}" method="GET">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Период</label>
                                <input
                                    name="period"
                                    class="form-control form-control-solid date-range-picker"
                                    value="{{ request()->input('period', $period) }}"
                                    autocomplete="off" />
                            </div>
                        </div>

                        <div class="col-md-12 mb-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Вид отчета</label>
                                <select
                                    name="view_name"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterCashFlowModal"
                                    data-allow-clear="true"
                                >
                                    <option value="view_one" {{ $viewName !== 'view_two' ? 'selected' : '' }}>Расходы в объектах</option>
                                    <option value="view_two" {{ $viewName === 'view_two' ? 'selected' : '' }}>Расходы под общими затратами</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="form-label">Объект</label>
                                <select
                                    id="filter-object"
                                    name="object_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterCashFlowModal"
                                    multiple
                                >
                                    <option value="{{ $object27_1->id }}" {{ in_array($object27_1->id, request()->input('object_id', [])) ? 'selected' : '' }}>{{ $object27_1->code . ' ' . $object27_1->name }}</option>
                                    @foreach($objects as $obj)
                                        <option value="{{ $obj->id }}" {{ in_array($obj->id, request()->input('object_id', [])) ? 'selected' : '' }}>{{ $obj->code . ' ' . $obj->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light float-left" data-bs-dismiss="modal">Закрыть</button>
                    <a href="{{ route('pivots.cash_flow.index') }}" class="btn btn-light">Сбросить</a>
                    <button id="filter-taxPlan-submit" type="submit" class="btn btn-primary">Применить</button>
                </div>
            </form>
        </div>
    </div>
</div>
