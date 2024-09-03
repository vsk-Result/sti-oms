@extends('layouts.app')

@section('title', 'Контрагенты')
@section('toolbar-title', 'Контрагенты')
@section('breadcrumbs', Breadcrumbs::render('organizations.index'))

@section('content')
    <div class="post" id="kt_post">
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex flex-wrap align-items-center position-relative my-1">
                            <span class="svg-icon svg-icon-3 position-absolute ms-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black"></rect>
                                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black"></path>
                                </svg>
                            </span>
                        <input autofocus id="organization-search" type="text" class="form-control form-control-sm form-control-solid w-300px ps-10" placeholder="Поиск">
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                        @can('create organizations')
                            <a href="{{ route('organizations.create') }}" class="btn btn-light-primary">
                                <span class="svg-icon svg-icon-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                        <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>
                                        <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                                    </svg>
                                </span>
                                Новый контрагент
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div id="organizations-container" data-organizations-index-url="{{ route('organizations.index') }}" class="min-h-100px"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const $filterSearch = $('#organization-search');
        const $organizationsContainer = $('#organizations-container');
        const organizationsContainerblockUI = new KTBlockUI($organizationsContainer.get(0), {
            message: '<div class="blockui-message"><span class="spinner-border text-primary"></span> Загрузка контрагентов...</div>',
        });

        $(function() {
            loadOrganizations();
        });

        $(document).on('click', '.page-link', function(e) {
            loadOrganizations($(this).attr('href').split('=').pop());
            return false;
        });

        let delayTimer;
        $filterSearch.on('keyup', function() {
            clearTimeout(delayTimer);
            delayTimer = setTimeout(function() {
                filterOrganizations();
            }, 400);
        });

        function filterOrganizations() {
            loadOrganizations();
        }

        function loadOrganizations(page) {
            $organizationsContainer.html('');
            page = page || '';
            organizationsContainerblockUI.block();
            mainApp.sendAJAX(
                $organizationsContainer.data('organizations-index-url') + '?search=' + $filterSearch.val() + '&page=' + page,
                'GET',
                {},
                (data) => {
                    $organizationsContainer.html(data.organizations_view);
                },
                {},
                () => {
                    organizationsContainerblockUI.release();
                    KTMenu.createInstances();
                }
            )
        }
    </script>
@endpush
