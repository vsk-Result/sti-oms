<div class="modal fade" tabindex="-1" id="lineChartPaymentActModal" data-chart-dates="{{ json_encode($actsMonths) }}" data-chart-data="{{ json_encode($RUBActsAmounts) }}">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">График выполнения по актам по месяцам</h4>
            </div>

            <div class="modal-body">
                <div id="lineChartPaymentAct" style="height: 350px;"></div>
            </div>

            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>
