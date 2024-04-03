<h3 class="mb-8">Таблица взаимодействий компании {{ $company }}</h3>

<form action="{{ route('schemas.interactions.update') }}" method="POST" class="hidden">
    @csrf
    @foreach($interactions as $interaction)
        <div class="row mb-4">
            <input type="hidden" name="id[]" value="{{ $interaction->id }}" />
            <div class="col-md-2">
                <p class="mt-4">{{ $interaction->name }}</p>
            </div>
            <div class="col-md-3">
                <select name="currency[]" data-control="select2" class="form-select form-select-solid form-select-lg">
                    @foreach($currencies as $currency)
                        <option value="{{ $currency }}" {{ $currency === $interaction->currency ? 'selected' : '' }}>{{ $currency }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input
                    class="form-control form-control-lg form-control-solid"
                    type="text"
                    name="amount[]"
                    value="{{ number_format($interaction->amount, 0, '.', '') }}"
                    autocomplete="off"
                />
            </div>
        </div>
    @endforeach

    <button type="submit" class="btn btn-sm btn-primary mt-4">Сохранить</button>
</form>