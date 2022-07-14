@extends('layouts.app')

@section('toolbar-title', 'Долги к СТИ')
@section('breadcrumbs', Breadcrumbs::render('pivots.acts.index'))

@section('content')
    <div class="card mb-5 mb-xl-8">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
            </div>

            <div class="card-toolbar">
            </div>
        </div>
        <div class="card-body py-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle table-row-dashed fs-7">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-100px ps-4">Объект</th>
                            <th class="min-w-150px">По актам</th>
                            <th class="min-w-150px">Не закрытый аванс</th>
                            <th class="min-w-150px">Гарантийное удержание</th>
                        </tr>
                        <tr class="fw-bolder" style="background-color: #f7f7f7;">
                            <th class="ps-4" style="vertical-align: middle;">Итого</th>
                            <th>
                                {{ \App\Models\CurrencyExchangeRate::format($pivot['total']['acts']['RUB'], 'RUB') }}
                                <br>
                                {{ \App\Models\CurrencyExchangeRate::format($pivot['total']['acts']['EUR'], 'EUR') }}
                            </th>
                            <th>
                                {{ \App\Models\CurrencyExchangeRate::format($pivot['total']['avanses']['RUB'], 'RUB') }}
                                <br>
                                {{ \App\Models\CurrencyExchangeRate::format($pivot['total']['avanses']['EUR'], 'EUR') }}
                            </th>
                            <th>
                                {{ \App\Models\CurrencyExchangeRate::format($pivot['total']['gu']['RUB'], 'RUB') }}
                                <br>
                                {{ \App\Models\CurrencyExchangeRate::format($pivot['total']['gu']['EUR'], 'EUR') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @forelse($pivot['entries'] as $entry)
                            <tr>
                                <td class="ps-4">
                                    @if(auth()->user()->can('show objects'))
                                        <a target="_blank" href="{{ route('objects.show', $entry['object']['id']) }}/acts?object_id%5B%5D={{ $entry['object']['id'] }}" class="show-link">{{ $entry['object']['name'] }}</a>
                                    @else
                                        {{ $entry['object']['name'] }}
                                    @endif
                                </td>
                                <td>
                                    <span class="{{ $entry['acts']['RUB'] > 0 ? 'text-success fw-bolder' : '' }}">{{ \App\Models\CurrencyExchangeRate::format($entry['acts']['RUB'], 'RUB') }}</span>
                                    <br>
                                    <span class="{{ $entry['acts']['EUR'] > 0 ? 'text-success fw-bolder' : '' }}">{{ \App\Models\CurrencyExchangeRate::format($entry['acts']['EUR'], 'EUR') }}</span>
                                </td>
                                <td>
                                    <span class="{{ $entry['avanses']['RUB'] > 0 ? 'text-success fw-bolder' : '' }}">{{ \App\Models\CurrencyExchangeRate::format($entry['avanses']['RUB'], 'RUB') }}</span>
                                    <br>
                                    <span class="{{ $entry['avanses']['EUR'] > 0 ? 'text-success fw-bolder' : '' }}">{{ \App\Models\CurrencyExchangeRate::format($entry['avanses']['EUR'], 'EUR') }}</span>
                                </td>
                                <td>
                                    <span class="{{ $entry['gu']['RUB'] > 0 ? 'text-success fw-bolder' : '' }}">{{ \App\Models\CurrencyExchangeRate::format($entry['gu']['RUB'], 'RUB') }}</span>
                                    <br>
                                    <span class="{{ $entry['gu']['EUR'] > 0 ? 'text-success fw-bolder' : '' }}">{{ \App\Models\CurrencyExchangeRate::format($entry['gu']['EUR'], 'EUR') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <p class="text-center text-dark fw-bolder d-block my-4 fs-6">
                                        Данные отсутствуют
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
