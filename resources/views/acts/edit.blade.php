@extends('layouts.app')

@section('toolbar-title', 'Изменения акт')
@section('breadcrumbs', Breadcrumbs::render('acts.edit', $act))

@section('content')
    <div class="post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Изменение акта за {{ $act->getDateFormatted() }}</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('acts.update', $act) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="return_url" value="{{ url()->previous() }}">

                        <div class="row mb-5">
                            <div class="col-md-12">
                                <div class="mb-1">
                                    <label class="form-label fw-bolder text-dark fs-6">Номер</label>
                                    <div class="position-relative mb-3">
                                        <input
                                            class="form-control form-control-lg form-control-solid {{ $errors->has('number') ? 'is-invalid' : '' }}"
                                            type="text"
                                            name="number"
                                            value="{{ old('number', $act->number) }}"
                                        />
                                    </div>
                                    @if ($errors->has('number'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div>{{ implode(' ', $errors->get('number')) }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col-md-12 fv-row">

                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Договор</label>
                                            <div class="position-relative mb-3">
                                                <select name="contract_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($contracts as $contract)
                                                        <option value="{{ $contract->id }}" {{ $contract->id === $act->contract_id ? 'selected' : '' }}>{{ $contract->object->code }} | {{ $contract->getName() }}</option>

                                                        @foreach($contract->children as $subContract)
                                                            <option value="{{ $subContract->id }}" {{ $subContract->id === $act->contract_id ? 'selected' : '' }}>{{ $subContract->object->code }} | {{ $contract->getName() . ' | ' . $subContract->getName() }}</option>
                                                        @endforeach
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Дата акта</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('date') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="date"
                                                    value="{{ old('date', $act->date) }}"
                                                    readonly
                                                    required
                                                />
                                            </div>
                                            @if ($errors->has('date'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('date')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-3 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Планируемая дата оплаты</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                        class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('planned_payment_date') ? 'is-invalid' : '' }}"
                                                        type="text"
                                                        name="planned_payment_date"
                                                        value="{{ old('planned_payment_date', $act->planned_payment_date) }}"
                                                        readonly
                                                        required
                                                />
                                            </div>
                                            @if ($errors->has('date'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('planned_payment_date')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Сумма выполнения</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('amount') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="amount"
                                                    value="{{ old('amount', $act->amount) }}"
                                                    required
                                                    autocomplete="off"
                                                />
                                            </div>
                                            @if ($errors->has('amount'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('amount')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Сумма удержания аванса</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('amount_avans') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="amount_avans"
                                                    value="{{ old('amount_avans', $act->amount_avans) }}"
                                                    required
                                                    autocomplete="off"
                                                />
                                            </div>
                                            @if ($errors->has('amount_avans'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('amount_avans')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Сумма удержания депозита</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('amount_deposit') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="amount_deposit"
                                                    value="{{ old('amount_deposit', $act->amount_deposit) }}"
                                                    required
                                                    autocomplete="off"
                                                />
                                            </div>
                                            @if ($errors->has('amount_deposit'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('amount_deposit')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Описание</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('description') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="description"
                                                    value="{{ old('description', $act->description) }}"
                                                    autocomplete="off"
                                                />
                                            </div>
                                            @if ($errors->has('description'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('description')) }}</div>
                                                </div>
                                            @endif
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
                                                accept=".jpg, .jpeg, .png, .pdf, .doc, .docx"
                                            />
                                            <div class="form-text">Доступные форматы:
                                                <code>jpg, jpeg, png, pdf, doc, docx</code>
                                            </div>
                                            @if ($errors->has('files.*'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    @foreach($errors->get('files.*') as $message)
                                                        <div>{{ implode(' ', $message) }}</div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-10 fv-row">
                                    <label class="form-label fw-bolder text-dark fs-6">Статус</label>
                                    <div class="position-relative mb-3">
                                        <select name="status_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                            @foreach($statuses as $statusId => $status)
                                                <option value="{{ $statusId }}" {{ $statusId === $act->status_id ? 'selected' : '' }}>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-5">
                            <h3 class="fw-bolder">Оплаты по акту</h3>

                            <div id="payment-template" class="col-md-3 mb-10 fv-row" style="display: none;">
                                <table>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <input
                                                class="date-range-picker-single form-control form-control-lg form-control-solid"
                                                type="text"
                                                name="payments_date[]"
                                                value=""
                                                readonly
                                            />
                                        </td>
                                        <td>
                                            <input
                                                class="form-control form-control-lg form-control-solid"
                                                type="text"
                                                name="payments_amount[]"
                                                value=""
                                                autocomplete="off"
                                            />
                                        </td>
                                        <td>
                                            <input
                                                class="form-control form-control-lg form-control-solid"
                                                type="text"
                                                name="payments_description[]"
                                                value=""
                                                autocomplete="off"
                                            />
                                        </td>
                                        <td>
                                            <button
                                                type="button"
                                                class="destroy-payment btn btn-outline btn-outline-dashed btn-outline-danger btn-active-light-danger"
                                            >
                                                Удалить
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mb-4 d-flex flex-left">
                                <button
                                    type="button"
                                    id="create-payment"
                                    class="mt-4 btn btn-outline btn-outline-dashed btn-outline-success btn-active-light-success me-2 mb-2"
                                >
                                    Добавить запись
                                </button>
                            </div>

                            <div class="d-flex flex-left mb-4">
                                <table id="payments-table" class="table align-middle table-row-dashed fs-6">
                                    <thead>
                                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                        <th class="min-w-150px">Дата оплаты</th>
                                        <th class="min-w-150px">Сумма оплаты</th>
                                        <th class="min-w-150px">Описание</th>
                                        <th class="min-w-150px rounded-end pe-4">Действие</th>
                                    </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-bold">
                                        @foreach($act->payments as $payment)
                                            <tr>
                                                <td>
                                                    <input
                                                        class="date-range-picker-single form-control form-control-lg form-control-solid"
                                                        type="text"
                                                        name="isset_payments_date[{{ $payment->id }}]"
                                                        value="{{ $payment->date }}"
                                                        readonly
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        class="form-control form-control-lg form-control-solid"
                                                        type="text"
                                                        name="isset_payments_amount[{{ $payment->id }}]"
                                                        value="{{ $payment->amount }}"
                                                        autocomplete="off"
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        class="form-control form-control-lg form-control-solid"
                                                        type="text"
                                                        name="isset_payments_description[{{ $payment->id }}]"
                                                        value="{{ $payment->description }}"
                                                        autocomplete="off"
                                                    />
                                                </td>
                                                <td>
                                                    <button
                                                        type="button"
                                                        class="destroy-payment btn btn-outline btn-outline-dashed btn-outline-danger btn-active-light-danger"
                                                    >
                                                        Удалить
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Сохранить</span>
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-light">Отменить</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#create-payment').on('click', function () {
                const $payment = $('#payment-template').clone();
                $('#payments-table tbody').append($payment.find('tr'));

                mainApp.init();
            });

            $(document).on('click', '.destroy-payment', function() {
                $(this).closest('tr').remove();
            });
        });
    </script>
@endpush
