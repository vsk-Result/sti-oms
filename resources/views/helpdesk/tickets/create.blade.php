@extends('layouts.app')

@section('toolbar-title', 'Новое обращение')
@section('breadcrumbs', Breadcrumbs::render('helpdesk.tickets.create'))

@section('content')
    <div class="post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Новое обращение</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('helpdesk.tickets.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-5">
                            <div class="col-md-12 fv-row">

                                <div class="mb-10 fv-row">
                                    <div class="mb-1">
                                        <label class="form-label fw-bolder text-dark fs-6">Тема</label>
                                        <div class="position-relative mb-3">
                                            <input
                                                class="form-control form-control-lg form-control-solid {{ $errors->has('title') ? 'is-invalid' : '' }}"
                                                type="text"
                                                name="title"
                                                value="{{ old('title') }}"
                                                required
                                            />
                                        </div>
                                        @if ($errors->has('title'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('title')) }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-10 fv-row">
                                    <div class="mb-1">
                                        <label class="form-label fw-bolder text-dark fs-6">Описание проблемы</label>
                                        <div class="position-relative mb-3">
                                            <textarea
                                                class="form-control form-control-lg form-control-solid {{ $errors->has('content') ? 'is-invalid' : '' }}"
                                                name="content"
                                                rows="7"
                                            >{{ old('content') }}</textarea>
                                        </div>
                                        @if ($errors->has('content'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('content')) }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-10 fv-row">
                                    <div class="mb-1">
                                        <label class="form-label fw-bolder text-dark fs-6">Приоритет</label>
                                        <div class="position-relative mb-3">
                                            <select name="priority_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                @foreach($priorities as $priority)
                                                    <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-10 fv-row">
                                    <div class="mb-1">
                                        <label class="fs-5 fw-bold mb-2">Приложите файлы</label>
                                        <input
                                                type="file"
                                                multiple
                                                class="form-control form-control-solid {{ $errors->has('files.*') ? 'is-invalid' : '' }}"
                                                placeholder=""
                                                name="files[]"
                                                accept=".jpg, .jpeg, .png, .pdf, .doc, .docx, .xls, .xlsx"
                                        />
                                        <div class="form-text">Доступные форматы:
                                            <code>jpg, jpeg, png, pdf, doc, docx, xls, xlsx</code>
                                        </div>
                                        @if ($errors->has('files.*'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                @foreach($errors->get('files.*') as $message)
                                                    <div>{{ implode(' ', $message) }}</div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Создать</span>
                            </button>
                            <a href="{{ request()->get('return_url') ?? route('helpdesk.tickets.index') }}" class="btn btn-light">Отменить</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
