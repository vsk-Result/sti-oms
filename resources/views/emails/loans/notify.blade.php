<p>Дата оплаты: {{ $payment->date }}</p>
<p>Описание оплаты: {{ $payment->description }}</p>
<p>Тег совпадения: <strong>{{ $tag->tag }}</strong></p>
<p>Номер {{$tag->loan->type_id === \App\Models\Loan::TYPE_CREDIT ? 'кредита' : 'займа'}}: {{ $tag->loan->name }}</p>