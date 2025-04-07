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
                            $hasManualUpload = false;
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
                                    @php
                                        if ($sourceInfo['source_name'] === 'Из Excel таблицы ручного обновления') {
                                            $hasManualUpload = true;
                                        }
                                    @endphp
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

            <div class="modal-footer justify-content-between">
                @if ($hasManualUpload)
                    <a
                            class="btn btn-light-primary me-3"
                            href="{{ route('debt_imports.manual_replace.reset.store', $object) }}"
                    >
                        Сбросить изменения долгов, обновленных вручную
                    </a>
                @endif
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
