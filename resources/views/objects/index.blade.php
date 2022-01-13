@extends('layouts.app')

@section('toolbar-title', 'Объекты')
@section('breadcrumbs', Breadcrumbs::render('objects.index'))

@section('content')
    <div class="post" id="kt_post">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex flex-wrap flex-stack pb-7">
                    <div class="d-flex flex-wrap align-items-center position-relative my-1">
                        <span class="svg-icon svg-icon-3 position-absolute ms-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black"></rect>
                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black"></path>
                            </svg>
                        </span>
                        <input autofocus id="object-search" type="text" class="form-control form-control-sm form-control-solid w-300px ps-10" placeholder="Поиск">
                    </div>

                    <div class="d-flex flex-wrap justify-content-end" data-kt-user-table-toolbar="base">
                        @can('create objects')
                            <a href="{{ route('objects.create') }}" class="btn btn-light-primary">
                            <span class="svg-icon svg-icon-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                    <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>
                                    <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                                </svg>
                            </span>
                                Новый объект
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div id="objects-container" data-objects-index-url="{{ route('objects.index') }}" class="min-h-100px"></div>
    </div>
@endsection

@push('scripts')
    <script>
        const $filterSearch = $('#object-search');
        const $objectsContainer = $('#objects-container');
        const objectsContainerblockUI = new KTBlockUI($objectsContainer.get(0), {
            message: '<div class="blockui-message"><span class="spinner-border text-primary"></span> Загрузка объектов...</div>',
        });

        $(function() {
            loadObjects();
        });

        $(document).on('click', '.page-link', function(e) {
            loadObjects($(this).attr('href').split('=').pop());
            return false;
        });

        let delayTimer;
        $filterSearch.on('keyup', function() {
            clearTimeout(delayTimer);
            delayTimer = setTimeout(function() {
                filterObjects();
            }, 400);
        });

        function filterObjects() {
            loadObjects();
        }

        function loadObjects(page) {
            $objectsContainer.html('');
            page = page || '';
            objectsContainerblockUI.block();
            mainApp.sendAJAX(
                $objectsContainer.data('objects-index-url') + '?q=' + $filterSearch.val() + '&page=' + page,
                'GET',
                {},
                (data) => {
                    $objectsContainer.html(data.objects_view);
                },
                {},
                () => {
                    objectsContainerblockUI.release();
                }
            )
        }
    </script>
@endpush
