@extends('layouts.app')

@section('title', 'Займы/Кредиты')
@section('toolbar-title', 'Займы/Кредиты')
@section('breadcrumbs', Breadcrumbs::render('loans.index'))

@section('content')
    @include('loans.modals.filter')
    <div class="post">
        @include('loans.parts._loans')
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            mainApp.initFreezeTable(1);

            const url = new URL(document.location.href);
            const sortByField = url.searchParams.get('sort_by');
            const sortByDirection = url.searchParams.get('sort_direction');

            if (sortByField && sortByDirection) {
                const sortRow = $('th[data-sort-by=' + sortByField + ']');
                sortRow.removeClass('sorting-asc').removeClass('sorting-desc');
                sortRow.addClass('sorting-' + sortByDirection);
            }

            $('.sortable-row').on('click', function(e) {
                e.preventDefault();
                const field = $(this).data('sort-by');
                const url = new URL(document.location.href);

                if (url.searchParams.has('sort_by')) {
                    url.searchParams.set('sort_by', field);
                } else {
                    url.searchParams.append('sort_by', field);
                }

                if (url.searchParams.has('sort_direction')) {
                    url.searchParams.set('sort_direction', url.searchParams.get('sort_direction') === 'asc' ? 'desc' : 'asc');
                } else {
                    url.searchParams.append('sort_direction', 'asc');
                }

                document.location = url.toString();
            });
        });
    </script>
@endpush