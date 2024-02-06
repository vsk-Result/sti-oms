<p>Добрый день!</p>
<p>Обращение #{{ $ticket->id }} было отмечено как выполнено пользователем <strong>{{ $closedBy->name }}</strong>.</p>
@include('emails.helpdesk.tickets.partials._general_info')