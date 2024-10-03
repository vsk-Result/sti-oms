<div class="modal fade" tabindex="-1" id="notificationsModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Уведомления</h4>
            </div>

            <div class="modal-body p-0">
                <ul class="nav nav-tabs flex-nowrap text-nowrap">
                    <li class="nav-item">
                        <a class="nav-link btn btn-active-light btn-color-gray-600 btn-active-color-primary rounded-bottom-0 active" data-bs-toggle="tab" href="#new">Новые</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-active-light btn-color-gray-600 btn-active-color-primary rounded-bottom-0" data-bs-toggle="tab" href="#history">История</a>
                    </li>
                    <li class="nav-item ms-auto">
                        <a href="{{ route('pivots.cash_flow.notifications.update') }}" class="nav-link btn btn-active-light btn-color-gray-600 btn-active-color-primary rounded-bottom-0">Отметить как прочитанное</a>
                    </li>
                </ul>

                <div class="tab-content p-4 pb-7 pt-0" id="myTabContent">
                    <div class="tab-pane fade show active" id="new" role="tabpanel">
                        @forelse($newNotifications->sortByDesc('created_date')->groupBy('created_date') as $createdDate => $groupedNotifications)
                            <p class="text-dark fw-bolder d-block m-0 fs-6 ps-3 text-center my-6">
                                <span class="badge badge-light-danger fs-7 fw-bolder">{{ \Carbon\Carbon::parse($createdDate)->format('d.m.Y') }}</span>
                            </p>

                            <div class="d-flex flex-column gap-4">
                                @foreach($groupedNotifications as $notification)
                                    <div class="ps-5 py-1 pe-2 text-gray-900 fw-semibold text-start" style="border-left: 3px solid {{ $notification->getEventStatusColor() }};">{{ $notification->name }}</div>
                                @endforeach
                            </div>
                        @empty
                            <p class="mt-6">Уведомлений нет</p>
                        @endforelse
                    </div>
                    <div class="tab-pane fade" id="history" role="tabpanel">
                        @forelse($historyNotifications->sortByDesc('created_date')->groupBy('created_date') as $createdDate => $groupedNotifications)
                            <p class="text-dark fw-bolder d-block m-0 fs-6 ps-3 text-center my-6">
                                <span class="badge badge-light-danger fs-7 fw-bolder">{{ \Carbon\Carbon::parse($createdDate)->format('d.m.Y') }}</span>
                            </p>

                            <div class="d-flex flex-column gap-4">
                                @foreach($groupedNotifications as $notification)
                                    <div class="ps-5 py-1 pe-2 text-gray-900 fw-semibold text-start" style="border-left: 3px solid {{ $notification->getEventStatusColor() }};">{{ $notification->name }}</div>
                                @endforeach
                            </div>
                        @empty
                            <p class="mt-6">Уведомлений нет</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
