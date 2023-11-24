<p>Добрый день!</p>
<p>Обращение #{{ $ticket->id }} было закрыто пользователем <strong>{{ $closedBy->name }}</strong>.</p>
@include('emails.helpdesk.tickets.partials._general_info')