@extends('layouts.app')

@section('toolbar-title', 'Служба поддержки - Обращения')
@section('breadcrumbs', Breadcrumbs::render('helpdesk.tickets.index'))

@section('content')
    <div class="row h-100">
        <div class="col-md-2">
            <div class="aside w-100">
                <div class="menu menu-column menu-active-bg menu-hover-bg menu-title-gray-700 fs-6 menu-rounded w-100">
                    <a href="{{ route('helpdesk.tickets.create') }}" class="btn btn-icon-primary btn-light-primary py-1">
                        <i class="fa fa-plus fs-6"><span class="path1"></span><span class="path2"></span></i>
                        Новое обращение
                    </a>

                    <div class="menu-item pt-5">
                        <div class="menu-content pb-2">
                            <span class="menu-section text-muted text-uppercase fs-7 fw-bold">Обращения</span>
                        </div>
                    </div>

                    <div class="menu-item">
                        <a href="{{ route('helpdesk.tickets.index', ['status_id' => [\App\Models\Status::STATUS_ACTIVE]]) }}" class="menu-link {{ request()->fullUrl() === route('helpdesk.tickets.index', ['status_id' => [\App\Models\Status::STATUS_ACTIVE]]) ? 'active' : '' }}">
                            <span class="menu-title">Открытые</span>
                            <span class="menu-badge">{{ $openTicketsCount }}</span>
                        </a>
                    </div>

                    <div class="menu-item">
                        <a href="{{ route('helpdesk.tickets.index', ['status_id' => [\App\Models\Status::STATUS_BLOCKED]]) }}" class="menu-link {{ request()->fullUrl() === route('helpdesk.tickets.index', ['status_id' => [\App\Models\Status::STATUS_BLOCKED]]) ? 'active' : '' }}">
                            <span class="menu-title">Закрытые</span>
                            <span class="menu-badge">{{ $closeTicketsCount }}</span>
                        </a>
                    </div>

                    <div class="menu-item pt-5">
                        <div class="menu-content pb-2">
                            <span class="menu-section text-muted text-uppercase fs-7 fw-bold">Приоритет</span>
                        </div>
                    </div>

                    @foreach($groupedByPriorities as $info)
                        <div class="menu-item">
                            <a href="{{ route('helpdesk.tickets.index', ['priority_id' => [$info['priority_id']]]) }}" class="menu-link {{ request()->fullUrl() === route('helpdesk.tickets.index', ['priority_id' => [$info['priority_id']]]) ? 'active' : '' }}">
                                <span class="menu-title">{{ $info['priority_name'] }}</span>
                                <span class="menu-badge">{{ $info['tickets_count'] }}</span>
                            </a>
                        </div>
                    @endforeach

                    @if (count($groupedByObjects) > 0)
                        <div class="menu-item pt-5">
                            <div class="menu-content pb-2">
                                <span class="menu-section text-muted text-uppercase fs-7 fw-bold">Объект</span>
                            </div>
                        </div>

                        @foreach($groupedByObjects as $info)
                            <div class="menu-item">
                                <a href="{{ route('helpdesk.tickets.index', ['object_id' => [$info['object_id']]]) }}" class="menu-link {{ request()->fullUrl() === route('helpdesk.tickets.index', ['object_id' => [$info['object_id']]]) ? 'active' : '' }}">
                                    <span class="menu-title">{{ $info['object_name'] }}</span>
                                    <span class="menu-badge">{{ $info['tickets_count'] }}</span>
                                </a>
                            </div>
                        @endforeach
                    @endif

                    @can('index admin-users')
                        @if (count($groupedByUsers) > 0)
                            <div class="menu-item pt-5">
                                <div class="menu-content pb-2">
                                    <span class="menu-section text-muted text-uppercase fs-7 fw-bold">Пользователи</span>
                                </div>
                            </div>

                            @foreach($groupedByUsers as $info)
                                <div class="menu-item">
                                    <a href="{{ route('helpdesk.tickets.index', ['user_id' => [$info['user_id']]]) }}" class="menu-link {{ request()->fullUrl() === route('helpdesk.tickets.index', ['user_id' => [$info['user_id']]]) ? 'active' : '' }}">
                                        <span class="menu-title">{{ $info['user_name'] }}</span>
                                        <span class="menu-badge">{{ $info['tickets_count'] }}</span>
                                    </a>
                                </div>
                            @endforeach
                        @endif
                    @endcan
                </div>
            </div>
        </div>

        <div id="ticket-preview-block" class="col-md-10 ps-6">
            @forelse($tickets as $ticket)
                @include('helpdesk.tickets.partials._ticket_preview', compact('ticket'))

                @if(!$loop->last)
                    <div class="separator separator-dashed border-gray-300 my-8"></div>
                @endif
            @empty
                <p class="text-start text-dark fw-bolder d-block my-4 fs-6">
                    Вы не создали ни одного обращения
                </p>
            @endforelse
        </div>
    </div>
@endsection

@push('scripts')
    <script>

    </script>
@endpush

@push('styles')
    <style>
        #ticket-preview-block {
            border-left: 1px dashed #e4e6ef;
        }

        .ticket-preview-title:hover {
            color: #f15a22 !important;
        }

        .menu-link:hover:not(.active) {
            transition: color 1s ease, background-color 1s ease !important;
            background-color: #F9F9F9 !important;
        }

        .menu-link:hover:not(.active) .menu-title {
            color: #f15a22 !important;
        }
    </style>
@endpush
