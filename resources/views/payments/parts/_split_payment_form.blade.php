<div class="modal-header">
    <h4 class="modal-title">Разбивка оплаты #{{ $payment->id }}</h4>
</div>

<div class="modal-body">
    <form class="form" action="{{ route('payments.split_by_category.store', $payment) }}" method="POST">
        @csrf

        <input type="hidden" name="return_url" value="{{ url()->previous() }}">

        <div class="row mb-5">
            <div class="col-md-12 fv-row">

                <div class="row">
                    <div class="col-md-4 mb-10 fv-row">
                        <div class="mb-1">
                            <label class="form-label fw-bolder text-dark fs-6">Объект</label>
                            <div class="position-relative mb-3">
                                {{ $payment->getObjectName() }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-10 fv-row">
                        <div class="mb-1">
                            <label class="form-label fw-bolder text-dark fs-6">Компания</label>
                            <div class="position-relative mb-3">
                                {{ $payment->company->name }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-10 fv-row">
                        <div class="mb-1">
                            <label class="form-label fw-bolder text-dark fs-6">Банк</label>
                            <div class="position-relative mb-3">
                                {{ $payment->getBankName() }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-10 fv-row">
                        <div class="mb-1">
                            <label class="form-label fw-bolder text-dark fs-6">Дата</label>
                            <div class="position-relative mb-3">
                                {{ $payment->getDateFormatted() }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-10 fv-row">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-1">
                                    <label class="form-label fw-bolder text-dark fs-6">Сумма</label>
                                    <div class="position-relative mb-3">
                                        <span data-amount="{{ $payment->amount }}" class="split-payment-amount {{ $payment->amount >= 0 ? 'text-success' : 'text-danger' }}"> {{ \App\Models\CurrencyExchangeRate::format($payment->amount, $payment->currency, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-10 fv-row">
                        <div class="mb-1">
                            <label class="form-label fw-bolder text-dark fs-6">Тип</label>
                            <div class="position-relative mb-3">
                                {{ $payment->getPaymentType() }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-10 fv-row">
                        <div class="mb-1">
                            <label class="form-label fw-bolder text-dark fs-6">Категория</label>
                            <div class="position-relative mb-3">
                                {{ $payment->category }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-10 fv-row">
                        <div class="mb-1">
                            <label class="form-label fw-bolder text-dark fs-6">Контрагент</label>
                            <div class="position-relative mb-3">
                                @php
                                    if ($payment->amount < 0) {
                                        $paymentOrganization = $payment->organizationReceiver?->name ?? '';
                                    } else {
                                        $paymentOrganization = $payment->organizationSender?->name ?? '';
                                    }
                                @endphp

                                {{ $paymentOrganization }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-10 fv-row">
                        <div class="mb-1">
                            <label class="form-label fw-bolder text-dark fs-6">Статья затрат</label>
                            <div class="position-relative mb-3">
                                {{ $payment->code }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-10 fv-row">
                        <div class="mb-1">
                            <label class="form-label fw-bolder text-dark fs-6">Описание</label>
                            <div class="position-relative mb-3">
                                {{ $payment->description }}
                            </div>
                            @if ($errors->has('description'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div>{{ implode(' ', $errors->get('description')) }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <table class="table align-middle table-row-dashed fs-6">
                    <thead>
                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                        <th class="min-w-150px">Категория</th>
                        <th class="min-w-150px">Сумма оплаты</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        <tr>
                            <td class="ps-2">
                                {{ \App\Models\Payment::CATEGORY_RAD }}
                            </td>
                            <td class="pe-2">
                                <input
                                    class="split-amount-change split-amount-rad amount-mask form-control form-control form-control-solid"
                                    type="text"
                                    name="split_amount_rad"
                                    value=""
                                    autocomplete="off"
                                />
                            </td>
                        </tr>

                        <tr>
                            <td class="ps-2">
                                {{ \App\Models\Payment::CATEGORY_MATERIAL }}
                            </td>
                            <td class="pe-2">
                                <input
                                    class="split-amount-change split-amount-material amount-mask form-control form-control form-control-solid"
                                    type="text"
                                    name="split_amount_material"
                                    value=""
                                    autocomplete="off"
                                />
                            </td>
                        </tr>

                        <tr>
                            <td class="ps-2">
                                {{ \App\Models\Payment::CATEGORY_OPSTE }}
                            </td>
                            <td class="pe-2">
                                <input
                                    class="split-amount-change split-amount-opste amount-mask form-control form-control form-control-solid"
                                    type="text"
                                    name="split_amount_opste"
                                    value=""
                                    autocomplete="off"
                                />
                            </td>
                        </tr>

                        <tr class="fw-bolder bg-light-info">
                            <td class="ps-2">
                                ИТОГО
                            </td>
                            <td class="text-end pe-2 split-amount-total">0</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex flex-center py-3">
            <button type="submit" class="split-amount-save btn btn-primary me-3" disabled>
                <span class="indicator-label">Сохранить</span>
            </button>
            <button type="button" class="btn btn-light float-left" data-bs-dismiss="modal">Закрыть</button>
        </div>
    </form>
</div>
