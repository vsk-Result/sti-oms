@extends('layouts.app')

@section('toolbar-title', 'Объекты')
@section('breadcrumbs', Breadcrumbs::render('objects.index'))

@section('content')
    <div class="post" id="kt_post">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    @can('create objects')
                        <a href="{{ route('objects.create') }}" class="btn btn-light-primary">
                                <span class="svg-icon svg-icon-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                        <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>
                                        <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                                    </svg>
                                </span>
                            Новый объект
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        @foreach($objects as $object)
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body pt-9 pb-0">
                            @include('objects.parts._object_general_info')
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
