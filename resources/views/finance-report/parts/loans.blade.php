<div class="card card-xxl-stretch mb-5 mb-xxl-10">
    <div class="card-body p-9">

        <div class="fs-1 fw-bolder {{ $loansInfo->totalLoanAmount < 0 ? 'text-danger' : '' }}">{{ \App\Models\CurrencyExchangeRate::format($loansInfo->totalLoanAmount, 'RUB') }}</div>

        <div class="d-flex align-items-center fs-5 fw-bold text-gray-400 mb-7">
            <span class="d-flex">Долг по займам на {{ $loansInfo->loansLastUpdateDate }}</span>
        </div>

        @php
            $groupInfo = [];
            $usedOrgNames = [];
            $groupTotal = [];
            foreach($loansGroupInfo as $group => $orgNames) {
                $groupTotal[$group] = 0;
                $groupInfo[$group] = [];
                foreach ($loansInfo->loans as $loan) {
                    $orgName = $loan->organization->name;
                    if (in_array($orgName, $orgNames)) {
                        $usedOrgNames[] = $orgName;
                        if ($orgName === $group) {
                            $groupInfo[$group][$loan->name] = $loan->amount;
                        } else {
                            $groupInfo[$group][$orgName . ', ' . $loan->name] = $loan->amount;
                        }
                        $groupTotal[$group] += $loan->amount;
                    }
                }
            }

        @endphp

        @foreach($groupInfo as $group => $groupLoans)
            <div class="fs-6 d-flex justify-content-between my-4">
                <div class="d-flex flex-column gap-1">
                    <div class="fw-bold">{{$group }}</div>
                    @foreach($groupLoans as $loan => $amount)
                        <p class="fs-7 text-muted mb-0">
                            {{ $loan }}
                        </p>
                    @endforeach
                </div>

                <div class="min-w-100px d-flex flex-column fw-bolder text-end gap-1 {{ $groupTotal[$group] < 0 ? 'text-danger' : 'text-success' }}">
                    {{ \App\Models\CurrencyExchangeRate::format($groupTotal[$group], 'RUB') }}

                    @foreach($groupLoans as $loan => $amount)
                        <p class="fs-7 fst-italic text-end mb-0 text-muted">
                            {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB') }}
                        </p>
                    @endforeach
                </div>
            </div>

            <div class="separator separator-dashed"></div>
        @endforeach

        @foreach($loansInfo->loans as $loan)
            @continue(in_array($loan->organization->name, $usedOrgNames))
            <div class="fs-6 d-flex justify-content-between my-4">
                <div>
                    <div class="fw-bold">{{ $loan->organization->name }}</div>
                    <p class="fs-7 mb-0">
                        <a
                            class="text-muted"
                            style="border-bottom: 1px dashed #ccc;"
                            href="{{ route('loans.history.index', $loan->id) }}"
                        >
                            {{ $loan->name }}
                        </a>
                    </p>
                </div>

                <div class="min-w-100px fw-bolder">
                    <p class="text-end mb-0 {{ $loan->amount < 0 ? 'text-danger' : 'text-success' }}">
                        {{ \App\Models\CurrencyExchangeRate::format($loan->amount, 'RUB') }}
                    </p>
                </div>
            </div>

            @if (! $loop->last)
                <div class="separator separator-dashed"></div>
            @endif
        @endforeach
    </div>
</div>
