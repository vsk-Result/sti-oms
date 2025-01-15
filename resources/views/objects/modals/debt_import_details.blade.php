<div class="modal fade" tabindex="-1" id="debtsImportDetailsModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Источники загрузки долгов</h4>
            </div>

            <input type="hidden" name="object_id" class="form-control" value="{{ $object->id }}" />

            <div class="modal-body">
                <table class="table table-row-dashed">
                    <thead>
                        <tr class="fw-bold fs-6">
                            <th>Тип и источники долга</th>
                            <th>Дата загрузки</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sourceInfo = [
                                'Долг подрядчикам' => $contractorDebts['sources'],
                                'Долг поставщикам' => $providerDebts['sources'],
                                'Долг за услуги' => $serviceDebts['sources'],
                            ];
                        @endphp

                        @foreach($sourceInfo as $title => $sources)
                            @if (count($sources) > 0)
                                <tr class="fw-bolder" style="background-color: #f7f7f7;">
                                    <td class="ps-2">{{ $title }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>

                                @foreach($sources as $sourceInfo)
                                    <tr>
                                        <td class="ps-8">{{ $sourceInfo['source_name'] }}</td>
                                        <td>{{ $sourceInfo['uploaded_date'] }}</td>
                                        <td>
                                            <a href="/storage/{{ $sourceInfo['filepath'] }}">Скачать</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
