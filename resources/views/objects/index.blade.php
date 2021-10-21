@extends('layouts.app')

@section('toolbar-title', 'Объекты')
@section('breadcrumbs', Breadcrumbs::render('objects.index'))

@section('content')
    <div class="post" id="kt_post">
        @foreach($objects as $object)
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body pt-9 pb-0">
                            <div class="d-flex flex-wrap flex-sm-nowrap mb-6">
                                <div class="me-7 mb-4">
                                    <div class="symbol symbol-150px">
                                        <div class="symbol-label" style="background-image:url({{ $object->getPhoto() }})"></div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                        <div class="d-flex flex-column">
                                            <div class="d-flex align-items-center mb-1">
                                                <a href="#" class="text-gray-800 text-hover-primary fs-2 fw-bolder me-3">{{ $object->code . ' | ' . $object->name }}</a>
                                                <span class="badge badge-light-success me-auto">Активен</span>
                                            </div>
                                            <div class="d-flex flex-wrap fw-bold mb-4 fs-5 text-gray-400">{{ $object->address }}</div>
                                        </div>
                                        <div class="d-flex mb-4">
                                            <a href="#" class="btn btn-sm btn-bg-light btn-active-color-primary me-3" data-bs-toggle="modal" data-bs-target="#kt_modal_users_search">Добавить оплату</a>
                                            <a href="#" class="btn btn-sm btn-primary me-3" data-bs-toggle="modal" data-bs-target="#kt_modal_new_target">В Excel </a>
                                            <!--begin::Menu-->
                                            <div class="me-0">
                                                <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <i class="bi bi-three-dots fs-3"></i>
                                                </button>
                                                <!--begin::Menu 3-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-bold w-200px py-3" data-kt-menu="true">
                                                    <!--begin::Heading-->
                                                    <div class="menu-item px-3">
                                                        <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">Payments</div>
                                                    </div>
                                                    <!--end::Heading-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3">Create Invoice</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link flex-stack px-3">Create Payment
                                                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="" data-bs-original-title="Specify a target name for future usage and reference" aria-label="Specify a target name for future usage and reference"></i></a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3">Generate Bill</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3" data-kt-menu-trigger="hover" data-kt-menu-placement="right-end">
                                                        <a href="#" class="menu-link px-3">
                                                            <span class="menu-title">Subscription</span>
                                                            <span class="menu-arrow"></span>
                                                        </a>
                                                        <!--begin::Menu sub-->
                                                        <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                                            <!--begin::Menu item-->
                                                            <div class="menu-item px-3">
                                                                <a href="#" class="menu-link px-3">Plans</a>
                                                            </div>
                                                            <!--end::Menu item-->
                                                            <!--begin::Menu item-->
                                                            <div class="menu-item px-3">
                                                                <a href="#" class="menu-link px-3">Billing</a>
                                                            </div>
                                                            <!--end::Menu item-->
                                                            <!--begin::Menu item-->
                                                            <div class="menu-item px-3">
                                                                <a href="#" class="menu-link px-3">Statements</a>
                                                            </div>
                                                            <!--end::Menu item-->
                                                            <!--begin::Menu separator-->
                                                            <div class="separator my-2"></div>
                                                            <!--end::Menu separator-->
                                                            <!--begin::Menu item-->
                                                            <div class="menu-item px-3">
                                                                <div class="menu-content px-3">
                                                                    <!--begin::Switch-->
                                                                    <label class="form-check form-switch form-check-custom form-check-solid">
                                                                        <!--begin::Input-->
                                                                        <input class="form-check-input w-30px h-20px" type="checkbox" value="1" checked="checked" name="notifications">
                                                                        <!--end::Input-->
                                                                        <!--end::Label-->
                                                                        <span class="form-check-label text-muted fs-6">Recuring</span>
                                                                        <!--end::Label-->
                                                                    </label>
                                                                    <!--end::Switch-->
                                                                </div>
                                                            </div>
                                                            <!--end::Menu item-->
                                                        </div>
                                                        <!--end::Menu sub-->
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3 my-1">
                                                        <a href="#" class="menu-link px-3">Settings</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu 3-->
                                            </div>
                                            <!--end::Menu-->
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap justify-content-start">
                                        <div class="d-flex flex-wrap">
                                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="fs-4 fw-bolder {{ $object->payments->sum('amount') < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($object->payments->sum('amount'), 2, '.', ' ') }}</div>
                                                </div>
                                                <div class="fw-bold fs-6 text-gray-400">Баланс</div>
                                            </div>
                                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="fs-4 fw-bolder text-danger">{{ number_format($object->payments->where('amount', '<', 0)->sum('amount'), 2, '.', ' ') }}</div>
                                                </div>
                                                <div class="fw-bold fs-6 text-gray-400">Расходы</div>
                                            </div>
                                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="fs-4 fw-bolder text-success">{{ number_format($object->payments->where('amount', '>=', 0)->sum('amount'), 2, '.', ' ') }}</div>
                                                </div>
                                                <div class="fw-bold fs-6 text-gray-400">Приходы</div>
                                            </div>
                                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="fs-4 fw-bolder text-danger">{{ number_format($object->payments->where('category', \App\Models\Payment::CATEGORY_OPSTE)->sum('amount'), 2, '.', ' ') }}</div>
                                                </div>
                                                <div class="fw-bold fs-6 text-gray-400">Общие расходы</div>
                                            </div>
                                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="fs-4 fw-bolder text-danger">{{ number_format($object->payments->where('category', \App\Models\Payment::CATEGORY_MATERIAL)->sum('amount'), 2, '.', ' ') }}</div>
                                                </div>
                                                <div class="fw-bold fs-6 text-gray-400">Расходы на материалы</div>
                                            </div>
                                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="fs-4 fw-bolder text-danger">{{ number_format($object->payments->where('category', \App\Models\Payment::CATEGORY_RAD)->sum('amount'), 2, '.', ' ') }}</div>
                                                </div>
                                                <div class="fw-bold fs-6 text-gray-400">Расходы на услуги</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
