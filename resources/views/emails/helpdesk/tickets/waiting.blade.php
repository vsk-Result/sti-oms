<p>Добрый день!</p>
<p>Обращение #{{ $ticket->id }} было перенесено в ожидание пользователем <strong>{{ $goWaitingBy->name }}</strong>.</p>
@include('emails.helpdesk.tickets.partials._general_info')