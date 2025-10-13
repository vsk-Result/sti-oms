@extends('layouts.app')

@section('title', 'Начисленные налоги')
@section('toolbar-title', 'Начисленные налоги')
@section('breadcrumbs', Breadcrumbs::render('accrued_taxes.index'))

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">Начисленные налоги</div>

                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                            <form action="{{ route('accrued_taxes.exports.store') }}" method="POST" class="hidden">
                                @csrf
                                <a
                                        href="javascript:void(0);"
                                        class="btn btn-light-success"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                >
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black"></rect>
                                            <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black"></path>
                                            <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4"></path>
                                        </svg>
                                    </span>
                                    Экспорт в Excel
                                </a>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-body py-3">
                    <div class="table-responsive freeze-table">
                        <table class="table table-bordered align-middle table-row-dashed fs-6 gy-3" data-update-url="{{ route('accrued_taxes.update') }}">
                            <thead>
                                <tr>
                                    <th rowspan="2" valign="middle" class="min-w-150px ps-2"></th>
                                    @foreach($dates as $year => $months)
                                        <th class="text-center" colspan="12">{{ $year }}</th>
                                    @endforeach
                                </tr>

                                <tr>
                                    @foreach($dates as $year => $months)
                                        @foreach($months as $month)
                                            <th class="text-center min-w-150px">{{ $month['month'] }}</th>
                                        @endforeach
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody class="text-gray-600 fw-bold fs-7">
                                @foreach($names as $name)
                                    <tr>
                                        <td class="ps-2">{{ $name }}</td>

                                        @foreach($dates as $year => $months)
                                            @foreach($months as $month)
                                                @php
                                                    $tax = $taxes->where('date', $month['date'])->where('name', $name)->first();
                                                    $amount = $tax ? $tax->amount : 0;

                                                    if ($amount == 0) {
                                                        $amount = '';
                                                    }
                                                @endphp

                                                <td>
                                                    <input
                                                        type="text"
                                                        value="{{ $amount }}"
                                                        class="amount-mask form-control form-control-sm form-control-solid db-field"
                                                        autocomplete="off"
                                                        data-name="{{ $name }}"
                                                        data-date="{{ $month['date'] }}"
                                                    />
                                                </td>
                                            @endforeach
                                        @endforeach
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

@push('styles')
    <style>
        .table td, .table th, .table tbody tr:last-child td {
            border: 1px solid #c8c8c8 !important;
            color: #3f4254;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(function() {
            mainApp.initFreezeTable(1);
        });

        $(document).on('focus', '.db-field', function() {
            $(this).data('initial-amount', $(this).val());
        });

        $(document).on('blur', '.db-field', function() {
            const $that = $(this);
            const name = $that.data('name');
            const date = $that.data('date');
            const amount = $that.val();
            const url = $('.table').data('update-url');

            if ($that.data('initial-amount') !== amount) {
                mainApp.sendAJAX(
                    url,
                    'POST',
                    {
                        name,
                        date,
                        amount,
                    }
                );
            }
        });
    </script>
@endpush