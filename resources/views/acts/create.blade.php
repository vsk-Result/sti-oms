@extends('layouts.app')

@section('toolbar-title', 'Новый акт')
@section('breadcrumbs', Breadcrumbs::render('acts.create'))

@section('content')
    <div class="post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Новый акт</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('acts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="return_url" value="{{ url()->previous() }}">

                        <div class="row mb-5">
                            <div class="col-md-12 fv-row">

                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Договор</label>
                                            <div class="position-relative mb-3">
                                                <select name="contract_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($contracts as $contract)
                                                        <option value="{{ $contract->id }}">{{ $contract->getName() }}</option>

                                                        @foreach($contract->children as $subContract)
                                                            <option value="{{ $subContract->id }}">{{ $contract->getName() . ' | ' . $subContract->getName() }}</option>
                                                        @endforeach
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Дата</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('date') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="date"
                                                    value="{{ old('date') }}"
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
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Сумма выполнения</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('amount') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="amount"
                                                    value="{{ old('amount') }}"
                                                    required
                                                />
                                            </div>
                                            @if ($errors->has('amount'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('amount')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Сумма удержания аванса</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('amount_avans') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="amount_avans"
                                                    value="{{ old('amount_avans') }}"
                                                    required
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
                                    <div class="col-md-12 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Сумма удержания депозита</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('amount_deposit') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="amount_deposit"
                                                    value="{{ old('amount_deposit') }}"
                                                    required
                                                />
                                            </div>
                                            @if ($errors->has('amount_deposit'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('amount_deposit')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Описание</label>
                                            <div class="position-relative mb-3">
                                                <textarea
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('description') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="description"
                                                >{{ old('description') }}</textarea>
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

                        <div class="row mb-5">
                            <h3 class="fw-bolder mb-8">Оплаты</h3>

                            <div id="payment-template" class="col-md-3 mb-10 fv-row" style="display: none;">
                                <div class="mb-1">
                                    <label class="form-label fw-bolder text-dark fs-6">Дата</label>
                                    <div class="position-relative mb-3">
                                        <input
                                            class="date-range-picker-single form-control form-control-lg form-control-solid"
                                            type="text"
                                            name="payments_date[]"
                                            value=""
                                            readonly
                                        />
                                    </div>
                                </div>

                                <div class="mb-1 mt-10">
                                    <label class="form-label fw-bolder text-dark fs-6">Сумма</label>
                                    <div class="position-relative mb-3">
                                        <input
                                            class="form-control form-control-lg form-control-solid"
                                            type="text"
                                            name="payments_amount[]"
                                            value=""
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 mb-10 fv-row">
                                <button type="button" id="create-payment" class="mt-9 btn btn-outline btn-outline-dashed btn-outline-success btn-active-light-success me-2 mb-2">Добавить</button>
                            </div>
                        </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Создать</span>
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
                $payment.attr('id', '');
                $payment.show();
                $(this).parent().before($payment);

                mainApp.init();
            });
        });
    </script>
@endpush
