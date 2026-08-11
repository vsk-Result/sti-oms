<div class="modal fade" tabindex="-1" id="objectAddResponsibleUserModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Заполнить данные</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label class="form-label">Пользователь</label>
                            <select
                                id="responsible-user-select"
                                name="details_type"
                                class="form-select form-select-solid"
                                data-control="select2"
                                data-dropdown-parent="#objectAddResponsibleUserModal"
                            >
                                @foreach($users as $user)
                                    <option value="{{ $user->name . '::' . $user->email . '::' . $user->phone }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer justify-content-between">
                <button class="btn btn-light-primary me-3" id="responsible-user-choose">Выбрать</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
