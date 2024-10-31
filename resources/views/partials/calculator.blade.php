<div id="calculator" class="flex-column p-5 position-fixed top-0 end-0 fs-4" style="display: none;">
    <div class="calculator-result text-end fs-1 pb-2">0.00</div>

    <div class="calculator-details fs-6 mt-4 d-flex flex-column gap-1"></div>

    <div class="calculator-buttons mt-4 justify-content-between d-flex flex-row">
        <button class="calculator-button-close btn btn-sm btn-light">Закрыть</button>
        <button class="calculator-button-clear btn btn-sm btn-light">Очистить</button>
    </div>
</div>

<div id="calculator-mini" class="position-fixed top-0 end-0 text-center fs-6">
    C
</div>

@push('scripts')
    <script>
        $(function() {
            const elementWithAmounts1 = [...document.querySelectorAll('.text-danger, .text-success, .fw-bolder')].filter(el => el.textContent.includes('₽'));
            const elementWithAmounts2 = [...document.querySelectorAll('td, th')].filter(el => el.textContent.includes('₽'));

            elementWithAmounts1.forEach(function(el) {
                if ($(el).children('.for-calc').length === 0 && ! $(el).hasClass('for-calc')) {
                    $(el).addClass('for-calc');
                }
            });

            elementWithAmounts2.forEach(function(el) {
                if ($(el).children('.for-calc').length === 0 && ! $(el).hasClass('for-calc')) {
                    $(el).addClass('for-calc');
                }
            });

            $(document).on('click', '.for-calc', function(e) {
                if ($(this).css('cursor') !== 'pointer') {
                    return true;
                }

                e.preventDefault();

                const amount = $(this).text().trim();
                const clearAmount = +amount.replace(/[^-.\d]+/g, "");
                const color = clearAmount < 0 ? 'danger' : 'success';

                const showOperator = $('.calculator-detail').length > 0;

                $('.calculator-details').append(`
                    <div class="calculator-detail d-flex flex-row justify-content-between gap-2 align-items-center">
                        <div class="calculator-detail-operator min-w-20px text-center cursor-pointer">${showOperator ? '+' : ''}</div>
                        <div class="calculator-detail-amount text-${color} cursor-pointer">${amount}</div>
                    </div>
                `);

                recalculate();
            });

            $(document).on('click', '#calculator-mini', function() {
                $(this).hide();
                $('#calculator').show();
                $('.for-calc').css('cursor', 'pointer');
                clear();
            });

            $(document).on('click', '.calculator-button-clear', function() {
                clear();
            });

            $(document).on('click', '.calculator-button-close', function() {
                clear();
                $('#calculator').hide();
                $('#calculator-mini').show();
                $('.for-calc').css('cursor', 'default');
            });

            $(document).on('click', '.calculator-detail-operator', function() {
                const operator = $(this).text();

                if (operator.length === 0) {
                    return;
                }

                $(this).text(operator === '+' ? '-' : '+');
                recalculate();
            });

            $(document).on('dblclick', '.calculator-detail-amount', function() {
                $(this).parent().remove();
                recalculate();
            });
        });

        function clear() {
            $('.calculator-result').text('0.00');
            $('.calculator-details').html('');
        }

        function recalculate() {
            let sum = 0;

            $('.calculator-detail').each(function() {
                const operator = $(this).find('.calculator-detail-operator').text();
                const amount = +$(this).find('.calculator-detail-amount').text().replace(/[^-.\d]+/g, "");

                if (operator === '+' || operator.length === 0) {
                    sum += amount;
                } else {
                    sum -= amount;
                }
            });

            sum = new Intl.NumberFormat("ru").format(sum);

            $('.calculator-result').text(sum);
        }
    </script>
@endpush