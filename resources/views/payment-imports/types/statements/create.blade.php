@extends('layouts.app')

@section('toolbar-title', 'Загрузка оплат из выписки')
@section('breadcrumbs', Breadcrumbs::render('payment_imports.types.statements.create'))

@section('content')
    <div class="post" id="kt_post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Загрузка оплат из выписки</span>
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

                    <form class="form" action="{{ route('payment_imports.types.statements.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="scroll-y me-n7 pe-7" id="kt_modal_new_address_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_new_address_header" data-kt-scroll-wrappers="#kt_modal_new_address_scroll" data-kt-scroll-offset="300px">
                            <div class="row mb-5">
                                <div class="col-md-6 fv-row">
                                    <label class="required fs-5 fw-bold mb-2">Дата</label>
                                    <input
                                        readonly
                                        type="text"
                                        class="form-control form-control-solid"
                                        name="date"
                                        value="{{ now()->isMonday() ? now()->subDays(3)->format('Y-m-d') : now()->subDays()->format('Y-m-d') }}"
                                    />
                                </div>
                                <div class="col-md-6 fv-row">
                                    <label class="required fs-5 fw-bold mb-2">Компания</label>
                                    <select name="company_id" class="form-select form-select-solid" data-control="select2">
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 fv-row">
                                    <label class="required fs-5 fw-bold mb-2">Банк</label>
                                    <select name="bank_id" class="form-select form-select-solid" data-control="select2">
                                        @foreach($banks as $bankId => $bankName)
                                            <option value="{{ $bankId }}">{{ $bankName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 fv-row">
                                    <label class="required fs-5 fw-bold mb-2">Файл для загрузки</label>
                                    <input type="file" class="form-control form-control-solid" placeholder="" name="file" accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" />
                                    <div class="form-text">Доступные форматы:
                                        <code>xls, xlsx</code>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-5">
                                <div class="col-md-6 fv-row">
                                    <label class="fs-5 fw-bold mb-2">Описание</label>
                                    <input
                                        type="text"
                                        class="form-control form-control-solid"
                                        name="description"
                                        value="Доллар: , Евро: "
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Загрузить</span>
                            </button>
                            <a href="{{ route('payment_imports.index') }}" class="btn btn-light">Отмена</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/daterangepicker/daterangepicker.js') }}"></script>
    <script>
        $('input[name=date]').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "Y-MM-DD"
            }
        })
    </script>
@endpush
