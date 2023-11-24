@if (auth()->user()->hasRole('super-admin') || auth()->id() === $ticket->created_by_user_id)
    <div class="d-flex align-items-baseline">
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
                @if ($ticket->isClosed())
                    <form action="{{ route('helpdesk.tickets.open', $ticket) }}" method="POST" class="hidden">
                        @csrf
                        <a
                                href="#"
                                class="menu-link px-3 text-success"
                                onclick="event.preventDefault(); if (confirm('Вы действительно хотите открыть обращение?')) {this.closest('form').submit();}"
                        >
                            Открыть
                        </a>
                    </form>
                @endif

                @if ($ticket->isOpened())
                    <form action="{{ route('helpdesk.tickets.close', $ticket) }}" method="POST" class="hidden">
                        @csrf
                        <a
                                href="#"
                                class="menu-link px-3 text-warning"
                                onclick="event.preventDefault(); if (confirm('Вы действительно хотите закрыть обращение?')) {this.closest('form').submit();}"
                        >
                            Закрыть
                        </a>
                    </form>
                @endif
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
@endif