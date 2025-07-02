@php
    $warningInfo = null;

    if (isset($organizationInn) || isset($organizationName)) {
        $warningOrganizationsInfo = \Illuminate\Support\Facades\Cache::get('warning_organizations_data', []);

        if (isset($organizationInn)) {
            $foundByInn = array_search($organizationInn, array_column($warningOrganizationsInfo, 'inn'));
            if ($foundByInn !== false) {
                $warningInfo = $warningOrganizationsInfo[$foundByInn];
            }
        } elseif (isset($organizationName)) {
            $foundByName = array_search($organizationName, array_column($warningOrganizationsInfo, 'organizationName'));
            if ($foundByName !== false) {
                $warningInfo = $warningOrganizationsInfo[$foundByName];
            }
        }
    }
@endphp

@if (!is_null($warningInfo))
    <span
            class="text-danger border-bottom-dashed"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            data-bs-html="true"
            data-bs-delay-hide="1000"
            title="{{ $warningInfo['type'] . ' на сумму ' . \App\Models\CurrencyExchangeRate::format($warningInfo['amount']) }}, <a href='{{ route('files.download', ['file' => base64_encode('public/objects-debts-manuals/warning_organizations.xlsx')]) }}'>Скачать детали</a>"
    >{{ $organizationName ?? '' }}</span>
@else
    <span>{{ $organizationName ?? '' }}</span>
@endif
