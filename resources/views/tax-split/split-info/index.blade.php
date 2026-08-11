<div class="row mb-8">
    <div class="col-md-12">
        <p class="fw-bolder text-dark fs-6">Данные из 1С</p>

        <div class="d-flex gap-2">
            <div class="d-grid">
                <ul class="nav nav-tabs flex-nowrap text-nowrap border-0">
                    @foreach($info as $month => $i)
                        <li class="nav-item">
                            <a class="nav-link btn btn-active-light btn-color-gray-600 btn-active-color-primary {{ $loop->last ? 'active' : '' }}" data-bs-toggle="tab" href="#tab-month-{{ $month }}">{{ translate_year_month_word(\Carbon\Carbon::parse($month)->format('F Y')) }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tab-content" id="myTabContent">
            @foreach($info as $month => $data)
                <div class="tab-pane fade {{ $loop->last ? 'show active' : '' }}" id="tab-month-{{ $month }}" role="tabpanel">
                    <table class="table table-hover align-middle table-row-dashed fs-6">
                        <thead>
                        <tr class="text-start fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-150px">Объект</th>
                            <th class="min-w-150px text-end">Сумма</th>
                        </tr>
                        </thead>
                        <tbody class="">
                            <tr>
                                <td>
                                    <p class="text-left text-dark fw-bolder d-block m-0 fs-6 ps-3">
                                        <span class="badge badge-light-danger fs-7 fw-bolder">НДФЛ</span>
                                    </p>
                                </td>
                                <td class="text-end fw-bolder">{{ \App\Models\CurrencyExchangeRate::format(array_sum($data['ndfl'])) }}</td>
                            </tr>

                            @foreach($data['ndfl'] as $code => $amount)
                                <tr class="fs-7">
                                    <td class="ps-3">{{ $code }}</td>
                                    <td class="text-end">{{  \App\Models\CurrencyExchangeRate::format($amount) }}</td>
                                </tr>
                            @endforeach

                            <tr>
                                <td>
                                    <p class="text-left text-dark fw-bolder d-block m-0 fs-6 ps-3">
                                        <span class="badge badge-light-danger fs-7 fw-bolder">Страховые взносы</span>
                                    </p>
                                </td>
                                <td class="text-end fw-bolder">{{ \App\Models\CurrencyExchangeRate::format(array_sum($data['strah'])) }}</td>
                            </tr>

                            @foreach($data['strah'] as $code => $amount)
                                <tr class="fs-7">
                                    <td class="ps-3">{{ $code }}</td>
                                    <td class="text-end">{{  \App\Models\CurrencyExchangeRate::format($amount) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </div>
</div>