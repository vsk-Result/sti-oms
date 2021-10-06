@extends('layouts.app')

@section('content')
    <div class="post" id="kt_post">
        <div class="row g-6 g-xl-9">
            @foreach($objects as $object)
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-header flex-nowrap border-0 pt-9">
                            <div class="card-title m-0">
                                <div class="symbol symbol-45px w-45px bg-light me-5">
                                    <img src="https://preview.keenthemes.com/metronic8/demo5/assets/media/svg/brand-logos/reddit.svg" alt="image" class="p-3" />
                                </div>
                                <a href="#" class="fs-4 fw-bold text-hover-primary text-gray-600 m-0">{{ $object->code }}</a>
                            </div>
                            <div class="card-toolbar m-0">
                                <button type="button" class="btn btn-clean btn-sm btn-icon btn-icon-primary btn-active-light-primary me-n3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                                    <span class="svg-icon svg-icon-3 svg-icon-primary">
															<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
																<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																	<rect x="5" y="5" width="5" height="5" rx="1" fill="#000000" />
																	<rect x="14" y="5" width="5" height="5" rx="1" fill="#000000" opacity="0.3" />
																	<rect x="5" y="14" width="5" height="5" rx="1" fill="#000000" opacity="0.3" />
																	<rect x="14" y="14" width="5" height="5" rx="1" fill="#000000" opacity="0.3" />
																</g>
															</svg>
														</span>

                                </button>

                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-bold w-200px py-3" data-kt-menu="true">

                                    <div class="menu-item px-3">
                                        <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">Payments</div>
                                    </div>

                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link flex-stack px-3">Import Payments
                                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Specify a target name for future usage and reference"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column px-9 pt-6 pb-8">
                            <a href="#" class="fs-3 fw-bolder text-hover-primary text-dark">{{ $object->code . ' ' . $object->name }}</a>
                            <p class="text-gray-400 fw-bold fs-5 mt-1 mb-7"></p>
                            <div class="d-flex flex-wrap mb-5">
                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3 me-7">
                                    <div class="fs-6 text-gray-800 fw-bolder">284 900.00 RUB</div>
                                    <div class="fw-bold text-gray-400">Balance</div>
                                </div>
                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-7 mb-3">
                                    <div class="fs-6 text-gray-800 fw-bolder">Feb 21, 2021</div>
                                    <div class="fw-bold text-gray-400">Last activity</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="d-flex flex-stack flex-wrap pt-10">
            <div class="fs-6 fw-bold text-gray-700">Showing 1 to 10 of 50 entries</div>
            <!--begin::Pages-->
            <ul class="pagination">
                <li class="page-item previous">
                    <a href="#" class="page-link">
                        <i class="previous"></i>
                    </a>
                </li>
                <li class="page-item active">
                    <a href="#" class="page-link">1</a>
                </li>
                <li class="page-item">
                    <a href="#" class="page-link">2</a>
                </li>
                <li class="page-item">
                    <a href="#" class="page-link">3</a>
                </li>
                <li class="page-item">
                    <a href="#" class="page-link">4</a>
                </li>
                <li class="page-item">
                    <a href="#" class="page-link">5</a>
                </li>
                <li class="page-item">
                    <a href="#" class="page-link">6</a>
                </li>
                <li class="page-item next">
                    <a href="#" class="page-link">
                        <i class="next"></i>
                    </a>
                </li>
            </ul>
            <!--end::Pages-->
        </div>
    </div>
@endsection
