@extends('layouts.app')

@section('title', 'База знаний')
@section('toolbar-title', 'База знаний')
@section('breadcrumbs', Breadcrumbs::render('knowledge.index'))

@section('content')
    <div class="content-fluid">
        <div class="card card-flush h-lg-100">
            <div class="card-header mt-6">
                <div class="card-title flex-column">
                    <h3 class="fw-bold mb-1">Инструкции</h3>
                </div>
            </div>

            <div class="card-body p-9 pt-3">
                @foreach($instructions as $instruction)
                    <div class="d-flex flex-column mb-6">
                        <div class="d-flex align-items-center mb-5">
                            <div class="symbol symbol-30px me-5">
                                <img alt="Icon" src="{{ get_extension_image_path($instruction['ext']) }}">
                            </div>

                            <div class="fw-semibold">
                                <a class="fs-6 fw-bold text-gray-900 text-hover-primary" download="{{ $instruction['file'] }}" href="/storage/knowledge/instructions/{{ $instruction['file'] }}">{{ $instruction['name'] }}</a>

                                <div class="text-gray-500">
                                    {{ $instruction['size'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
