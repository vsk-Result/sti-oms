<div class="col-md-6 col-xl-4">
    <a href="{{ route('cash_accounts.show', $cashAccount) }}" class="card border-hover-sti">
        <div class="card-header border-0 pt-9">
            <div class="card-title m-0 d-flex gap-7">
                <div class="symbol symbol-50px w-50px bg-light">
                    @if ($cashAccount->responsible?->photo)
                        <img src="{{ $cashAccount->responsible?->getPhoto() }}" alt="{{ $cashAccount->responsible?->name }}">
                    @else
                        <span class="symbol-label fs-1 fw-semibold text-{{ $cashAccount->responsible?->getInitialColor() }} bg-light-{{ $cashAccount->responsible?->getInitialColor() }}">{{ $cashAccount->responsible?->getInitials() }}</span>
                    @endif
                </div>

                <div class="d-flex flex-column justify-content-center">
                    <div class="fs-3 fw-bold text-gray-900">
                        {{ $cashAccount->name }}
                    </div>

                    <p class="text-gray-500 fw-semibold fs-5 mt-1 mb-0">
                        {{ $cashAccount->responsible?->name }}
                    </p>
                </div>
            </div>
            <div class="card-toolbar">

            </div>
        </div>
        <div class="card-body p-9">
            <div class="d-flex flex-wrap mb-5">
                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-7 mb-3">
                    <div class="fs-6 text-gray-800 fw-bold">{{ $cashAccount->closePeriods()->orderBy('period')->first() ? translate_year_month_word(\Carbon\Carbon::parse($cashAccount->closePeriods()->orderBy('period')->first()->period)->format('F Y')) : '-' }}</div>
                    <div class="fw-semibold text-gray-500">Последний закрытый месяц</div>
                </div>

                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3">
                    <div class="fs-6 fw-bold {{ $cashAccount->getBalance() < 0 ? 'text-danger' : 'text-success' }}">{{ \App\Models\CurrencyExchangeRate::format($cashAccount->getBalance()) }}</div>
                    <div class="fw-semibold text-gray-500">Баланс</div>
                </div>
            </div>

            @php
                $validPercent = $cashAccount->getValidPercent();
                $validColor = $validPercent <= 49 ? 'danger' : ($validPercent < 100 ? 'warning' : 'success');
            @endphp

            <div class="h-4px w-100 bg-light mb-7" data-bs-toggle="tooltip" aria-label="Проверена на {{ $validPercent }}%" data-bs-original-title="Проверена на {{ $validPercent }}%" data-kt-initialized="1">
                <div class="bg-{{ $validColor }} rounded h-4px" role="progressbar" style="width: {{ $validPercent }}%" aria-valuenow=" {{ $validPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div class="symbol-group symbol-hover">
                    @foreach($cashAccount->sharedUsers as $user)
                        <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip" aria-label="{{ $user->name }}" data-bs-original-title="{{ $user->name }}" data-kt-initialized="1">
                            @if ($user->photo)
                                <img alt="{{ $user->name }}" src="{{ $user->getPhoto() }}">
                            @else
                                <span class="symbol-label fs-5 fw-semibold text-{{ $user->getInitialColor() }} bg-light-{{ $user->getInitialColor() }}">{{ $user->getInitials() }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="d-flex gap-1 flex-row">
                    @if ($cashAccount->objects->count() > 0)
                        @foreach($cashAccount->objects->sortBy('code') as $object)
                            <span class="badge badge-light" data-bs-toggle="tooltip" aria-label="{{ $object->name }}" data-bs-original-title="{{ $object->name }}">{{ $object->code }}</span>
                        @endforeach
                    @else
                        <span class="badge badge-light">Все объекты</span>
                    @endif
                </div>
            </div>

        </div>
    </a>
</div>