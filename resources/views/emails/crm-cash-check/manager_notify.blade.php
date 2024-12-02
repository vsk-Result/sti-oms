<p>Добрый день!</p>
<p>Поступила заявка на закрытие периода {{ $check->getFormattedPeriod() }} от пользователя {{ $check->crmUser->name }}.</p>
<p>Заявку можно проверить  <a target="_blank" href="{{ config('app.url') }}/crm-cash-check">данной ссылке</a>.</p>
<p>Спасибо!</p>
