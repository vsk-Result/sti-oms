@if ($status === 'Активен' || $status === 'Заполнен')
    <span class="badge badge-success fw-bolder">{{ $status }}</span>
@elseif ($status === 'Недоступен' || $status === 'Не заполнен' || $status === 'В архиве')
    <span class="badge badge-primary fw-bolder">{{ $status }}</span>
@elseif ($status === 'Удален')
    <span class="badge badge-danger fw-bolder">{{ $status }}</span>
@endif

