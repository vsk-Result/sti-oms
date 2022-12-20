@extends('layouts.app')

@section('toolbar-title', 'Изменение займа / кредита')
@section('breadcrumbs', Breadcrumbs::render('loans.edit', $loan))

@section('content')
    <div class="post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Изменение займа / кредита</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('loans.update', $loan) }}?return_url={{ request()->get('return_url', '') }}" method="POST" enctype="multipart/form-data">
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
                                                        <option value="{{ $typeId }}" {{ $loan->type_id === $typeId ? 'selected' : '' }}>{{ $typeName }}</option>
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
                                                    value="{{ old('name', $loan->name) }}"
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
                                            <label class="form-label fw-bolder text-dark fs-6">Номер для поиска оплат</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('search_name') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="search_name"
                                                    value="{{ old('search_name', $loan->search_name) }}"
                                                    required
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

                                <div class="row">
                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Компания</label>
                                            <div class="position-relative mb-3">
                                                <select name="company_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($companies as $company)
                                                        <option value="{{ $company->id }}" {{ $company->id === $loan->company_id ? 'selected' : '' }}>{{ $company->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Банк</label>
                                            <div class="position-relative mb-3">
                                                <select name="bank_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    <option value="" {{ is_null($loan->bank_id) ? 'selected' : '' }}>Не указан</option>
                                                    @foreach($banks as $bankId => $bankName)
                                                        <option value="{{ $bankId }}" {{ $loan->bank_id === $bankId ? 'selected' : '' }}>{{ $bankName }}</option>
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
                                                    <option value="" {{ is_null($loan->organization_id) ? 'selected' : '' }}>Не указана</option>
                                                    @foreach($organizations as $organization)
                                                        <option value="{{ $organization->id }}" {{ $loan->organization_id === $organization->id ? 'selected' : '' }}>{{ $organization->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Дата зачисления</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('start_date') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="start_date"
                                                    value="{{ old('start_date', $loan->start_date) }}"
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

                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Дата окончания</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('end_date') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="end_date"
                                                    value="{{ old('end_date', $loan->end_date) }}"
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
                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="fv-row">
                                            <div class="mb-1">
                                                <label class="form-label fw-bolder text-dark fs-6">Сумма займа/кредита</label>
                                                <div class="position-relative mb-3">
                                                    <input
                                                            class="form-control form-control-lg form-control-solid {{ $errors->has('total_amount') ? 'is-invalid' : '' }}"
                                                            type="text"
                                                            name="total_amount"
                                                            value="{{ old('total_amount', $loan->total_amount) }}"
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

                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="fv-row">
                                            <div class="mb-1">
                                                <label class="form-label fw-bolder text-dark fs-6">Сумма долга</label>
                                                <div class="position-relative mb-3">
                                                    <input
                                                        class="form-control form-control-lg form-control-solid {{ $errors->has('amount') ? 'is-invalid' : '' }}"
                                                        type="text"
                                                        name="amount"
                                                        value="{{ old('amount', $loan->amount) }}"
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

                                    <div class="col-md-4 fv-row">
                                        <div class="mb-10 fv-row">
                                            <div class="mb-1">
                                                <label class="form-label fw-bolder text-dark fs-6">Процент</label>
                                                <div class="position-relative mb-3">
                                                    <input
                                                        class="form-control form-control-lg form-control-solid {{ $errors->has('percent') ? 'is-invalid' : '' }}"
                                                        type="text"
                                                        name="percent"
                                                        value="{{ old('percent', $loan->percent) }}"
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

                                <div class="mb-10 fv-row">
                                    <div class="mb-1">
                                        <label class="form-label fw-bolder text-dark fs-6">Описание</label>
                                        <div class="position-relative mb-3">
                                            <textarea
                                                class="form-control form-control-lg form-control-solid {{ $errors->has('description') ? 'is-invalid' : '' }}"
                                                name="description"
                                            >{{ old('description', $loan->description) }}</textarea>
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
                                    <input name="tags" class="form-control form-control-solid form-control-lg" value="{{ implode(', ', $loan->notifyTags->pluck('tag')->toArray()) }}" id="tags"/>
                                </div>
                            </div>

                            <div class="mb-10 fv-row">
                                <label class="form-label fw-bolder text-dark fs-6">Статус</label>
                                <div class="position-relative mb-3">
                                    <select name="status_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                        @foreach($statuses as $statusId => $status)
                                            <option value="{{ $statusId }}" {{ $statusId === $loan->status_id ? 'selected' : '' }}>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Сохранить</span>
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
        });
    </script>
@endpush