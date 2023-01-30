<p>{{ $mes }}</p>
<p>Срок действия: {{ \Carbon\Carbon::parse($info['date'])->format('d.m.y') }}</p>
<p>Номер банковской гарантии: {{ $info['number'] }}</p>
<p><strong>Банковская гарантия была перемещена в архив</strong></p>
<p><a href="{{ route('bank_guarantees.edit', $info['id']) }}">Ссылка на изменение банковской гарантии в ОМС</a></p>