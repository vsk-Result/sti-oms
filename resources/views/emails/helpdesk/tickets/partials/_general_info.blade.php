<p>Номер обращения: #{{ $ticket->id }}</p>
<p>Тема обращения: <strong>{{ $ticket->title }}</strong></p>
<p>Создал(а): {{ $ticketAuthor->name }} (<a href="mailto: {{ $ticketAuthor->email }}">{{ $ticketAuthor->email }}</a>)</p>
<p>Объект: {{ $ticket->getObjectName() }}</p>
<p>Приоритет: {{ $ticket->getPriority()->name }}</p>

@if (! isset($deletedBy))
    <p><a href="{{ route('helpdesk.tickets.show', $ticket) }}">Перейдите по ссылке, чтобы посмотреть полную информацию</a></p>
@endif

@if ($ticket->getPriority()->name === 'Высокий')
    <p style="color: red; font-weight: bold;">Обратите внимание, обращение имеет высокий приоритет</p>
@endif

<p>Хорошего дня!</p>