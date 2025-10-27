<div class="modal fade" tabindex="-1" id="filterCalculateWorkersCostModal">
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
                                <label class="form-label">Год</label>
                                <select
                                    name="year"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterCalculateWorkersCostModal"
                                    data-allow-clear="true"
                                >
                                    @foreach($filterYears as $year)
                                        <option value="{{ $year }}" {{ $year === request()->get('year', date('Y')) ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
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
                                    data-dropdown-parent="#filterCalculateWorkersCostModal"
                                    multiple
                                >
                                    @foreach($objects as $obj)
                                        <option value="{{ $obj->id }}" {{ in_array($obj->id, request()->input('object_id', [])) ? 'selected' : '' }}>{{ $obj->getName() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light float-left" data-bs-dismiss="modal">Закрыть</button>
                    <a href="{{ route('pivots.calculate_workers_cost.index') }}" class="btn btn-light">Сбросить</a>
                    <button id="filter-taxPlan-submit" type="submit" class="btn btn-primary">Применить</button>
                </div>
            </form>
        </div>
    </div>
</div>
