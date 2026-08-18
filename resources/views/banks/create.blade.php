@extends('layouts.app')

@section('title', 'Новый банк')
@section('toolbar-title', 'Новый банк')
@section('breadcrumbs', Breadcrumbs::render('banks.create'))

@section('content')
    <div class="post" id="kt_post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Новый банк</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('banks.store') }}" method="POST">
                        @csrf
                        <div class="scroll-y me-n7 pe-7" id="kt_modal_new_address_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_new_address_header" data-kt-scroll-wrappers="#kt_modal_new_address_scroll" data-kt-scroll-offset="300px">
                            <div class="row mb-5">
                                <div class="col-md-12 fv-row">
                                    <div class="mb-10 fv-row" data-kt-password-meter="true">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Название</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid"
                                                    type="text"
                                                    name="name"
                                                    required
                                                    autofocus
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="fv-row mb-10">
                                        <label class="form-label fw-bolder text-dark fs-6">Баланс</label>
                                        <input
                                            class="amount-mask form-control form-control-lg form-control-solid"
                                            type="text"
                                            name="balance_amount"
                                            required
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Создать</span>
                            </button>
                            <a href="{{ route('banks.index') }}" class="btn btn-light">Отменить</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
