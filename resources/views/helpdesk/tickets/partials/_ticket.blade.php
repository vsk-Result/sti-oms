<div class="mb-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex flex-column">
            <a href="{{ route('helpdesk.tickets.show', $ticket) }}" class="fs-2 fw-bold text-gray-900 text-hover-primary me-1">
                {{ $ticket->getPreviewTitle() }}
            </a>
            <span class="text-gray-400 fw-bold">
                Статус: <span class="text-success">Открыто</span>,
                Приоритет: <span class="text-{{ $ticket->getPriority()->color }}">{{ $ticket->getPriority()->name }}</span>
            </span>
        </div>

        <div class="d-flex align-items-center">
            <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots" viewBox="0 0 16 16">
                    <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
                </svg>
            </a>
            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-250px py-4" data-kt-menu="true">
                <div class="menu-item px-3">
                    <a href="{{ route('helpdesk.tickets.edit', $ticket) }}" class="menu-link px-3">Изменить</a>
                </div>
                <div class="menu-item px-3">
                    <form action="{{ route('helpdesk.tickets.destroy', $ticket) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                        <a
                                href="#"
                                class="menu-link px-3 text-danger"
                                onclick="event.preventDefault(); if (confirm('Вы действительно хотите удалить обращение?')) {this.closest('form').submit();}"
                        >
                            Удалить
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="fs-base fw-normal text-gray-700 mb-4">
        {!! nl2br($ticket->getPreviewContent()) !!}

        <br />
        @include('helpdesk.tickets.partials._attachments', ['attachments' => $ticket->getMedia()])
    </div>

    <div class="d-flex flex-stack flex-wrap">
        <div class="d-flex align-items-center py-1">
            <div class="symbol symbol-35px me-2">
                <div class="symbol symbol-35px me-2">
                    <img src="{{ $ticket->createdBy()->first()->getPhoto() }}" alt="{{ $ticket->createdBy()->first()->name }}">
                </div>
            </div>

            <div class="d-flex flex-column align-items-start justify-content-center">
                <span class="text-gray-900 fs-7 fw-semibold lh-1 mb-2">{{ $ticket->createdBy()->first()->name }}</span>
                <span class="text-muted fs-8 fw-semibold lh-1">{{ $ticket->created_at->diffForHumans() }}</span>
            </div>
        </div>

        <div class="d-flex align-items-center py-1">
            <a href="{{ route('helpdesk.tickets.show', $ticket) }}" class="btn btn-sm btn-outline btn-outline-dashed btn-outline-default px-4 me-2">
                {{ $ticket->getAnswersCountPlural() }}
            </a>
        </div>
    </div>
</div>