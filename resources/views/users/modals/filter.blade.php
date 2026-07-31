<div class="modal fade" tabindex="-1" id="filterUsersModal">
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
                                <label class="form-label">Статус</label>
                                <select
                                    name="status_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterUsersModal"
                                    multiple
                                >
                                    @foreach($statuses as $statusId => $statusName)
                                        <option value="{{ $statusId }}" {{ in_array($statusId, request()->input('status_id', [])) ? 'selected' : '' }}>
                                            {{ $statusName }}
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
