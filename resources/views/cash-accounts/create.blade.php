@extends('layouts.app')

@section('title', 'Новая касса')
@section('toolbar-title', 'Новая касса')
@section('breadcrumbs', Breadcrumbs::render('cash_accounts.create'))

@section('content')
    <div class="post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Новая касса</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('cash_accounts.store') }}" method="POST">
                        @csrf

                        <div class="row mb-5">
                            <div class="col-md-12 fv-row">

                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Название</label>
                                            <input
                                                class="form-control form-control-lg form-control-solid"
                                                type="text"
                                                name="name"
                                                value="{{ old('name') }}"
                                                required
                                            />
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Ответственный</label>
                                            <div class="position-relative mb-3">
                                                <select name="responsible_user_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($responsibleUsers as $user)
                                                        <option value="{{ $user->id }}" {{ $user->id == auth()->id() ? 'selected' : '' }}>{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Начальный баланс</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="amount-mask amount-formatted form-control form-control-lg form-control-solid"
                                                    type="text"
                                                    name="start_balance_amount"
                                                    value="{{ old('start_balance_amount', 0) }}"
                                                    required
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Создать</span>
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
    <script>
        $(function() {

        });
    </script>
@endpush
