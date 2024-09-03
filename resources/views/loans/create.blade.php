@extends('layouts.app')

@section('title', 'Новый займ/кредит')
@section('toolbar-title', 'Новый займ/кредит')
@section('breadcrumbs', Breadcrumbs::render('loans.create'))

@section('content')
    <div class="post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Новый займ / кредит</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('loans.store') }}?return_url={{ request()->get('return_url', '') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-5">
                            <div class="col-md-12 fv-row">

                                <div class="row">
                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Тип</label>
                                            <div class="position-relative mb-3">
                                                <select name="type_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($types as $typeId => $typeName)
                                                        <option value="{{ $typeId }}">{{ $typeName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Номер</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="name"
                                                    value="{{ old('name') }}"
                                                    required
                                                />
                                            </div>
                                            @if ($errors->has('name'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('name')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Компания</label>
                                            <div class="position-relative mb-3">
                                                <select name="company_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($companies as $company)
                                                        <option value="{{ $company->id }}" {{ $company->id === 1 ? 'selected' : '' }}>{{ $company->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Банк</label>
                                            <div class="position-relative mb-3">
                                                <select name="bank_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    <option value="">Не указан</option>
                                                    @foreach($banks as $bankId => $bankName)
                                                        <option value="{{ $bankId }}">{{ $bankName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Тип контрагента</label>
                                            <div class="position-relative mb-3">
                                                <select name="organization_type_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($organizationTypes as $typeId => $typeName)
                                                        <option value="{{ $typeId }}">{{ $typeName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Контрагент</label>
                                            <div class="position-relative mb-3">
                                                <select name="organization_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    <option value="">Не указана</option>
                                                    @foreach($organizations as $organization)
                                                        <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Тип кредита (если выбран кредит)</label>
                                            <div class="position-relative mb-3">
                                                <select name="credit_type_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($creditTypes as $typeId => $typeName)
                                                        <option value="{{ $typeId }}">{{ $typeName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Дата зачисления</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('start_date') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="start_date"
                                                    value="{{ old('start_date') }}"
                                                    readonly
                                                />
                                            </div>
                                            @if ($errors->has('start_date'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('start_date')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Дата окончания</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('end_date') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="end_date"
                                                    value="{{ old('end_date') }}"
                                                    readonly
                                                />
                                            </div>
                                            @if ($errors->has('end_date'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('end_date')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 fv-row">
                                        <div class="fv-row">
                                            <div class="mb-1">
                                                <label class="loan-sum form-label fw-bolder text-dark fs-6">Сумма займа/кредита</label>
                                                <div class="position-relative mb-3">
                                                    <input
                                                            class="amount-mask form-control form-control-lg form-control-solid {{ $errors->has('total_amount') ? 'is-invalid' : '' }}"
                                                            type="text"
                                                            name="total_amount"
                                                            value="{{ old('total_amount', 0) }}"
                                                            required
                                                    />
                                                </div>
                                                @if ($errors->has('total_amount'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div>{{ implode(' ', $errors->get('total_amount')) }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 fv-row">
                                        <div class="fv-row">
                                            <div class="mb-1">
                                                <label class="loan-paid form-label fw-bolder text-dark fs-6">Сумма оплаты</label>
                                                <div class="position-relative mb-3">
                                                    <input
                                                        class="amount-mask form-control form-control-lg form-control-solid {{ $errors->has('paid_amount') ? 'is-invalid' : '' }}"
                                                        type="text"
                                                        name="paid_amount"
                                                        value="{{ old('paid_amount', 0) }}"
                                                        required
                                                    />
                                                </div>
                                                @if ($errors->has('paid_amount'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div>{{ implode(' ', $errors->get('paid_amount')) }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 fv-row">
                                        <div class="fv-row mb-10">
                                            <div class="mb-1">
                                                <label class="form-label fw-bolder text-dark fs-6">Процент</label>
                                                <div class="position-relative mb-3">
                                                    <input
                                                        class="amount-mask form-control form-control-lg form-control-solid {{ $errors->has('percent') ? 'is-invalid' : '' }}"
                                                        type="text"
                                                        name="percent"
                                                        value="{{ old('percent', 0) }}"
                                                    />
                                                </div>
                                                @if ($errors->has('percent'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div>{{ implode(' ', $errors->get('percent')) }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 fv-row">
                                        <div class="fv-row mb-10">
                                            <div class="mb-1">
                                                <label class="form-label fw-bolder text-dark fs-6">Расчет оплаты</label>
                                                <div class="position-relative mb-3">
                                                    <select name="auto_paid" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                        <option value="auto">Автоматический</option>
                                                        <option value="manual" selected>Ручной</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-8 fv-row" id="visible-search-name" style="display: none;">
                                        <div class="fv-row">
                                            <div class="mb-1">
                                                <label class="form-label fw-bolder text-dark fs-6">Описание для поиска оплат</label>
                                                <div class="position-relative mb-3">
                                                    <input
                                                            class="form-control form-control-lg form-control-solid {{ $errors->has('search_name') ? 'is-invalid' : '' }}"
                                                            type="text"
                                                            name="search_name"
                                                            value="{{ old('search_name') }}"
                                                    />
                                                </div>
                                                @if ($errors->has('search_name'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div>{{ implode(' ', $errors->get('search_name')) }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-10 fv-row">
                                    <div class="mb-1">
                                        <label class="form-label fw-bolder text-dark fs-6">Описание</label>
                                        <div class="position-relative mb-3">
                                            <textarea
                                                class="form-control form-control-lg form-control-solid {{ $errors->has('description') ? 'is-invalid' : '' }}"
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

                                <div class="mb-10">
                                    <label class="form-label fw-bolder text-dark fs-6">Теги для уведомлений</label>
                                    <input name="tags" class="form-control form-control-solid form-control-lg" value="" id="tags"/>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Создать</span>
                            </button>
                            <a href="{{ request()->get('return_url') ?? route('loans.index') }}" class="btn btn-light">Отменить</a>
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
            new Tagify(document.querySelector('#tags'));
            checkCreditType();
        });

        $('select[name=auto_paid]').on('change', function() {
            const value = $(this).val();
            if (value === 'auto') {
                $('#visible-search-name').show();
            } else {
                $('#visible-search-name').hide();
            }
        });

        $('select[name=credit_type_id]').on('change', function() {
            checkCreditType();
        });

        function checkCreditType() {
            const value = $('select[name=credit_type_id]').val();
            if (value == 0) {
                $('.loan-sum').text('Сумма кредита');
                $('.loan-paid').text('Погашено');
            } else {
                $('.loan-sum').text('Всего');
                $('.loan-paid').text('В использовании');
            }
        };
    </script>
@endpush
