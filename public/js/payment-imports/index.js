$('#export-selected-imports').on('click', function () {
    $(this).closest('form').submit();
});

$('#check-all-imports').on('click', function () {
    $('.widget-9-check').prop('checked', $(this).prop('checked'));
    updateCount();
});

$('.widget-9-check').on('input', function() {
    updateCount();
});

function updateCount() {
    const $checkedInputs = $('.widget-9-check:checked');
    const count = $checkedInputs.length;

    if (count > 0) {
        $('#export-selected-imports').text('Экспорт выбранных оплат (' + count + ')').show();
    } else {
        $('#export-selected-imports').text('Экспорт выбранных оплат').hide();
    }

    let ids = [];
    $checkedInputs.each(function() {
        ids.push($(this).val())
    });

    $('#selected-imports-ids').val(JSON.stringify(ids));
}
