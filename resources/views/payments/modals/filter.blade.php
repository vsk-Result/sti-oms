<div class="modal fade" tabindex="-1" id="filterPaymentModal">
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

                            <div class="form-group mb-3">
                                <label class="form-label">Категория</label>
                                <select
                                    name="category[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterPaymentModal"

                                    multiple
                                >
                                    @foreach($categories as $categoryId => $category)
                                        <option value="{{ $categoryId }}" {{ in_array($categoryId, request()->input('category', [])) ? 'selected' : '' }}>{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
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
                                                data-dropdown-parent="#filterPaymentModal"

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

                            <div class="form-group mb-3">
                                <label class="form-label">Статья затрат</label>
                                <select
                                    name="code[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterPaymentModal"
                                    multiple
                                >
                                    @foreach($codes as $code => $codeName)
                                        <option value="{{ $code }}" {{ in_array($code, request()->input('code', []), true) ? 'selected' : '' }}>{{ $codeName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Компания</label>
                                <select
                                    name="company_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterPaymentModal"

                                    multiple
                                >
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ in_array($company->id, request()->input('company_id', [])) ? 'selected' : '' }}>{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Организация</label>
                                <select
                                    id="organization-select"
                                    name="organization_id[]"
                                    class="form-select form-select-solid"
                                    multiple
                                >
                                    @foreach($activeOrganizations as $organization)
                                        <option value="{{ $organization->id }}" selected>{{ $organization->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Банк</label>
                                <select
                                    name="bank_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterPaymentModal"

                                    multiple
                                >
                                    @foreach($banks as $bankId => $bank)
                                        <option value="{{ $bankId }}" {{ in_array($bankId, request()->input('bank_id', [])) ? 'selected' : '' }}>{{ $bank }}</option>
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

                            @if (auth()->user()->hasRole('super-admin'))
                                <div class="form-group mb-3">
                                    <label class="form-label">Цвет шрифта</label>
                                    <select
                                        name="parameter_font_color[]"
                                        class="form-select form-select-solid"
                                        data-control="select2"
                                        data-dropdown-parent="#filterPaymentModal"
                                        multiple
                                    >
                                        <option value="transfer_font_color::#60bd60" {{ in_array('transfer_font_color::#60bd60', request()->input('parameter_font_color', [])) ? 'selected' : '' }}>Зеленый</option>
                                        <option value="transfer_font_color::#ff0000" {{ in_array('transfer_font_color::#ff0000', request()->input('parameter_font_color', [])) ? 'selected' : '' }}>Красный</option>
                                    </select>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">Объект</label>
                                <select
                                    id="filter-object"
                                    name="object_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterPaymentModal"

                                    multiple
                                    {{ isset($object) ? 'disabled' : ''}}
                                >

                                    <option value="Общее" {{ (in_array('Общее', request()->input('object_id', [])) || (isset($object) && 'Общее' === $object)) ? 'selected' : '' }}>Общее</option>
                                    <option value="Трансфер" {{ (in_array('Трансфер', request()->input('object_id', [])) || (isset($object) && 'Трансфер' === $object)) ? 'selected' : '' }}>Трансфер</option>

                                    @foreach($objects as $obj)
                                        <option value="{{ $obj->id }}" {{ (in_array($obj->id, request()->input('object_id', [])) || (isset($object) && $obj->id === $object->id)) ? 'selected' : '' }}>{{ $obj->code . ' ' . $obj->name }}</option>
                                    @endforeach
                                </select>

                                @if (isset($object))
                                    <input type="hidden" name="object_id[]" value="{{ $object->id }}">
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Вид работ</label>
                                <select
                                    name="object_worktype_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterPaymentModal"

                                    multiple
                                >
                                    @foreach($worktypes as $worktype)
                                        <option value="{{ $worktype['id'] }}" {{ in_array($worktype['id'], request()->input('object_worktype_id', [])) ? 'selected' : '' }}>{{ $worktype['code'] . ' ' . $worktype['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Источник</label>
                                <select
                                    name="import_type_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterPaymentModal"
                                    multiple
                                >
                                    @foreach($importTypes as $typeId => $type)
                                        <option value="{{ $typeId }}" {{ in_array($typeId, request()->input('import_type_id', [])) ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Тип</label>
                                <select
                                    name="payment_type_id[]"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterPaymentModal"
                                    multiple
                                    {{ isset($pType) ? 'disabled' : ''}}
                                >
                                    @foreach($paymentTypes as $typeId => $type)
                                        <option value="{{ $typeId }}" {{ (in_array($typeId, request()->input('payment_type_id', [])) || (isset($pType) && $typeId === $pType)) ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>

                                @if (isset($pType))
                                    <input type="hidden" name="payment_type_id[]" value="{{ $pType }}">
                                @endif
                            </div>

                            @if (auth()->user()->hasRole('super-admin'))
                                <div class="form-group mb-3">
                                    <label class="form-label">Цвет фона</label>
                                    <select
                                        name="parameter_background_color[]"
                                        class="form-select form-select-solid"
                                        data-control="select2"
                                        data-dropdown-parent="#filterPaymentModal"
                                        multiple
                                    >
                                        <option value="transfer_background_color::#d7ffb7" {{ in_array('transfer_background_color::#d7ffb7', request()->input('parameter_background_color', [])) ? 'selected' : '' }}>Зеленый</option>
                                        <option value="transfer_background_color::#fdfd6b" {{ in_array('transfer_background_color::#fdfd6b', request()->input('parameter_background_color', [])) ? 'selected' : '' }}>Желтый</option>
                                    </select>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light float-left" data-bs-dismiss="modal">Закрыть</button>
                    <a href="{{ request()->url() }}" class="btn btn-light">Сбросить</a>
                    <button id="filter-payment-submit" type="submit" class="btn btn-primary">Применить</button>
                </div>
            </form>
        </div>
    </div>
</div>
