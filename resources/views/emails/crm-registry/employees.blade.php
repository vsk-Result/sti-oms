<p>Уважаемый Дмитрий Владимирович <3</p>
<p>Хотим сообщимть вам, что при загрузке реестров для базы CRM из 1С произошли ошибки.</p>
<p>Простите нас пожалуйста, рабов системы.</p>
<p>Ошибки такие:</p>
<p>
    @foreach($errors as $error)
        <strong>В файле {{ $error['file'] }} не найдены: </strong>
        <ul>
            @foreach($error['employees'] as $employee)
                <li>{{ $employee['uid'] . ' - ' . $employee['name']}}</li>
            @endforeach
        </ul>
    @endforeach
</p>
<p>Да прибудет с вами Дмитрий Елисеев.</p>
<p>Хорошего дня сенсей!</p>