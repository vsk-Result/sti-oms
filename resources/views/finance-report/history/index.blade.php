@extends('layouts.app')

@section('toolbar-title', 'История финансовых отчетов')
@section('breadcrumbs', Breadcrumbs::render('finance_report.history.index'))

@section('content')
    <div class="post" id="kt_post">
        <div class="card">
            <div class="card-body pt-0">
                <table class="table align-middle table-row-dashed fs-6 gy-5" >
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-125px">Дата отчета</th>
                            <th class="min-w-125px">Дата и время последнего обновления</th>
                            <th class="text-end min-w-100px">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @foreach($financeReportHistoryItems as $financeReportHistoryItem)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($financeReportHistoryItem->date)->format('d.m.Y') }}</td>
                                <td>{{ $financeReportHistoryItem->updated_at->format('d.m.Y H:i') }}</td>
                                <td class="text-end d-flex flex-row gap-2 justify-content-end">
                                    <a href="{{ route('finance_report.index', ['balance_date' => $financeReportHistoryItem->date]) }}" class="btn btn-light-dark">Посмотреть</a>

                                    <form action="{{ route('finance_report.exports.store', $financeReportHistoryItem->date) }}" method="POST" class="hidden">
                                        @csrf
                                        <a
                                                href="javascript:void(0);"
                                                class="btn btn-light-primary"
                                                onclick="event.preventDefault(); this.closest('form').submit();"
                                        >
                                            Экспорт в Excel
                                        </a>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {

        });
    </script>
@endpush

@push('styles')
    <style>

    </style>
@endpush
