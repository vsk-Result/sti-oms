@extends('layouts.app')

@section('title', 'Статус загруженных файлов по долгам объектов')
@section('toolbar-title', 'Статус загруженных файлов по долгам объектов')
@section('breadcrumbs', Breadcrumbs::render('upload_debts_status.index'))

@section('content')
    @include('upload-debts-status.modals.upload_manual')

    <div class="post">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-dismissible bg-light-primary d-flex flex-column flex-sm-row p-5 mb-10">
                    <i class="ki-duotone ki-search-list fs-2hx text-success me-4 mb-5 mb-sm-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    <div class="d-flex flex-column pe-0 pe-sm-10">
                        <h5 class="mb-1">Важно прочитать</h5>
                        <span>После обновления файла, данные о долгах изменятся в течение 10-15 минут. Если этого не произошло, обратитесь по адресу <a href="mailto:result007@yandex.ru" alt="result007@yandex.ru">result007@yandex.ru</a></span>
                    </div>
                </div>

                <div class="card mb-5 mb-xl-8">
                    <div class="card-body py-3">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-125px">Название файла</th>
                                <th class="min-w-125px">Дата последней загрузки автоматически</th>
                                <th class="min-w-125px">Дата последней загрузки вручную</th>
                                <th class="min-w-125px"></th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @foreach($items as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item['name'] }}</strong>
                                            <p class="text-muted fs-8">{{ $item['file']}}</p>
                                        </td>
                                        <td>
                                            @if (is_null($item['auto_date']))
                                                <span class="text-danger">Файл не найден</span>
                                            @else
                                                {{ $item['auto_date']->diffForHumans() }}
                                                <p class="text-muted fs-8">{{ $item['auto_date']->format('d.m.Y H:i') }}</p>
                                            @endif
                                        </td>
                                        <td>
                                            @if (is_null($item['manual_date']))
                                                -
                                            @else
                                                {{ $item['manual_date']->diffForHumans() }}
                                                <p class="text-muted fs-8">{{ $item['manual_date']->format('d.m.Y H:i') }}</p>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Действия
                                                <span class="svg-icon svg-icon-5 m-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                                </svg>
                                            </span>
                                            </a>
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-250px py-4" data-kt-menu="true">
                                                @if (!is_null($item['auto_date']))
                                                    <div class="menu-item px-3">
                                                        <a href="{{ $item['url'] }}" class="menu-link px-3">Скачать</a>
                                                    </div>
                                                @endif

                                                <div class="menu-item px-3">
                                                    <a href="javascript:void(0)" class="upload-manual menu-link px-3" data-name="{{ $item['name'] }}" data-file="{{ $item['file'] }}" data-command="{{ $item['command'] }}">Загрузить вручную</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('.upload-manual').on('click', function() {
            $('.debt-name').text(`${$(this).data('name')} (${$(this).data('file')})`);
            $('.upload-command').val($(this).data('command'));
            $('.upload-filename').val($(this).data('file'));

            $('#uploadManualModal').modal('show');
        });
    </script>
@endpush
