<div class="d-flex flex-row gap-2 mb-3">
    <div title="Приоритет" class="fs-7 badge badge-outline badge-light px-4 py-2 me-2 fw-bold text-{{ $ticket->getPriority()->color }}">
        <i class="fa fa-exclamation-circle me-2 text-{{ $ticket->getPriority()->color }}"></i> {{ $ticket->getPriority()->name }}
    </div>

    @if (!empty($ticket->time_to_complete))
        <div title="Время на выполнение" class="fs-7 badge badge-outline badge-light px-4 py-2 me-2 fw-bold">
            <i class="fa fa-clock me-2"></i> {{ $ticket->time_to_complete }}
        </div>
    @endif

    @if ($ticket->haveUnreadUpdates() && isset($isPreview))
        <div title="Есть непрочитанные изменения" class="fs-7 badge badge-success badge-light px-4 py-2 me-2 fw-bold">
            Есть непрочитанные изменения
        </div>
    @endif
</div>