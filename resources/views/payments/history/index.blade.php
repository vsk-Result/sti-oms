@extends('layouts.app')

@section('toolbar-title', 'История оплат')
@section('breadcrumbs', Breadcrumbs::render('payments.history.index'))

@section('content')
    @include('payments.history.modals.filter')

    <div class="post">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-6">
                <div class="card-title"></div>

                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                        <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#filterPaymentHistoryModal">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                                </svg>
                            </span>
                            Фильтр
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-row-dashed fs-6">
                        <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-150px">Дата изменения</th>
                                <th class="min-w-150px">Пользователь</th>
                                <th class="min-w-150px">Тип</th>
                                <th class="min-w-150px">Оплата</th>
                                <th class="min-w-150px">Поле</th>
                                <th class="min-w-200px">Старое значение</th>
                                <th class="min-w-200px">Новое значение</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            @forelse($audits as $audit)
                                @foreach($audit->getModified() as $fieldName => $changes)
                                    @php
                                        if (request()->get('list_fields')) {
                                            foreach (request()->get('list_fields') as $f) {
                                                $fieldsArray[$f] = '';
                                            }
                                        } else {
                                            $fieldsArray = $fields;
                                        }
                                    @endphp
                                    @if (array_key_exists($fieldName, $fieldsArray))
                                        <tr>
                                            <td>{{ $audit->created_at->format('d.m.Y H:i:s') }}</td>
                                            <td>{{ $audit->user->name }}</td>
                                            <td>{{ $events[$audit->event] }}</td>
                                            <td>
                                                @if ($audit->auditable)
                                                    <a href="{{ route('payments.edit', $audit->auditable) }}">Оплата #{{ $audit->auditable->id }}</a>
                                                @else
                                                    Оплата #{{ $audit->auditable_id }}
                                                @endif
                                            </td>
                                            @if ($audit->event !== 'updated')
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                @break
                                            @else
                                                <td>{{ $fields[$fieldName] }}</td>
                                                <td>
                                                    @if ($fieldName === 'object_id' && isset($changes['old']))
                                                        {{ $objects[$changes['old']] }}
                                                    @elseif ($fieldName === 'type_id' && isset($changes['old']))
                                                        {{ $types[$changes['old']] }}
                                                    @elseif ($fieldName === 'status_id' && isset($changes['old']))
                                                        {{ $statuses[$changes['old']] }}
                                                    @elseif ($fieldName === 'object_worktype_id' && isset($changes['old']))
                                                        {{ $workTypes[$changes['old']] }}
                                                    @elseif ($fieldName === 'company_id' && isset($changes['old']))
                                                        {{ $companies[$changes['old']] }}
                                                    @elseif ($fieldName === 'bank_id' && isset($changes['old']))
                                                        {{ $banks[$changes['old']] }}
                                                    @elseif (($fieldName === 'organization_sender_id' || $fieldName === 'organization_receiver_id') && isset($changes['old']))
                                                        {{ $organizations[$changes['old']] }}
                                                    @elseif ($fieldName === 'amount'))
                                                        {{ number_format($changes['old'] ?? 0, 2, '.', ' ') }}
                                                    @else
                                                        {{ $changes['old'] ?? '' }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($fieldName === 'object_id' && isset($changes['new']))
                                                        {{ $objects[$changes['new']] }}
                                                    @elseif ($fieldName === 'type_id' && isset($changes['new']))
                                                        {{ $types[$changes['new']] }}
                                                    @elseif ($fieldName === 'status_id' && isset($changes['new']))
                                                        {{ $statuses[$changes['new']] }}
                                                    @elseif ($fieldName === 'object_worktype_id' && isset($changes['new']))
                                                        {{ $workTypes[$changes['new']] }}
                                                    @elseif ($fieldName === 'company_id' && isset($changes['new']))
                                                        {{ $companies[$changes['new']] }}
                                                    @elseif ($fieldName === 'bank_id' && isset($changes['new']))
                                                        {{ $banks[$changes['new']] }}
                                                    @elseif (($fieldName === 'organization_sender_id' || $fieldName === 'organization_receiver_id') && isset($changes['new']))
                                                        {{ $organizations[$changes['new']] }}
                                                    @elseif ($fieldName === 'amount'))
                                                        {{ number_format($changes['new'] ?? 0, 2, '.', ' ') }}
                                                    @else
                                                        {{ $changes['new'] ?? '' }}
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endif
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <p class="text-center text-dark fw-bolder d-block my-4 fs-6">
                                            История отсутствуют
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $audits->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection



