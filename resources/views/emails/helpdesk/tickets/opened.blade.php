<p>Добрый день!</p>
<p>Обращение #{{ $ticket->id }} было перенесено в разработку пользователем <strong>{{ $openedBy->name }}</strong>.</p>
@include('emails.helpdesk.tickets.partials._general_info')