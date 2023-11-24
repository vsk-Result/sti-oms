<p>Добрый день!</p>
<p>Обращение #{{ $ticket->id }} было удалено пользователем <strong>{{ $deletedBy->name }}</strong>.</p>
@include('emails.helpdesk.tickets.partials._general_info')