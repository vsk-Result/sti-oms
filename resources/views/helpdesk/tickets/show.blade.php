@extends('layouts.app')

@section('toolbar-title', 'Обращение #' . $ticket->id)
@section('breadcrumbs', Breadcrumbs::render('helpdesk.tickets.show', $ticket))

@section('content')
    <div>
        @include('helpdesk.tickets.partials._info_badges', compact('ticket'))

        <h1 class="fs-2x fw-bold text-gray-900 mb-6 d-flex flex-row justify-content-between">
            {{ $ticket->title }}

            @include('helpdesk.tickets.partials._menu_buttons', compact('ticket'))
        </h1>

        <div class="fs-5 fw-normal text-gray-800 mb-6">
            {!! nl2br($ticket->content) !!}

            <br />
            @include('helpdesk.tickets.partials._attachments', ['attachments' => $ticket->getMedia()])
        </div>

        <div class="d-flex flex-stack flex-wrap">
            <div class="d-flex align-items-center py-1">
                <div class="symbol symbol-35px me-3">
                    <img src="{{ $ticket->createdBy()->first()->getPhoto() }}" alt="{{ $ticket->createdBy()->first()->name }}">
                </div>

                <div class="d-flex flex-column align-items-start justify-content-center">
                    <span class="text-gray-900 fs-7 fw-semibold lh-1 mb-2">{{ $ticket->createdBy()->first()->name }}</span>
                    <span class="text-muted fs-8 fw-semibold lh-1">{{ $ticket->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        <div class="separator separator-dashed border-gray-300 mt-8 mb-10"></div>

        <form class="form mb-10" action="{{ route('helpdesk.tickets.answers.store', $ticket) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group mb-2">
                <textarea name="text" class="form-control" rows="6" placeholder="Напишите ответ здесь..." maxlength="1000" data-kt-autosize="true" style="overflow: hidden; overflow-wrap: break-word; resize: none; text-align: start; height: 151px;"></textarea>
            </div>

            <div class="d-flex align-items-center justify-content-between py-2 mb-5">
                <div class="text-primary fs-4 fw-semibold cursor-pointer collapsed" data-bs-toggle="collapse" data-bs-target="#kt_devs_ask_formatting" aria-expanded="false">
                    <div class="mb-10 fv-row">
                        <div class="mb-1">
                            <label class="fs-5 fw-bold mb-2">Прикрепить файлы</label>
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

                <button class="btn btn-primary fw-bold" data-kt-action="submit">Отправить</button>
            </div>
        </form>

        <h2 class="fw-bold text-gray-900 mb-10">
            Ответов ({{ $ticket->answers->count() }})
        </h2>

        @forelse($ticket->answers as $answer)
            <div class="border rounded p-2 p-lg-6 mb-10">
                <div class="mb-0">
                    <div class="d-flex flex-stack flex-wrap mb-5">
                        <div class="d-flex align-items-center py-1">
                            <div class="symbol symbol-35px me-3">
                                <img src="{{ $answer->createdBy()->first()->getPhoto() }}" alt="{{ $answer->createdBy()->first()->name }}">
                            </div>

                            <div class="d-flex flex-column align-items-start justify-content-center">
                                <span class="text-gray-800 fs-7 fw-semibold lh-1 mb-2">{{ $answer->createdBy()->first()->name }}</span>
                                <span class="text-muted fs-8 fw-semibold lh-1">{{ $answer->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center py-1">
                            <a href="#" class="btn btn-sm btn-flex btn-color-gray-500 btn-active-light me-1">
                                Ответить
                            </a>
                        </div>
                    </div>

                    <div class="fs-5 fw-normal text-gray-800">
                        {!! nl2br($answer->text) !!}
                    </div>

                    @include('helpdesk.tickets.partials._attachments', ['attachments' => $answer->getMedia()])
                </div>
                <div class="ps-10 mb-0">
                </div>
            </div>
        @empty
            <p>Ответов еще нет</p>
        @endforelse
    </div>
@endsection
