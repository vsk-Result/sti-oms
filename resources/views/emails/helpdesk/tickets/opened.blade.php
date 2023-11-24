<p>Добрый день!</p>
<p>Обращение #{{ $ticket->id }} было открыто повторно пользователем <strong>{{ $openedBy->name }}</strong>.</p>
@include('emails.helpdesk.tickets.partials._general_info')