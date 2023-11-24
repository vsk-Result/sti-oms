<p>Добрый день!</p>
<p>В системе ОМС появился новый ответ от пользователя <strong>{{ $answerAuthor->name }}</strong> на обращение, связанное с вами.</p>
<hr/>
<p>{{ $answer->text }}</p>
<hr/>
@include('emails.helpdesk.tickets.partials._general_info')