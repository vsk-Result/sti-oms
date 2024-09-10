<div class="modal fade" tabindex="-1" id="filterActCategoryModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Настройте фильтр для получения более точной информации</h4>
            </div>

            <form action="{{ request()->url() }}" method="GET">

                <input name="filter" type="hidden" value="{{ request()->get('filter', 'current') }}" />

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Объект</label>
                                <select
                                        name="object_id[]"
                                        class="form-select form-select-solid"
                                        data-control="select2"
                                        data-dropdown-parent="#filterActCategoryModal"
                                        multiple
                                >
                                    @foreach($objects as $obj)
                                        <option value="{{ $obj->id }}" {{ (in_array($obj->id, request()->input('object_id', [])) || (isset($object) && $obj->id === $object->id)) ? 'selected' : '' }}>{{ $obj->code . ' ' . $obj->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light float-left" data-bs-dismiss="modal">Закрыть</button>
                    <a href="{{ route('pivots.acts_category.index') }}" class="btn btn-light">Сбросить</a>
                    <button id="filter-taxPlan-submit" type="submit" class="btn btn-primary">Применить</button>
                </div>
            </form>
        </div>
    </div>
</div>
