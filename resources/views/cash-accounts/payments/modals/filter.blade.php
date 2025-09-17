<div class="modal fade" tabindex="-1" id="cashAccountFilterPaymentModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Настройте фильтр для получения более точной информации</h4>
            </div>

            <form action="{{ request()->url() }}" method="GET">

                <input
                    type="hidden"
                    name="year"
                    value="{{ request()->input('year', '') }}"
                />

                <input
                    type="hidden"
                    name="month"
                    value="{{ request()->input('month', '') }}"
                />

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

                                <div class="mt-3">
                                    <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2025</span>
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

                                <div class="mt-3">
                                    <span class="description-or badge cursor-pointer badge-light bg-hover-secondary">%% - для условия ИЛИ</span>
                                    <span class="description-and badge cursor-pointer badge-light bg-hover-secondary">^^ - для условия И</span>
                                </div>
                            </div>

                            <div class="form-group mb-3 mt-2">
                                <div class="form-group">
                                    <label class="form-label">Условие для суммы</label>
                                    <div class="row">
                                        <div class="col-md-8">
                                            @php
                                                $expressionOperators = [
                                                    '<=' => 'Меньше или равно',
                                                    '<' => 'Меньше',
                                                    '>' => 'Больше',
                                                    '>=' => 'Больше или равно',
                                                    '=' => 'Равно',
                                                    '!=' => 'Не равно',
                                                ];
                                            @endphp
                                            <select
                                                    name="amount_expression_operator"
                                                    class="form-select form-select-solid"
                                                    data-control="select2"
                                                    data-dropdown-parent="#cashAccountFilterPaymentModal"

                                            >
                                                <option value=""></option>
                                                @foreach($expressionOperators as $operator => $expression)
                                                    <option value="{{ $operator }}" {{ $operator === request()->input('amount_expression_operator') ? 'selected' : '' }}>{{ $expression }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <input
                                                    name="amount_expression"
                                                    class="form-control form-control-solid"
                                                    value="{{ request()->input('amount_expression', '') }}"
                                                    autocomplete="off"
                                            />
                                        </div>

                                        <div class="col-md-12">
                                            <div class="mt-3">
                                                <span
                                                        class="amount-expression-quick badge badge-light cursor-pointer bg-hover-secondary"
                                                        data-operator=">"
                                                >
                                                    только приходы
                                                </span>
                                                <span
                                                        class="amount-expression-quick badge badge-light cursor-pointer bg-hover-secondary"
                                                        data-operator="<"
                                                >
                                                    только расходы
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Контрагент</label>
                                <select
                                    name="organization_id[]"
                                    class="organization-select form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#cashAccountFilterPaymentModal"
                                    multiple
                                >
                                    @foreach($activeOrganizations as $organization)
                                        <option value="{{ $organization->id }}" selected>{{ $organization->name }}</option>
                                    @endforeach
                                </select>
                            </div>



                            <div class="form-group mb-3">
                                <label class="form-label">Категория</label>
                                <select
                                        name="category[]"
                                        class="form-select form-select-solid"
                                        data-control="select2"
                                        data-dropdown-parent="#cashAccountFilterPaymentModal"

                                        multiple
                                >
                                    @foreach($categories as $categoryId => $category)
                                        <option value="{{ $categoryId }}" {{ in_array($categoryId, request()->input('category', [])) ? 'selected' : '' }}>{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Статья затрат</label>
                                <select
                                        name="code[]"
                                        class="form-select form-select-solid"
                                        data-control="select2"
                                        data-dropdown-parent="#cashAccountFilterPaymentModal"
                                        multiple
                                >
                                    <option value="null" {{ in_array("null", request()->input('code', []), true) ? 'selected' : '' }}>Не указана</option>
                                    @foreach($codes as $codeL1)
                                        <option value="{{ $codeL1['code'] }}" {{ in_array($codeL1['code'], request()->input('code', []), true) ? 'selected' : '' }}>{{ $codeL1['code'] . ' - ' . $codeL1['title'] }}</option>
                                        @if (count($codeL1['children']) > 0)
                                            @foreach($codeL1['children'] as $codeL2)
                                                <option value="{{ $codeL2['code'] }}" {{ in_array($codeL2['code'], request()->input('code', []), true) ? 'selected' : '' }}>{{ $codeL2['code'] . ' - ' . $codeL2['title'] }}</option>
                                                @if (count($codeL2['children']) > 0)
                                                    @foreach($codeL2['children'] as $codeL3)
                                                        <option value="{{ $codeL3['code'] }}" {{ in_array($codeL3['code'], request()->input('code', []), true) ? 'selected' : '' }}>{{ $codeL3['code'] . ' - ' . $codeL3['title'] }}</option>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
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
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Объект</label>
                                <select
                                    id="filter-object"
                                    name="object_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#cashAccountFilterPaymentModal"

                                    multiple
                                >
                                    @foreach($objects as $obj)
                                        <option value="{{ $obj->id }}" {{ in_array($obj->id, request()->input('object_id', [])) ? 'selected' : '' }}>{{ $obj->code . ' ' . $obj->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Вид работ</label>
                                <select
                                        name="object_worktype_id[]"
                                        class="form-select form-select-solid"
                                        data-control="select2"
                                        data-dropdown-parent="#cashAccountFilterPaymentModal"

                                        multiple
                                >
                                    @foreach($worktypes as $worktype)
                                        <option value="{{ $worktype['id'] }}" {{ in_array($worktype['id'], request()->input('object_worktype_id', [])) ? 'selected' : '' }}>{{ $worktype['code'] . ' ' . $worktype['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Статус</label>
                                <select
                                    name="status_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#cashAccountFilterPaymentModal"
                                    multiple
                                >
                                    @foreach($statuses as $statusId => $statusName)
                                        <option value="{{ $statusId }}" {{ in_array($statusId, request()->input('status_id', [])) ? 'selected' : '' }}>{{ $statusName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light float-left" data-bs-dismiss="modal">Закрыть</button>
                    <a href="{{ request()->url() }}" class="btn btn-light">Сбросить</a>
                    <button type="submit" class="btn btn-primary">Применить</button>
                </div>
            </form>
        </div>
    </div>
</div>
