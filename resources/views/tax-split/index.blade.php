@extends('layouts.app')

@section('title', 'Разбивка налогов')
@section('toolbar-title', 'Разбивка налогов')
@section('breadcrumbs', Breadcrumbs::render('tax_split.index'))

@section('content')
    <div class="post">
        <div class="content-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 mb-1">Разбивка налогов</span>
                            </h3>
                        </div>
                        <div class="card-body py-3">
                            @if ($errors->any())
                                <div class="alert alert-dismissible bg-light-danger border border-dashed border-danger d-flex flex-column flex-sm-row p-5 mb-10">
                                    <div class="d-flex flex-column pe-0 pe-sm-10">
                                        <h5 class="mb-1">Ошибки при загрузке</h5>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            @if (session()->has('status_success'))
                                <div class="alert alert-dismissible bg-light-success border border-dashed border-success d-flex flex-column flex-sm-row p-5 mb-10">
                                    <div class="d-flex flex-column pe-0 pe-sm-10">
                                        <h5 class="mb-1 fs-6">{{ session()->get('status_success') }}</h5>
                                    </div>
                                </div>
                            @endif

                            @if (session()->has('status_error'))
                                <div class="alert alert-dismissible bg-light-danger border border-dashed border-danger d-flex flex-column flex-sm-row p-5 mb-10">
                                    <div class="d-flex flex-column pe-0 pe-sm-10">
                                        <h5 class="mb-1 fs-6">{{ session()->get('status_error') }}</h5>
                                    </div>
                                </div>
                            @endif

                            <form class="form" action="{{ route('tax_split.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Дата разбивки</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('split_date') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="split_date"
                                                    value="{{ old('split_date') }}"
                                                    readonly
                                                    required
                                                />
                                            </div>
                                            @if ($errors->has('split_date'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('split_date')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Тип налога</label>
                                            <div class="position-relative mb-3">
                                                <select id="filter-type" name="type_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($taxTypes as $typeId => $typeName)
                                                        <option value="{{ $typeId }}">{{ $typeName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-12 mb-10 fv-row">
                                    <div class="mb-1">
                                        <label class="form-label fw-bolder text-dark fs-6">Оплаты для разбивки</label>
                                        <div class="position-relative mb-3">
                                            <select required multiple id="payments-select" name="payment_ids[]" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                @foreach($payments as $payment)
                                                    <option value="{{ $payment->id }}">{{ $payment->description }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 fv-row mb-5">
                                    <label class="fs-5 fw-bold mb-2">Файл Excel с таблицей для разбивки</label>
                                    <input required type="file" class="form-control form-control-solid" placeholder="" name="file" />
                                </div>

                                <div class="d-flex flex-center py-3">
                                    <button type="submit" class="btn btn-primary me-3">
                                        <span class="indicator-label">Сохранить</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#payments-select').select2({
                sorter: function (data) {
                    return data.sort(function (a, b) {
                        return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
                    });
                },
                ajax: {
                    url: '/payments?type=select',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            search: params.term,
                            type: $('#filter-type').val()
                        };
                    },
                    processResults: function (data) {
                        const results = [];
                        $.each(data.payments, function (id, text) {
                            results.push({id, text})
                        });
                        return {results};
                    }
                }
            });
        });
    </script>
@endpush
