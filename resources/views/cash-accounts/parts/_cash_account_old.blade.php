<div class="col-xl-4">
    <a href="{{ route('cash_accounts.show', $cashAccount) }}">
        <div class="card card-xl-stretch mb-xl-8">
            <div class="card-body d-flex flex-column p-0 position-relative">

                @if ($cashAccount->sharedUsers->count() > 0)
                    <span class=" position-absolute end-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Доступна другим пользователям">
                                        <span class="btn btn-sm btn-icontranslate-middle" data-kt-password-meter-control="visibility">
                                            <i class="bi bi-eye fs-2"></i>
                                        </span>
                                    </span>
                @endif

                <div class="d-flex flex-stack flex-grow-1 card-p">
                    <div class="d-flex flex-column me-2">
                        <div class="text-gray-900 text-hover-primary fw-bold fs-3">
                            {{ $cashAccount->name }}

                            @if($cashAccountNotificationService->hasUnreadNotifications(auth()->user(), $cashAccount))
                                <i class="ms-3 fa fa-info-circle text-danger"></i>
                            @endif
                        </div>

                        <span class="text-muted fw-semibold mt-1">{{ $cashAccount->responsible?->name }}</span>

                        <div class="d-flex gap-1 flex-row mt-4">
                            @foreach($cashAccount->objects->sortBy('code') as $object)
                                <span class="badge badge-light">{{ $object->code }}</span>
                            @endforeach
                        </div>
                    </div>

                    <span class="symbol symbol-50px">
                                        <span style="width: 100px" class="symbol-label fs-5 fw-bold {{ $cashAccount->getBalance() < 0 ? 'bg-light-danger text-danger' : 'bg-light-success text-success' }}">{{ \App\Models\CurrencyExchangeRate::format($cashAccount->getBalance()) }}</span>
                                    </span>
                </div>
            </div>
        </div>
    </a>
</div>