@extends('layouts.app')

@section('title', 'Отчет о движении денежных средств')
@section('toolbar-title', 'Отчет о движении денежных средств')
@section('breadcrumbs', Breadcrumbs::render('pivots.money_movement.index'))

@section('content')
    <div class="post">
        <div class="content-fluid">
            <div class="row">
                <div class="col-md-6">
                    @if (session()->has('task_in_progress'))
                        <div class="alert alert-dismissible bg-light-warning border border-dashed border-warning d-flex flex-column flex-sm-row p-5 mb-10">
                            <div class="d-flex flex-column pe-0 pe-sm-10">
                                <p class="mb-0">Отчет с данными параметрами находится на стадии формирования. После завершения на почту придет файл с отчетом.</p>
                            </div>
                        </div>
                    @endif

                    @if (session()->has('task_created'))
                        <div class="alert alert-dismissible bg-light-success border border-dashed border-success d-flex flex-column flex-sm-row p-5 mb-10">
                            <div class="d-flex flex-column pe-0 pe-sm-10">
                                <p class="mb-0">Система начала формировать отчет. По завершению вам на почту придет файл с отчетом. Можете продолжить пользоваться сайтом.</p>
                            </div>
                        </div>
                    @endif

                    <div class="card mb-5 mb-xl-8">
                        <div class="card-body py-3">
                            <form class="form" action="{{ route('pivots.money_movement.exports.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="form-group mb-3">
                                    <label class="form-label">Период</label>
                                    <input
                                        name="period"
                                        class="form-control form-control-solid date-range-picker"
                                        value=""
                                        autocomplete="off"
                                        required
                                    />

                                    <div class="mt-3">
                                        <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2017</span>
                                        <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2018</span>
                                        <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2019</span>
                                        <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2020</span>
                                        <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2021</span>
                                        <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2022</span>
                                        <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2023</span>
                                        <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2024</span>
                                        <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2025</span>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Контрагент</label>
                                    <select
                                        id="organization-select"
                                        name="organization_id[]"
                                        class="form-select form-select-solid"
                                        multiple
                                    ></select>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Банк</label>
                                    <select
                                        name="bank_id[]"
                                        class="form-select form-select-solid"
                                        data-control="select2"
                                        multiple
                                    >
                                        @foreach($banks as $bankId => $bank)
                                            <option value="{{ $bankId }}">{{ $bank }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Тип оплат</label>
                                    <select
                                            name="payment_type_id[]"
                                            class="form-select form-select-solid"
                                            data-control="select2"
                                            multiple
                                    >
                                        @foreach($paymentTypes as $typeId => $type)
                                            <option value="{{ $typeId }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Объект</label>
                                    <select
                                        id="filter-object"
                                        name="object_id[]"
                                        class="form-select form-select-solid"
                                        data-control="select2"
                                        multiple
                                    >
                                        @foreach($objects as $object)
                                            <option value="{{ $object->id }}" >{{ $object->getName() }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-check form-check-custom form-check-solid mb-3 fw-bold fs-6 mt-4">
                                    <input name="need_group_by_objects" class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                                    <label class="form-check-label" for="flexCheckChecked">Добавить разбивку по объектам в контрагентах</label>
                                </div>

                                <div class="d-flex py-3">
                                    <button type="submit" class="btn btn-primary me-3">
                                        <span class="indicator-label">Экспорт в Excel</span>
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
            $('#organization-select').select2({
                sorter: function(data) {
                    return data.sort(function(a, b) {
                        return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
                    });
                },
                ajax: {
                    url: '/organizations?type=select',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            search: params.term,
                            objects: $('#filter-object').val()
                        };
                    },
                    processResults: function (data) {
                        const results = [];
                        $.each(data.organizations, function(id, text) {
                            results.push({id, text})
                        });
                        return {results};
                    }
                }
            });

            $('.period-quick').on('click', function() {
                const year = $(this).text();
                $('input[name=period]').val('01.01.' + year + ' - 31.12.' + year);
            });
        });
    </script>
@endpush
