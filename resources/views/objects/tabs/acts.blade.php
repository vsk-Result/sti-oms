@extends('objects.layouts.show')

@section('object-tab-title', 'Акты')

@section('object-tab-content')
    @include('acts.modals.filter')
    @include('acts.modals.line_chart_payments')

    <div class="row g-6 g-xl-9">
        <div class="col-lg-12">
            @if ($isShowGroupedActs)
                @include('acts.partials._acts_grouped')
            @else
                @include('acts.partials._acts')
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            mainApp.initFreezeTable(2);

            const url = new URL(document.location.href);
            const sortByField = url.searchParams.get('sort_by');
            const sortByDirection = url.searchParams.get('sort_direction');

            if (sortByField && sortByDirection) {
                const sortRow = $('th[data-sort-by=' + sortByField + ']');
                sortRow.removeClass('sorting-asc').removeClass('sorting-desc');
                sortRow.addClass('sorting-' + sortByDirection);
            }
        });

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

        $('.collapse-trigger').on('click', function() {
            const $tr = $(this);
            const trigger = $tr.data('trigger');
            const isCollapsed = $tr.hasClass('collapsed');

            if (isCollapsed) {
                $tr.text('+');
                $tr.removeClass('collapsed');
                $(`.collapse-row[data-trigger="${trigger}"]`).hide();
            } else {
                $tr.text('-');
                $tr.addClass('collapsed');
                $(`.collapse-row[data-trigger="${trigger}"]`).show();
            }
        })
    </script>
@endpush
