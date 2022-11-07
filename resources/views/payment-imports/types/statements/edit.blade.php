@extends('layouts.app')

@section('toolbar-title', 'Изменение данных загрузки оплат из выписки')
@section('breadcrumbs', Breadcrumbs::render('payment_imports.types.statements.edit'))

@section('content')
    <div class="post" id="kt_post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Изменение данных загрузки оплат из выписки</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    @if ($errors->any())
                        <div class="alert alert-dismissible bg-light-danger border border-dashed border-danger d-flex flex-column flex-sm-row p-5 mb-10">
                            <div class="d-flex flex-column pe-0 pe-sm-10">
                                <h5 class="mb-1">Ошибки при сохранении</h5>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @if (session()->has('status'))
                        <div class="alert alert-dismissible bg-light-danger border border-dashed border-danger d-flex flex-column flex-sm-row p-5 mb-10">
                            <div class="d-flex flex-column pe-0 pe-sm-10">
                                <h5 class="mb-1">Ошибки при сохранении</h5>
                                <ul>
                                    <li>{{ session()->get('status') }}</li>
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form class="form" action="{{ route('payment_imports.types.statements.update', $statement) }}" method="POST" enctype="multipart/form-data">
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
                                            value="{{ old('date', $statement->date) }}"
                                    />
                                </div>
                                <div class="col-md-6 fv-row">
                                    <label class="required fs-5 fw-bold mb-2">Компания</label>
                                    <select name="company_id" class="form-select form-select-solid" data-control="select2">
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}" {{ $statement->company_id === $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 fv-row">
                                    <label class="required fs-5 fw-bold mb-2">Банк</label>
                                    <select name="bank_id" class="form-select form-select-solid" data-control="select2">
                                        @foreach($banks as $bankId => $bankName)
                                            <option value="{{ $bankId }}" {{ $statement->bank_id === $bankId ? 'selected' : '' }}>{{ $bankName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" class="btn btn-primary me-3">
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
