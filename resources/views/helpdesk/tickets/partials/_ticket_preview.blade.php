<div class="ticket-preview">
    @include('helpdesk.tickets.partials._info_badges', ['ticket' => $ticket, 'isPreview' => true])

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="w-100 d-flex flex-row justify-content-between">
            <a href="{{ route('helpdesk.tickets.show', $ticket) }}" class="fs-3 fw-bolder text-gray-900 text-hover-primary me-1 ticket-preview-title">
                {{ $ticket->getPreviewTitle() }}

                @include('helpdesk.tickets.partials._menu_buttons', compact('ticket'))
            </a>
        </div>
    </div>

    @if (!empty($ticket->complete_date))
        <div class="alert alert-dismissible bg-light-success d-flex flex-column flex-sm-row p-3 mb-10">
            <div class="d-flex flex-column pe-0 pe-sm-10">
                <span><strong class="fs-6">Выполнено: </strong>{{ \Carbon\Carbon::parse($ticket->complete_date)->format('d.m.Y H:i') }}</span>
            </div>
        </div>
    @endif

    <div class="fs-base fw-normal text-gray-700 mb-4">
        {!! nl2br($ticket->getPreviewContent()) !!}
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
            <div class="badge badge-outline badge-light px-4 py-2 me-2 fw-bold">
                {{ $ticket->getAnswersCountPlural() }}
            </div>
        </div>
    </div>
</div>