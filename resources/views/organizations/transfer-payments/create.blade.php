@extends('layouts.app')

@section('toolbar-title', 'Перенос оплат')
@section('breadcrumbs', Breadcrumbs::render('organizations.transfer_payments.create', $organization))

@section('content')
    <div class="post" id="kt_post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Перенос оплат с {{ $organization->name }}</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('organizations.transfer_payments.store', $organization) }}" method="POST">
                        @csrf
                        <div class="scroll-y me-n7 pe-7" id="kt_modal_new_address_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_new_address_header" data-kt-scroll-wrappers="#kt_modal_new_address_scroll" data-kt-scroll-offset="300px">
                            <div class="row mb-5">
                                <div class="col-md-12 fv-row">
                                    <label class="required fs-5 fw-bold mb-2">На организацию</label>
                                    <select name="organization_id" class="form-select form-select-solid" data-control="select2">
                                        @foreach($organizations as $organization)
                                            <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Перенсти</span>
                            </button>
                            <a href="{{ route('organizations.index') }}" class="btn btn-light">Отмена</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
