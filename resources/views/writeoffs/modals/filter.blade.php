<div class="modal fade" tabindex="-1" id="filterWriteoffModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Настройте фильтр для получения более точной информации</h4>
            </div>

            <form action="{{ request()->url() }}" method="GET">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-5">
                                <label class="form-label">Период</label>
                                <input
                                        name="period"
                                        class="form-control form-control-solid date-range-picker"
                                        value="{{ request()->input('period', '') }}"
                                        autocomplete="off"/>

                                <div class="mt-3">
                                    <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2017</span>
                                    <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2018</span>
                                    <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2019</span>
                                    <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2020</span>
                                    <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2021</span>
                                    <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2022</span>
                                    <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2023</span>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">
                                    Описание
                                    <button type="button" class="btn btn-sm btn-icon h-20px" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="right" title="Часть фразы или фразу целиком">
                                        <i class="las la-info-circle fs-3 me-2"></i>
                                    </button>
                                </label>
                                <input name="description" class="form-control form-control-solid" value="{{ request()->input('description', '') }}" />
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
                                        data-dropdown-parent="#filterWriteoffModal"

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
                    <a href="{{ isset($object) ? (request()->url() . '?object_id%5B%5D=' . $object->id) : request()->url() }}" class="btn btn-light">Сбросить</a>
                    <button id="filter-writeoff-submit" type="submit" class="btn btn-primary">Применить</button>
                </div>
            </form>
        </div>
    </div>
</div>
