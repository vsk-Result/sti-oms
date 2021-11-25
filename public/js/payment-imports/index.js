const $exportSelectedImportsBtn = $('#export-selected-imports');
const $checkboxInputs = $('.widget-9-check');

$exportSelectedImportsBtn.on('click', function () {
    $(this).closest('form').submit();
});

$('#check-all-imports').on('click', function () {
    $checkboxInputs.prop('checked', $(this).prop('checked'));
    updateCount();
});

$checkboxInputs.on('input', function() {
    updateCount();
});

function updateCount() {
    const $checkedInputs = $checkboxInputs.filter(':checked');
    const count = $checkedInputs.length;

    $exportSelectedImportsBtn.text('Экспорт выбранных оплат (' + count + ')').toggle(count > 0);

    let ids = [];
    $checkedInputs.each(function() {
        ids.push($(this).val());
    });

    $('#selected-imports-ids').val(JSON.stringify(ids));
}
