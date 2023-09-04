<div class="modal fade" tabindex="-1" id="filterDepositsHistoryModal">
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
                                <label class="form-label">Период</label>
                                <input
                                    name="period"
                                    class="form-control form-control-solid date-range-picker"
                                    value="{{ request()->input('period', '') }}"
                                    autocomplete="off"/>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Пользователь</label>
                                <select
                                    name="user[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterDepositsHistoryModal"
                                    data-allow-clear="true"
                                    multiple
                                >
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ in_array($user->id, request()->input('user', [])) ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Объект истории</label>
                                <select
                                    id="filter-object-history"
                                    name="auditable[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterDepositsHistoryModal"
                                    data-allow-clear="true"
                                    multiple
                                    {{ isset($auditable) ? 'disabled' : ''}}
                                >
                                    @foreach($auditables as $aud => $audName)
                                        <option value="{{ $aud }}" {{ (in_array($aud, request()->input('auditable', [])) || (isset($auditable) && $aud === $auditable)) ? 'selected' : '' }}>{{ $audName }}</option>
                                    @endforeach
                                </select>

                                @if (isset($auditable))
                                    <input type="hidden" name="auditable[]" value="{{ $auditable }}">
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Поле</label>
                                <select
                                    name="list_fields[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterDepositsHistoryModal"
                                    data-allow-clear="true"
                                    multiple
                                >
                                    @foreach($fields as $fieldName => $fieldLocaleName)
                                        <option value="{{ $fieldName }}" {{ in_array($fieldName, request()->input('list_fields', [])) ? 'selected' : '' }}>{{ $fieldLocaleName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Тип события</label>
                                <select
                                    name="event[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterDepositsHistoryModal"
                                    data-allow-clear="true"
                                    multiple
                                >
                                    @foreach($events as $eventName => $eventLocaleName)
                                        <option value="{{ $eventName }}" {{ in_array($eventName, request()->input('event', [])) ? 'selected' : '' }}>{{ $eventLocaleName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Количество записей на странице</label>
                                <input
                                    name="count_per_page"
                                    class="form-control form-control-solid"
                                    value="{{ request()->input('count_per_page', '15') }}"
                                    autocomplete="off"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light float-left" data-bs-dismiss="modal">Закрыть</button>
                    <a href="{{ request()->url() }}" class="btn btn-light">Сбросить</a>
                    <button id="filter-deposit-history-submit" type="submit" class="btn btn-primary">Применить</button>
                </div>
            </form>
        </div>
    </div>
</div>
