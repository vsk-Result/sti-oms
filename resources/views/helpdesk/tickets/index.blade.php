@extends('layouts.app')

@section('toolbar-title', 'Служба поддержки - Обращения')
@section('breadcrumbs', Breadcrumbs::render('helpdesk.tickets.index'))

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-5 mb-xl-8 border-0">
                <div class="card-header border-0 p-0">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <h1 class="d-flex align-items-center my-1">
                                <span class="text-dark fw-bold fs-1">
                                    Мои обращения
                                </span>

                                <small class="text-muted fs-6 fw-semibold ms-1">
                                    ({{ $tickets->count() }})
                                </small>
                            </h1>
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                            <a href="{{ route('helpdesk.tickets.create') }}" class="btn btn-light-primary me-3">
                        <span class="svg-icon svg-icon-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>
                                <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                            </svg>
                        </span>
                                Новое обращение
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @forelse($tickets as $ticket)
                @include('helpdesk.tickets.partials._ticket', compact('ticket'))

                @if(!$loop->last)
                    <div class="separator separator-dashed border-gray-300 my-8"></div>
                @endif
            @empty
                <p class="text-start text-dark fw-bolder d-block my-4 fs-6">
                    Вы не создали ни одного обращения
                </p>
            @endforelse
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {

        });
    </script>
@endpush
