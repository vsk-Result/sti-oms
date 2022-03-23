<div class="modal fade" tabindex="-1" id="filterContractModal">
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
                                    Номер
                                    <button type="button" class="btn btn-sm btn-icon h-20px" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="right" title="Часть фразы или фразу целиком">
                                        <i class="las la-info-circle fs-3 me-2"></i>
                                    </button>
                                </label>
                                <input name="name" class="form-control form-control-solid" value="{{ request()->input('name', '') }}" />
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
                                    data-dropdown-parent="#filterContractModal"

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
                        </div>
                        <div class="col-md-4">
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
                    <button id="filter-contract-submit" type="submit" class="btn btn-primary">Применить</button>
                </div>
            </form>
        </div>
    </div>
</div>
