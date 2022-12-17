@extends('layouts.app')

@section('toolbar-title', 'Планировщик задач')
@section('breadcrumbs', Breadcrumbs::render('scheduler.index'))

@section('content')
    <div class="post">
        <div class="content-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 mb-1">Автоматический перенос оплат между контрагентами</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Каждый день в 13:00 и в 18:00</span>
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

                            @if (session()->has('status'))
                                <div class="alert alert-dismissible bg-light-success border border-dashed border-success d-flex flex-column flex-sm-row p-5 mb-10">
                                    <div class="d-flex flex-column pe-0 pe-sm-10">
                                        <h5 class="mb-1 fs-6">{{ session()->get('status') }}</h5>
                                    </div>
                                </div>
                            @endif

                            <form class="form" action="{{ route('organizations.transfer_payments.import.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="col-md-12 fv-row mb-5">
                                    <label class="fs-5 fw-bold mb-2">Файл Excel с таблицей контрагентов</label>
                                    <input type="file" class="form-control form-control-solid" placeholder="" name="file" />
                                </div>

                                <div class="col-md-12 fv-row mb-5">
                                    <a class="fs-6 fw-bold" href="/storage/public/transfer_organizations_payments.xlsx" >Скачать актуальную версию таблицы</a>
                                </div>

                                <div class="d-flex flex-center py-3">
                                    <button type="submit" class="btn btn-primary me-3">
                                        <span class="indicator-label">Сохранить</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
