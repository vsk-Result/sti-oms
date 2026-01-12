<div class="modal-header">
    <h4 class="modal-title">
        @if ($copyPayment)
            Новая запись на основе #{{ $copyPayment->id }}
        @else
            Новая запись
        @endif
    </h4>
</div>

<div class="modal-body">
    <form class="form" action="{{ route('cash_accounts.payments.store', $cashAccount) }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row mb-5">
        <div class="col-md-12 fv-row">

            <div class="row">
                <div class="col-md-4 mb-10 fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Дата</label>
                        <div class="position-relative mb-3">
                            <input
                                    class="date-range-picker-single form-control form-control-lg form-control-solid"
                                    type="text"
                                    name="date"
                                    value="{{ old('date', $copyPayment ? $copyPayment->date : \Carbon\Carbon::now()->format('Y-m-d')) }}"
                                    readonly
                            />
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-10 fv-row">
                    @if ($errors->has('amount'))
                        <div class="fv-plugins-message-container invalid-feedback">
                            <div>{{ implode(' ', $errors->get('amount')) }}</div>
                        </div>
                    @endif
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Объект</label>
                        <div class="position-relative mb-3">
                            <select name="object_id" data-control="select2" class="form-select form-select-solid form-select-lg" data-dropdown-parent="#createPaymentModal">
                                <option value="{{ null }}" {{ ($copyPayment && $copyPayment->isTransferObject()) ? 'selected' : '' }}>Трансфер</option>
                                @foreach($objects as $objectId => $objectName)
                                    <option value="{{ $objectId }}" {{ ($copyPayment && $copyPayment->getObjectId() === $objectId) ? 'selected' : '' }}>{{ $objectName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-10 fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Статья затрат</label>
                        <select
                                id="create-code"
                                name="code"
                                class="form-select form-select-solid"
                                data-control="select2"
                                data-dropdown-parent="#createPaymentModal"
                        >
                            <optgroup label="Часть используемые">
                                @foreach($popularCodes as $code => $codeName)
                                    <option value="{{ $code }}" {{ $copyPayment && $copyPayment->code === $code ? 'selected' : '' }}>{{ $codeName }}</option>
                                @endforeach
                            </optgroup>

                            <optgroup label="Все статьи">
                                @foreach($codes as $codeL1)
                                    <option
                                        value="{{ $codeL1['code'] }}" {{ $copyPayment && $copyPayment->code === $codeL1['code'] ? 'selected' : '' }}
                                        {{ in_array($codeL1['code'], $availableCodes) ? '' : 'disabled' }}
                                    >
                                        {{ $codeL1['code'] . ' - ' . $codeL1['title'] }}
                                    </option>
                                    @if (count($codeL1['children']) > 0)
                                        @foreach($codeL1['children'] as $codeL2)
                                            <option
                                                value="{{ $codeL2['code'] }}" {{ $copyPayment && $copyPayment->code === $codeL2['code'] ? 'selected' : '' }}
                                                {{ in_array($codeL2['code'], $availableCodes) ? '' : 'disabled' }}
                                            >
                                                {{ $codeL2['code'] . ' - ' . $codeL2['title'] }}
                                            </option>
                                            @if (count($codeL2['children']) > 0)
                                                @foreach($codeL2['children'] as $codeL3)
                                                    <option
                                                        value="{{ $codeL3['code'] }}" {{ $copyPayment && $copyPayment->code === $codeL3['code'] ? 'selected' : '' }}
                                                        {{ in_array($codeL3['code'], $availableCodes) ? '' : 'disabled' }}
                                                    >
                                                        {{ $codeL3['code'] . ' - ' . $codeL3['title'] }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-10 fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Сумма</label>
                        <div class="position-relative mb-3">
                            <input
                                class="amount-mask form-control form-control-lg form-control-solid"
                                type="text"
                                name="amount"
                                value="{{ old('amount', $copyPayment ? abs($copyPayment->amount) : '') }}"
                                required
                            />
                        </div>

                        <label class="form-check form-check-custom form-check-solid form-check-inline gap-2 justify-content-end">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="is_receive_amount"
                                {{ $copyPayment && $copyPayment->amount > 0 ? 'checked' : '' }}
                            />

                            Приход
                        </label>
                    </div>
                </div>

                <div class="col-md-4 mb-10 fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Категория</label>
                        <div class="position-relative mb-3">
                            <select id="create-category" name="category" data-control="select2" class="form-select form-select-solid form-select-lg" data-dropdown-parent="#createPaymentModal">
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ ($copyPayment && $copyPayment->category === $category) ? 'selected' : '' }}>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div id="create-organization" class="col-md-4 mb-10 fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Контрагент</label>
                        <div class="position-relative mb-3">
                            <select name="organization_id" required data-control="select2" class="organization-select form-select form-select-solid form-select-lg" data-dropdown-parent="#createPaymentModal">
                                @if ($copyPayment)
                                    <option value="{{ $copyPayment->organization_id }}" selected>{{ \App\Models\Organization::find($copyPayment->organization_id)?->name }}</option>
                                @endif
                            </select>

                            <a target="_blank" href="{{ route('organizations.create') }}">Создать нового</a>
                        </div>
                    </div>
                </div>
            </div>

            <div id="create-employee-crm" class="row border-dashed border-warning p-3 mb-4" style="margin-top: -24px; display: none">
                <div class="col-md-4 fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Рабочий в CRM</label>
                        <div class="position-relative mb-3">
                            <select name="crm_employee_id" data-control="select2" class="form-select form-select-solid form-select-lg" data-dropdown-parent="#createPaymentModal">
                                @foreach($crmEmployees as $employeeId => $employeeName)
                                    <option value="{{ $employeeId }}" {{ ($copyPayment && $copyPayment->getCrmAvansData()['id'] == $employeeId) ? 'selected' : '' }}>{{ $employeeName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Месяц вычета</label>
                        <div class="position-relative mb-3">
                            @php
                                $months = [];
                                foreach ([date('Y'), '2025'] as $year) {
                                    foreach (['Декабрь', 'Ноябрь', 'Октябрь', 'Сентябрь', 'Август', 'Июль', 'Июнь', 'Май', 'Апрель', 'Март', 'Февраль', 'Январь'] as $m) {
                                        $months[] = $m . ' ' . $year;
                                    }
                                }
                            @endphp

                            <select name="crm_date" data-control="select2" class="form-select form-select-solid form-select-lg">
                                @foreach($months as $month)
                                    <option value="{{ $month }}" {{ ($copyPayment && translate_year_month($copyPayment->getCrmAvansData()['date']) === $month) ? 'selected' : '' }}>{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 fv-row">
                    <div class="mb-1" style="padding-top: 38px;">
                        <label class="form-check form-check-custom form-check-solid form-check-inline gap-2">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="crm_not_need_avans"
                                {{ $copyPayment && $copyPayment->getCrmAvansData()['crm_not_need_avans'] ? 'checked' : '' }}
                            />

                            Не создавать аванс в CRM
                        </label>
                    </div>
                </div>
            </div>

            <div id="create-itr" class="row border-dashed border-warning p-3 mb-4" style="margin-top: -24px; display: none">
                <div class="col-md-4 fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">ИТР из 1С</label>
                        <div class="position-relative mb-3">
                            <select name="itr_id" data-control="select2" class="form-select form-select-solid form-select-lg" data-dropdown-parent="#createPaymentModal">
                                @foreach($itr as $itrData)
                                    <option value="{{ $itrData['Id'] }}" {{ ($copyPayment && $copyPayment->getItrData()['id'] == $itrData['Id']) ? 'selected' : '' }}>{{ $itrData['Name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 fv-row">
                    <div class="mb-1" style="padding-top: 38px;">
                        <label class="form-check form-check-custom form-check-solid form-check-inline gap-2">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="1c_itr_not_need_create"
                                {{ $copyPayment && is_null($copyPayment->getItrData()['id']) ? 'checked' : '' }}
                            />

                            Запись относится к списку ИТР
                        </label>
                    </div>
                </div>
            </div>

            <div id="create-apartment" class="row border-dashed border-warning p-3 mb-4" style="margin-top: -24px; display: none">
                <div class="col-md-3 fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Квартира</label>
                        <div class="position-relative mb-3">
                            <select name="crm_apartment_id" data-control="select2" class="form-select form-select-solid form-select-lg" data-dropdown-parent="#createPaymentModal">
                                @foreach($crmApartments as $apartmentId => $apartmentName)
                                    <option value="{{ $apartmentId }}" {{ ($copyPayment && $copyPayment->getCrmApartmentData()['apartment_id'] == $apartmentId) ? 'selected' : '' }}>{{ $apartmentName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Дата оплаты</label>
                        <div class="position-relative mb-3">
                            <input
                                class="date-range-picker-single form-control form-control-lg form-control-solid"
                                type="text"
                                name="crm_payment_date"
                                value="{{ $copyPayment && $copyPayment->getCrmApartmentData()['payment_date'] ? $copyPayment->getCrmApartmentData()['payment_date'] : '' }}"
                                readonly
                            />
                        </div>
                    </div>
                </div>

                <div class="col-md-3 fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Месяц оплаты</label>
                        <div class="position-relative mb-3">
                            @php
                                $months = [];
                                foreach ([date('Y'), '2025'] as $year) {
                                    foreach (['Декабрь', 'Ноябрь', 'Октябрь', 'Сентябрь', 'Август', 'Июль', 'Июнь', 'Май', 'Апрель', 'Март', 'Февраль', 'Январь'] as $m) {
                                        $months[] = $m . ' ' . $year;
                                    }
                                }
                            @endphp

                            <select name="crm_payment_month" data-control="select2" class="form-select form-select-solid form-select-lg">
                                @foreach($months as $month)
                                    <option value="{{ $month }}" {{ ($copyPayment && translate_year_month($copyPayment->getCrmApartmentData()['payment_month']) === $month) ? 'selected' : '' }}>{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Сумма оплаты</label>
                        <div class="position-relative mb-3">
                            <input
                                class="amount-mask form-control form-control-lg form-control-solid"
                                type="text"
                                name="crm_payment_amount"
                                value="{{ $copyPayment && $copyPayment->getCrmApartmentData()['payment_amount'] ? $copyPayment->getCrmApartmentData()['payment_amount'] : '' }}"
                            />
                        </div>
                    </div>
                </div>

                <div class="col-md-3 fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Сумма коммуналки</label>
                        <div class="position-relative mb-3">
                            <input
                                class="amount-mask form-control form-control-lg form-control-solid"
                                type="text"
                                name="crm_payment_communal"
                                value="{{ $copyPayment && $copyPayment->getCrmApartmentData()['payment_communal'] ? $copyPayment->getCrmApartmentData()['payment_communal'] : '' }}"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-10 fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Описание</label>
                        <div class="position-relative mb-3">
                            <textarea
                                class="form-control form-control-lg form-control-solid"
                                rows="3"
                                name="description"
                                required
                            >{{ old('description', $copyPayment ? $copyPayment->description : '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-10 fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Файлы</label>
                        <input
                            type="file"
                            multiple
                            class="form-control form-control-solid {{ $errors->has('files.*') ? 'is-invalid' : '' }}"
                            placeholder=""
                            name="files[]"
                            accept=".jpg, .jpeg, .png, .pdf, .doc, .docx, .xls, .xlsx"
                        />
                        <div class="form-text">Доступные форматы:
                            <code>jpg, jpeg, png, pdf, doc, docx, xls, xlsx</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-center py-3">
        <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary me-3">
            <span class="indicator-label">Создать</span>
        </button>
        <button type="button" class="btn btn-light float-left" data-bs-dismiss="modal">Закрыть</button>
    </div>
</form>
</div>
