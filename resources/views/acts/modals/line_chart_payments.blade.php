<div
    class="modal fade"
    tabindex="-1"
    id="lineChartPaymentActModal"
    data-rub-chart-months="{{ json_encode($actsPaymentsLineChartInfo['rub_months'] ?? []) }}"
    data-rub-chart-amounts="{{ json_encode($actsPaymentsLineChartInfo['rub_amounts'] ?? []) }}"
    data-eur-chart-months="{{ json_encode($actsPaymentsLineChartInfo['eur_months'] ?? []) }}"
    data-eur-chart-amounts="{{ json_encode($actsPaymentsLineChartInfo['eur_amounts'] ?? []) }}"
>
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">График выполнения по актам по месяцам</h4>
            </div>

            <div class="modal-body">
                <ul class="nav nav-pills nav-pills-custom mb-3 mx-9">
                    <li class="nav-item mb-3 me-3 me-lg-6">
                        <a style="padding-top: 0.65rem!important;" class="nav-link btn btn-outline btn-outline-dashed btn-outline-default flex-column overflow-hidden w-80px h-40px pb-2 active" data-bs-toggle="pill" href="#tab_pade_line_chart_payment_act_rub">
                            <span class="nav-text text-gray-800 fw-bolder fs-6 lh-1">RUB</span>
                        </a>
                    </li>
                    <li class="nav-item mb-3 me-3 me-lg-6">
                        <a style="padding-top: 0.65rem!important;" class="nav-link btn btn-outline btn-outline-dashed btn-outline-default flex-column overflow-hidden w-80px h-40px pb-2" data-bs-toggle="pill" href="#tab_pade_line_chart_payment_act_eur">
                            <span class="nav-text text-gray-800 fw-bolder fs-6 lh-1">EUR</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content ps-4 pe-6">
                    <div class="tab-pane fade active show" id="tab_pade_line_chart_payment_act_rub">
                        <div id="lineChartPaymentActRUB" style="height: 450px;"></div>
                    </div>

                    <div class="tab-pane fade" id="tab_pade_line_chart_payment_act_eur">
                        <div id="lineChartPaymentActEUR" style="height: 450px;"></div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>
