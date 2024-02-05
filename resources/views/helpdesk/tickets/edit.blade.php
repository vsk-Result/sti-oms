@extends('layouts.app')

@section('toolbar-title', 'Изменение обращения')
@section('breadcrumbs', Breadcrumbs::render('helpdesk.tickets.edit', $ticket))

@section('content')
    <div class="post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Изменение обращения</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('helpdesk.tickets.update',$ticket) }}" method="POST" enctype="multipart/form-data">
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
                                                    value="{{ old('title', $ticket->title) }}"
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
                                            >{{ old('content', $ticket->content) }}</textarea>
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
                                        <label class="form-label fw-bolder text-dark fs-6">Объект</label>
                                        <div class="position-relative mb-3">
                                            <select name="object_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                <option value="null" {{ $ticket->object_id === null ? 'selected' : '' }}>Общее</option>
                                                @foreach($objects as $object)
                                                    <option value="{{ $object->id }}" {{ $ticket->object_id === $object->id ? 'selected' : '' }}>{{ $object->getName() }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                @if (auth()->user()->hasRole('super-admin'))
                                    <div class="mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Приоритет</label>
                                            <div class="position-relative mb-3">
                                                <select name="priority_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($priorities as $priority)
                                                        <option value="{{ $priority->id }}" {{ $ticket->priority_id === $priority->id ? 'selected' : '' }}>{{ $priority->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Время на выполнение</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('time_to_complete') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="time_to_complete"
                                                    value="{{ old('time_to_complete', $ticket->time_to_complete) }}"
                                                />
                                            </div>
                                            @if ($errors->has('time_to_complete'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('time_to_complete')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Назначить исполнителя</label>
                                            <div class="position-relative mb-3">
                                                <select name="assign_user_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    <option value="{{ null }}" {{ $ticket->assign_user_id === null ? 'selected' : '' }}>Не назначен</option>
                                                    @foreach($assigners as $assign)
                                                        <option value="{{ $assign->id }}" {{ $ticket->assign_user_id === $assign->id ? 'selected' : '' }}>{{ $assign->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

{{--                                    <div class="mb-10 fv-row">--}}
{{--                                        <div class="mb-1">--}}
{{--                                            <label class="form-label fw-bolder text-dark fs-6">Срок исполнения</label>--}}
{{--                                            <div class="position-relative mb-3">--}}
{{--                                                <input--}}
{{--                                                    class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('execution_date') ? 'is-invalid' : '' }}"--}}
{{--                                                    type="text"--}}
{{--                                                    name="execution_date"--}}
{{--                                                    value="{{ old('execution_date', $ticket->execution_date) }}"--}}
{{--                                                    readonly--}}
{{--                                                />--}}
{{--                                            </div>--}}
{{--                                            @if ($errors->has('execution_date'))--}}
{{--                                                <div class="fv-plugins-message-container invalid-feedback">--}}
{{--                                                    <div>{{ implode(' ', $errors->get('execution_date')) }}</div>--}}
{{--                                                </div>--}}
{{--                                            @endif--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                @endif

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
                                <span class="indicator-label">Сохранить</span>
                            </button>
                            <a href="{{ request()->get('return_url') ?? route('helpdesk.tickets.index') }}" class="btn btn-light">Отменить</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
