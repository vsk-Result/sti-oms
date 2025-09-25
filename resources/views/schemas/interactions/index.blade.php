@extends('layouts.app')

@section('title', 'Схема взаимодействия')
@section('toolbar-title', 'Схема взаимодействия')
@section('breadcrumbs', Breadcrumbs::render('schemas.interactions.index'))

@section('content')
    <button class="btn btn-primary" id="download-svg">Скачать в PDF</button>

    <div style="width: fit-content;" id="svg-container" class="interactions" data-get-interactions-url="{{ route('schemas.interactions.index') }}">
        {!! file_get_contents('images/schemas/interactions.svg') !!}
    </div>
    <div class="update-interactions" data-get-interactions-table-url="{{ route('schemas.interactions.edit') }}" data-can-edit="{{ auth()->user()->can('edit schema-interactions') ? 'true' : 'false' }}"></div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>

    <script>
        const getInteractionsUrl = $('.interactions').data('get-interactions-url');
        const getInteractionsTableUrl = $('.update-interactions').data('get-interactions-table-url');
        const canEditInteraction = $('.update-interactions').data('can-edit');

        const customOffsets = new Map();
        customOffsets.set('СТИ_НП', {margin: 75, padding: 5});
        customOffsets.set('ПТИ_НП', {margin: 50, padding: 5});
        customOffsets.set('БАМС_НП', {margin: 70, padding: 0});
        customOffsets.set('ДТГ_НП', {margin: 75, padding: 5});
        customOffsets.set('ПТИ-БАМС_Аренда офиса', {margin: 90, padding: 0});
        customOffsets.set('ДТГ-Любомир_Премия', {margin: 74, padding: 20});
        customOffsets.set('ДТГ-Belenzia_Поставка', {margin: 95, padding: 10});
        customOffsets.set('ДТГ-Maviboni_Поставка', {margin: 95, padding: 15});
        customOffsets.set('ДТГ-NS_Поставxка', {margin: 50, padding: 10});
        customOffsets.set('Maviboni-Прохорова_%', {margin: 80, padding: 40});
        customOffsets.set('Maviboni-NS_Поставка', {margin: 80, padding: 5});
        customOffsets.set('NS-Maviboni_Поставка', {margin: 75, padding: 0});
        customOffsets.set('Belenzia-Maviboni_Переуступка', {margin: 70, padding: 0});
        customOffsets.set('Belenzia-Milenium_Переуступка_1', {margin: 65, padding: 25});
        customOffsets.set('Belenzia-Milenium_Переуступка_2', {margin: 55, padding: 25});
        customOffsets.set('СТИ-Belenzia_Поставка', {margin: 65, padding: 0});
        customOffsets.set('БАМС-ПТИ_Аренда авто', {margin: 65, padding: 0});

        $(function() {
            updateInteractions();
        });

        function updateInteractions() {
            mainApp.sendAJAX(
                getInteractionsUrl,
                'GET',
                {},
                (data) => {
                    data.interactions.forEach(({name, amount}) => {
                        const $textDiv = $(`.interactions div:contains(${name})`);

                        if ($textDiv) {
                            const $parent = $textDiv.parent();
                            const originText = name.substring(name.indexOf('_') + 1);
                            const originTextDiv = originText === 'НП' ? '' : `<div>${originText}</div>`;

                            const paddingOffset = customOffsets.has(name) ? customOffsets.get(name).padding : 15;
                            const marginOffset = customOffsets.has(name) ? customOffsets.get(name).margin : 80;

                            $parent.css('height', '100px');
                            $parent.css('width', '150px');
                            $parent.css('flex-direction', 'column');
                            $parent.css('align-items', 'center');

                            $textDiv.text('');
                            $textDiv.css('padding-top', (parseInt($textDiv.css('padding-top')) - paddingOffset) + 'px');
                            $textDiv.css('margin-left', (parseInt($textDiv.css('margin-left')) - marginOffset) + 'px');
                            $textDiv.css('font-size', '11px');
                            $textDiv.append(`${originTextDiv}<div>${amount}</div>`);
                        }
                    });

                    if (canEditInteraction) {
                        data.companies.forEach((company) => {
                            const $companyDiv = $(`.interactions div:contains(${company})`);

                            if ($companyDiv) {
                                $companyDiv.first().css('cursor', 'pointer');
                                $companyDiv.first().on('click', function(e) {
                                    getInteractionsTable(company);
                                    $("html, body").animate({ scrollTop: $(document).height() }, 1000);
                                });
                            }
                        });
                    }
                },
            )
        }

        function getInteractionsTable(company) {
            mainApp.sendAJAX(
                getInteractionsTableUrl,
                'GET',
                {
                    company
                },
                (data) => {
                    $('.update-interactions').html(data.interactions_table);
                },
            )
        }

        $(function() {
            window.jsPDF = window.jspdf.jsPDF;

            $('#download-svg').on('click', function () {
                const element = document.getElementById('svg-container');
                const options = { scale: 2 };

                html2canvas(element, options).then(canvas => {
                    var imgData = canvas.toDataURL('image/png'); // Конвертируем canvas в PNG
                    let pdf = window.jsPDF({ orientation: 'l', unit: 'mm', format: 'a4'});

                    const w = pdf.internal.pageSize.getWidth() - 20;
                    const h = (w / canvas.width) * canvas.height;

                    pdf.addImage(imgData, 'PNG', 10, 10, w, h);
                    pdf.save("Схема взаимодействия.pdf");
                });
            })
        })
    </script>
@endpush
