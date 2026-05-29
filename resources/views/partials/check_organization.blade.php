@php
    $warningInfo = null;
    $warningFineInfo = null;
    $hasMoreWithINN = false;

    if (isset($organizationInn) || isset($organizationName)) {
        $amount = 0;
        $warningOrganizationsInfo = \Illuminate\Support\Facades\Cache::get('warning_organizations_data', []);
        $warningOrganizationsFineInfo = \Illuminate\Support\Facades\Cache::get('warning_organizations_fine_data', []);
        if (empty($organizationInn) && !empty($organizationName)) {
            $or = \App\Models\Organization::where('name', $organizationName)->whereNotNull('inn')->where('inn', '!=', '')->first();
            if ($or) {
                $organizationInn = $or->inn;
            }
        }
        if (isset($organizationInn) && !empty($organizationName)) {
            $foundByInn = array_search($organizationInn, array_column($warningOrganizationsInfo, 'inn'));
            if ($foundByInn !== false) {
                $warningInfo = $warningOrganizationsInfo[$foundByInn];

                foreach ($warningOrganizationsInfo as $info) {
                    if (!empty($info['inn']) && $info['inn'] == $organizationInn) {
                        $amount += $info['amount'];
                    }
                }
            } else {
                if (isset($organizationName) && !empty($organizationName)) {
                    $foundByName = array_search($organizationName, array_column($warningOrganizationsInfo, 'organizationName'));
                    if ($foundByName !== false) {
                        $warningInfo = $warningOrganizationsInfo[$foundByName];

                        foreach ($warningOrganizationsInfo as $info) {
                            if (!empty($info['organizationName']) && ($info['organizationName'] == $organizationName)) {
                                $amount += $info['amount'];
                            }
                        }
                    }
                }
            }
        } elseif (isset($organizationName) && !empty($organizationName)) {
            $foundByName = array_search($organizationName, array_column($warningOrganizationsInfo, 'organizationName'));
            if ($foundByName !== false) {
                $warningInfo = $warningOrganizationsInfo[$foundByName];

                foreach ($warningOrganizationsInfo as $info) {
                    if (!empty($info['organizationName']) && ($info['organizationName'] == $organizationName)) {
                        $amount += $info['amount'];
                    }
                }
            }
        }

        if (isset($organizationInn) && !empty($organizationInn)) {
            $foundByInn = array_search($organizationInn, array_column($warningOrganizationsFineInfo, 'inn'));
            if ($foundByInn !== false) {
                $warningFineInfo = $warningOrganizationsFineInfo[$foundByInn];

                foreach ($warningOrganizationsFineInfo as $info) {
                    if (!empty($info['inn']) && $info['inn'] == $organizationInn) {
                        $amount += $info['amount'];
                    }
                }
            } else {
                if (isset($organizationName) && !empty($organizationName)) {
                    $foundByName = array_search($organizationName, array_column($warningOrganizationsFineInfo, 'organizationName'));
                    if ($foundByName !== false) {
                        $warningFineInfo = $warningOrganizationsFineInfo[$foundByName];

                        foreach ($warningOrganizationsFineInfo as $info) {
                            if (!empty($info['organizationName']) && ($info['organizationName'] == $organizationName)) {
                                $amount += $info['amount'];
                            }
                        }
                    }
                }
            }
        } elseif (isset($organizationName) && !empty($organizationName)) {
            $foundByName = array_search($organizationName, array_column($warningOrganizationsFineInfo, 'organizationName'));
            if ($foundByName !== false) {
                $warningFineInfo = $warningOrganizationsFineInfo[$foundByName];

                foreach ($warningOrganizationsFineInfo as $info) {
                    if (!empty($info['organizationName']) && ($info['organizationName'] == $organizationName)) {
                        $amount += $info['amount'];
                    }
                }
            }
        }

//        if (isset($organizationInn) && isset($organizationName) && $organizationInn != 0 && !empty($organizationInn)) {
//            $moreOrganizations = \App\Models\Organization::where('name', '!=', $organizationName)->where('inn', $organizationInn)->get();
//
//            if ($moreOrganizations->count() > 0) {
//                $hasMoreWithINN = true;
//            }
//        }
    }
@endphp

@if (!is_null($warningInfo) && $amount != 0)
    <span
            class="text-danger border-bottom-dashed"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            data-bs-html="true"
            data-bs-delay-hide="1000"
            title="{{ $warningInfo['type'] . ' на сумму ' . \App\Models\CurrencyExchangeRate::format($amount) }}, <a href='{{ route('files.download', ['file' => base64_encode('public/objects-debts-manuals/warning_organizations.xls')]) }}'>Скачать детали</a>"
    >{{ $organizationName ?? '' }}</span>
@elseif(!is_null($warningFineInfo) && $amount != 0)
    <span
            class="text-danger border-bottom-dashed"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            data-bs-html="true"
            data-bs-delay-hide="1000"
            title="{{ $warningFineInfo['type'] . ' на сумму ' . \App\Models\CurrencyExchangeRate::format($amount) }}, <a href='{{ route('files.download', ['file' => base64_encode('public/objects-debts-manuals/' . $warningFineInfo['filename'])]) }}'>Скачать детали</a>"
    >{{ $organizationName ?? '' }}</span>
@elseif($hasMoreWithINN)
    <span
        class="text-danger border-bottom-dashed"
        data-bs-toggle="tooltip"
        data-bs-placement="top"
        data-bs-html="true"
        data-bs-delay-hide="1000"
        title="С ИНН {{ $organizationInn ?? '' }} в базе есть еще контрагенты: {{ implode(', ', $moreOrganizations->pluck('name')->toArray()) }}"
    >{{ $organizationName ?? '' }}</span>
@else
    <span>{{ $organizationName ?? '' }}</span>
@endif
