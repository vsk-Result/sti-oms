@inject('currencyExchangeService', 'App\Services\CurrencyExchangeRateService')

<div class="d-flex flex-wrap flex-sm-nowrap mb-6">
    <div class="me-7 mb-4">
        <a class="d-block overlay w-150px" data-fslightbox="object-{{ $object->id }}" href="{{ $object->getPhoto() }}">
            <div class="overlay-wrapper bgi-no-repeat bgi-position-center bgi-size-cover card-rounded min-h-175px"
                 style="background-image:url({{ $object->getPhoto() }})">
            </div>
            <div class="overlay-layer card-rounded bg-dark bg-opacity-25 shadow">
                <i class="bi bi-eye-fill text-white fs-3x"></i>
            </div>
        </a>
    </div>

    <div class="flex-grow-1">
        <div class="d-flex justify-content-between align-items-start flex-wrap">
            <div class="d-flex flex-column">
                <div class="d-flex align-items-center mb-1">
                    <a href="{{ route('objects.show', $object) }}" class="text-gray-800 text-hover-primary fs-2 fw-bolder me-3">{{ $object->getName() }}</a>
                    @if ($object->isActive())
                        <span class="badge badge-light-success me-auto">Активен</span>
                    @elseif ($object->isBlocked())
                        <span class="badge badge-light-danger me-auto">Закрыт</span>
                    @elseif ($object->isDeleted())
                        <span class="badge badge-light-danger me-auto">Удален</span>
                    @endif
                </div>

                <div class="d-flex flex-wrap fw-bold fs-6 mb-4 pe-2">
                    @if ($object->address)
                        <div class="d-flex align-items-center text-gray-400  me-5 mb-2">
                            <span class="svg-icon svg-icon-4 me-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z" fill="black"></path>
                                    <path d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z" fill="black"></path>
                                </svg>
                            </span>
                            {{ $object->address }}
                        </div>
                    @endif
                    @if ($object->responsible_name)
                        <div class="d-flex align-items-center text-gray-400 me-5 mb-2">
                            <span class="svg-icon svg-icon-4 me-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 7C10.3 7 9 8.3 9 10C9 11.7 10.3 13 12 13C13.7 13 15 11.7 15 10C15 8.3 13.7 7 12 7Z" fill="black"></path>
                                    <path d="M12 22C14.6 22 17 21 18.7 19.4C17.9 16.9 15.2 15 12 15C8.8 15 6.09999 16.9 5.29999 19.4C6.99999 21 9.4 22 12 22Z" fill="black"></path>
                                </svg>
                            </span>
                            {{ $object->responsible_name }}
                        </div>
                    @endif
                    @if ($object->responsible_email)
                        <a href="mailto:{{ $object->responsible_email }}" class="d-flex align-items-center text-primary-400 text-hover-dark me-5 mb-2">
                            <span class="svg-icon svg-icon-4 me-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M21 19H3C2.4 19 2 18.6 2 18V6C2 5.4 2.4 5 3 5H21C21.6 5 22 5.4 22 6V18C22 18.6 21.6 19 21 19Z" fill="black"></path>
                                    <path d="M21 5H2.99999C2.69999 5 2.49999 5.10005 2.29999 5.30005L11.2 13.3C11.7 13.7 12.4 13.7 12.8 13.3L21.7 5.30005C21.5 5.10005 21.3 5 21 5Z" fill="black"></path>
                                </svg>
                            </span>
                            {{ $object->responsible_email }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="d-flex mb-4">
                @can('edit objects')
                    <a href="{{ route('objects.edit', $object) }}" class="btn btn-light btn-active-light-primary btn-sm me-3">Изменить</a>

                    <form action="{{ route('objects.exports.store', $object) }}" method="POST" class="hidden">
                        @csrf
                        <a
                                href="javascript:void(0);"
                                class="btn btn-sm btn-light-primary me-3"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                        >
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black"></rect>
                                    <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black"></path>
                                    <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4"></path>
                                </svg>
                            </span>
                            Экспорт в Excel
                        </a>
                    </form>
                @endcan
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-start">
            @inject('contractService', 'App\Services\Contract\ContractService')
            @inject('pivotObjectDebtService', 'App\Services\PivotObjectDebtService')

            @php
                $debts = $pivotObjectDebtService->getPivotDebtForObject($object->id);
                $serviceDebtsAmount = $debts['service']->total_amount;
                $contractorDebtsAmount = $debts['contractor']->total_amount;

                $debtObjectImport = \App\Models\Debt\DebtImport::where('type_id', \App\Models\Debt\DebtImport::TYPE_OBJECT)->latest('date')->first();
                $objectExistInObjectImport = $debtObjectImport->debts()->where('object_id', $object->id)->count() > 0;

                if ($objectExistInObjectImport) {
                    $contractorDebtsAvans = \App\Models\Debt\Debt::where('import_id', $debtObjectImport->id)->where('type_id', \App\Models\Debt\Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->sum('avans');
                    $contractorDebtsGU = \App\Models\Debt\Debt::where('import_id', $debtObjectImport->id)->where('type_id', \App\Models\Debt\Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->sum('guarantee');
                    $contractorDebtsAmount = $contractorDebtsAmount + $contractorDebtsAvans + $contractorDebtsGU;
                }

                $providerDebtsAmount = $debts['provider']->total_amount;
                $ITRSalaryDebt = $object->getITRSalaryDebt();
                $workSalaryDebt = $object->getWorkSalaryDebt();
                $workSalaryDebtDetails = $object->getWorkSalaryDebtDetails();
                $customerDebtInfo = [];
                $contractService->filterContracts(['object_id' => [$object->id]], $customerDebtInfo);
//                $customerDebt = $customerDebtInfo['avanses_acts_left_paid_amount']['RUB'] + $customerDebtInfo['avanses_left_amount']['RUB'] + $customerDebtInfo['avanses_acts_deposites_amount']['RUB'] - $object->guaranteePayments->where('currency', 'RUB')->sum('amount');

                $contractsTotalAmount = $customerDebtInfo['amount']['RUB'];

                $dolgZakazchikovZaVipolnenieRaboti = $customerDebtInfo['avanses_acts_left_paid_amount']['RUB'];
                $dolgFactUderjannogoGU = $customerDebtInfo['avanses_acts_deposites_amount']['RUB'] - $object->guaranteePayments->where('currency', 'RUB')->sum('amount');

                // старая версия
//                $ostatokPoDogovoruSZakazchikom = $customerDebtInfo['amount']['RUB'] - $customerDebtInfo['avanses_notwork_left_amount']['RUB'] - $customerDebtInfo['acts_amount']['RUB'];

                //новая версия
                $ostatokPoDogovoruSZakazchikom = $customerDebtInfo['amount']['RUB'] - $customerDebtInfo['avanses_received_amount']['RUB'] - $customerDebtInfo['avanses_acts_paid_amount']['RUB'];

                $ostatokNeotrabotannogoAvansa = $customerDebtInfo['avanses_notwork_left_amount']['RUB'];

                $writeoffs = $object->writeoffs->sum('amount');

                $date = now();
                $EURExchangeRate = $currencyExchangeService->getExchangeRate($date->format('Y-m-d'), 'EUR');
                if ($EURExchangeRate) {
                    $dolgZakazchikovZaVipolnenieRaboti += $customerDebtInfo['avanses_acts_left_paid_amount']['EUR'] * $EURExchangeRate->rate;
                    $dolgFactUderjannogoGU += ($customerDebtInfo['avanses_acts_deposites_amount']['EUR'] - $object->guaranteePayments->where('currency', 'EUR')->sum('amount')) * $EURExchangeRate->rate;
                    $ostatokNeotrabotannogoAvansa += ($customerDebtInfo['avanses_notwork_left_amount']['EUR'] * $EURExchangeRate->rate);

                    // старая версия
//                    $ostatokPoDogovoruSZakazchikom += ($customerDebtInfo['amount']['EUR'] * $EURExchangeRate->rate);
//                    $ostatokPoDogovoruSZakazchikom -= ($customerDebtInfo['avanses_notwork_left_amount']['EUR'] * $EURExchangeRate->rate);
//                    $ostatokPoDogovoruSZakazchikom -= ($customerDebtInfo['acts_amount']['EUR'] * $EURExchangeRate->rate);

                    //новая версия
                    $ostatokPoDogovoruSZakazchikom += ($customerDebtInfo['amount']['EUR'] * $EURExchangeRate->rate);
                    $ostatokPoDogovoruSZakazchikom -= ($customerDebtInfo['avanses_received_amount']['EUR'] * $EURExchangeRate->rate);
                    $ostatokPoDogovoruSZakazchikom -= ($customerDebtInfo['avanses_acts_paid_amount']['EUR'] * $EURExchangeRate->rate);

//                    $customerDebt += $customerDebtInfo['avanses_acts_left_paid_amount']['EUR'] * $EURExchangeRate->rate;
//                    $customerDebt += $customerDebtInfo['avanses_left_amount']['EUR'] * $EURExchangeRate->rate;
//                    $customerDebt += $customerDebtInfo['avanses_acts_deposites_amount']['EUR'] * $EURExchangeRate->rate;
//                    $customerDebt -= $object->guaranteePayments->where('currency', 'EUR')->sum('amount')  * $EURExchangeRate->rate;

                    $contractsTotalAmount += $customerDebtInfo['amount']['EUR'] * $EURExchangeRate->rate;
                }

                if (! empty($object->closing_date) && $object->status_id === \App\Models\Status::STATUS_BLOCKED) {
                    $ostatokPoDogovoruSZakazchikom = 0;
                }

                if ($object->code === '288') {
                    $dolgFactUderjannogoGU = $customerDebtInfo['avanses_acts_deposites_amount']['RUB'];
                }

                $objectBalance = $object->total_with_general_balance +
                                $dolgZakazchikovZaVipolnenieRaboti +
                                $dolgFactUderjannogoGU +
                                $contractorDebtsAmount +
                                $providerDebtsAmount +
                                $serviceDebtsAmount +
                                $ITRSalaryDebt +
                                $workSalaryDebt +
                                $writeoffs;

                $prognozBalance = $objectBalance + $ostatokPoDogovoruSZakazchikom;
            @endphp

            <div class="me-11">
                <div class="d-flex flex-stack">
                    <a class="pivot-box position-relative w-100 d-flex flex-stack" href="{{ route('objects.payments.index', $object) }}?amount_expression_operator=>%3D&amount_expression=0&object_id%5B%5D={{ $object->id }}">
                        <div class="text-gray-700 fw-semibold fs-7 me-2">Приходы</div>
                        <div class="ms-3 d-flex align-items-senter fw-bold text-success">
                            {{ \App\Models\CurrencyExchangeRate::format($object->total_receive, 'RUB') }}
                        </div>
                        <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $object->total_receive }}">
                                        <span class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                            </svg>
                                        </span>
                        </button>
                    </a>
                </div>

                <div class="separator separator-dashed my-3"></div>

                <div class="d-flex flex-stack">
                    <a class="pivot-box position-relative w-100 d-flex flex-stack" href="{{ route('objects.payments.index', $object) }}?amount_expression_operator=<&amount_expression=0&object_id%5B%5D={{ $object->id }}">
                        <div class="text-gray-700 fw-semibold fs-7 me-2">Расходы</div>
                        <div class="ms-3 d-flex align-items-senter fw-bold text-danger">
                            {{ \App\Models\CurrencyExchangeRate::format($object->total_pay, 'RUB') }}
                        </div>
                        <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $object->total_pay }}">
                                        <span class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                            </svg>
                                        </span>
                        </button>
                    </a>
                </div>

                <div class="separator separator-dashed my-3"></div>

                <div class="d-flex flex-stack">
                    @if ($object->code === '288')
                        @php
                            $general = $object->payments->where('object_worktype_id', 7)->sum('amount');
                            $one =  $object->payments->where('object_worktype_id', 1)->sum('amount');
                            $twoFour = $object->payments->whereIn('object_worktype_id', [2, 4])->sum('amount');
                            $oneBalance = ($one / ($one + $twoFour) * $general) + $one;
                            $twoFourBalance = ($twoFour / ($one + $twoFour) * $general) + $twoFour;
                        @endphp
                        <div class="d-flex flex-column w-100">
                            <a class="pivot-box mb-2 w-100 d-flex flex-stack position-relative" href="{{ route('objects.payments.index', $object) }}?object_id%5B%5D={{ $object->id }}">
                                <div class="text-gray-700 fw-semibold fs-7 me-2">Сальдо без общ. расходов</div>
                                <div class="ms-3 d-flex align-items-senter fw-bold {{ $object->total_balance < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ \App\Models\CurrencyExchangeRate::format($object->total_balance, 'RUB') }}
                                </div>
                                <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $object->total_balance }}">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                        </svg>
                                    </span>
                                </button>
                            </a>
                            <a class="pivot-box position-relative mb-2 w-100 d-flex flex-stack" href="{{ route('objects.payments.index', $object) }}?object_worktype_id%5B%5D=1&object_id%5B%5D={{ $object->id }}">
                                <div class="text-gray-700 fw-semibold fs-7 me-2">Сальдо без общ. расходов (1)</div>
                                <div class="ms-3 d-flex align-items-senter fw-bold {{ $oneBalance < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ \App\Models\CurrencyExchangeRate::format($oneBalance, 'RUB') }}
                                </div>
                                <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $oneBalance }}">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                        </svg>
                                    </span>
                                </button>
                            </a>
                            <a class="pivot-box position-relative w-100 d-flex flex-stack" href="{{ route('objects.payments.index', $object) }}?object_worktype_id%5B%5D=2&object_worktype_id%5B%5D=4&object_id%5B%5D={{ $object->id }}">
                                <div class="text-gray-700 fw-semibold fs-7 me-2">Сальдо без общ. расходов (2,4)</div>
                                <div class="ms-3 d-flex align-items-senter fw-bold {{ $twoFourBalance < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ \App\Models\CurrencyExchangeRate::format($twoFourBalance, 'RUB') }}
                                </div>
                                <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $twoFourBalance }}">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                        </svg>
                                    </span>
                                </button>
                            </a>
                        </div>
                    @else
                        <a class="pivot-box position-relative w-100 d-flex flex-stack" href="{{ route('objects.payments.index', $object) }}?object_id%5B%5D={{ $object->id }}">
                            <div class="text-gray-700 fw-semibold fs-7 me-2">Сальдо без общ. расходов</div>
                            <div class="ms-3 d-flex align-items-senter fw-bold {{ $object->total_balance < 0 ? 'text-danger' : 'text-success' }}">
                                {{ \App\Models\CurrencyExchangeRate::format($object->total_balance, 'RUB') }}
                            </div>
                            <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $object->total_balance }}">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                        </svg>
                                    </span>
                            </button>
                        </a>
                    @endif
                </div>

                <div class="separator separator-dashed my-3"></div>

                <div class="d-flex flex-stack">
                    @php
                        $generalCostURL = auth()->user()->can('index general-costs') ? route('general_costs.index') : 'javascript:void(0);';
                        $generalCostClass = auth()->user()->can('index general-costs') ? 'cursor-pointer' : 'cursor-default';
                    @endphp

                    @if ($object->code === '288')
                        <div class="d-flex flex-column w-100">
                            <a class="pivot-box position-relative mb-2 w-100 d-flex flex-stack {{ $generalCostClass }}" href="{{ $generalCostURL }}">
                                <div class="text-gray-700 fw-semibold fs-7 me-2">Общие расходы</div>
                                <div class="ms-3 d-flex align-items-senter fw-bold {{ ($object->total_with_general_balance - $object->total_balance) < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ \App\Models\CurrencyExchangeRate::format($object->total_with_general_balance - $object->total_balance, 'RUB') }}
                                </div>
                                <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ ($object->total_with_general_balance - $object->total_balance) }}">
                                        <span class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                            </svg>
                                        </span>
                                </button>
                            </a>
                            <a class="pivot-box position-relative mb-2 w-100 d-flex flex-stack {{ $generalCostClass }}" href="{{ $generalCostURL }}">
                                <div class="text-gray-700 fw-semibold fs-7 me-2">Общие расходы (1)</div>
                                <div class="ms-3 d-flex align-items-senter fw-bold {{ $object->general_balance_1 < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ \App\Models\CurrencyExchangeRate::format($object->general_balance_1, 'RUB') }}
                                </div>
                                <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $object->general_balance_1 }}">
                                        <span class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                            </svg>
                                        </span>
                                </button>
                            </a>
                            <a class="pivot-box position-relative w-100 d-flex flex-stack {{ $generalCostClass }}" href="{{ $generalCostURL }}">
                                <div class="text-gray-700 fw-semibold fs-7 me-2">Общие расходы (2,4)</div>
                                <div class="ms-3 d-flex align-items-senter fw-bold {{ $object->general_balance_24 < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ \App\Models\CurrencyExchangeRate::format($object->general_balance_24, 'RUB') }}
                                </div>
                                <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $object->general_balance_24 }}">
                                        <span class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                            </svg>
                                        </span>
                                </button>
                            </a>
                        </div>
                    @else
                        <a class="pivot-box position-relative w-100 d-flex flex-stack {{ $generalCostClass }}" href="{{ $generalCostURL }}">
                            <div class="text-gray-700 fw-semibold fs-7 me-2">Общие расходы</div>
                            <div class="ms-3 d-flex align-items-senter fw-bold {{ ($object->total_with_general_balance - $object->total_balance) < 0 ? 'text-danger' : 'text-success' }}">
                                {{ \App\Models\CurrencyExchangeRate::format($object->total_with_general_balance - $object->total_balance, 'RUB') }}
                            </div>
                            <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ ($object->total_with_general_balance - $object->total_balance) }}">
                                        <span class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                            </svg>
                                        </span>
                            </button>
                        </a>
                    @endif
                </div>

                <div class="separator separator-dashed my-3"></div>

                <div class="d-flex flex-stack">
                    @if ($object->code === '288')
                        <div class="d-flex flex-column w-100">
                            <div class="pivot-box position-relative mb-2 w-100 d-flex flex-stack">
                                <div class="text-gray-700 fw-semibold fs-7 me-2">Сальдо c общ. расходами</div>
                                <div class="ms-3 d-flex align-items-senter fw-bold {{ $object->total_with_general_balance < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ \App\Models\CurrencyExchangeRate::format($object->total_with_general_balance, 'RUB') }}
                                </div>
                                <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $object->total_with_general_balance }}">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                            <div class="pivot-box position-relative mb-2 w-100 d-flex flex-stack">
                                <div class="text-gray-700 fw-semibold fs-7 me-2">Сальдо c общ. расходами (1)</div>
                                <div class="ms-3 d-flex align-items-senter fw-bold {{ ($oneBalance + $object->general_balance_1) < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ \App\Models\CurrencyExchangeRate::format(($oneBalance + $object->general_balance_1), 'RUB') }}
                                </div>
                                <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ ($oneBalance + $object->general_balance_1) }}">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                            <div class="pivot-box position-relative w-100 d-flex flex-stack">
                                <div class="text-gray-700 fw-semibold fs-7 me-2">Сальдо c общ. расходами (2,4)</div>
                                <div class="ms-3 d-flex align-items-senter fw-bold {{ ($twoFourBalance + $object->general_balance_24) < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ \App\Models\CurrencyExchangeRate::format(($twoFourBalance + $object->general_balance_24), 'RUB') }}
                                </div>
                                <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ ($twoFourBalance + $object->general_balance_24) }}">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="pivot-box position-relative w-100 d-flex flex-stack">
                            <div class="text-gray-700 fw-semibold fs-7 me-2">Сальдо c общ. расходами</div>
                            <div class="ms-3 d-flex align-items-senter fw-bold {{ $object->total_with_general_balance < 0 ? 'text-danger' : 'text-success' }}">
                                {{ \App\Models\CurrencyExchangeRate::format($object->total_with_general_balance, 'RUB') }}
                            </div>
                            <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $object->total_with_general_balance }}">
                                        <span class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                            </svg>
                                        </span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="me-11">
                <div class="d-flex flex-stack">
                    <a class="pivot-box position-relative w-100 d-flex flex-stack" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&type_id%5B%5D={{ \App\Models\Debt\Debt::TYPE_CONTRACTOR }}">
                        <div class="text-gray-700 fw-semibold fs-7 me-2">Долг подрядчикам (в т.ч. ГУ)</div>
                        <div class="ms-3 d-flex align-items-senter fw-bold {{ $contractorDebtsAmount < 0 ? 'text-danger' : 'text-success' }}">
                            {{ \App\Models\CurrencyExchangeRate::format($contractorDebtsAmount, 'RUB') }}
                        </div>
                        <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $contractorDebtsAmount }}">
                                        <span class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                            </svg>
                                        </span>
                        </button>
                    </a>
                </div>

                <div class="separator separator-dashed my-3"></div>

                <div class="d-flex flex-stack">
                    <a class="pivot-box position-relative w-100 d-flex flex-stack" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&type_id%5B%5D={{ \App\Models\Debt\Debt::TYPE_PROVIDER }}">
                        <div class="text-gray-700 fw-semibold fs-7 me-2">Долг поставщикам</div>
                        <div class="ms-3 d-flex align-items-senter fw-bold {{ $providerDebtsAmount < 0 ? 'text-danger' : 'text-success' }}">
                            {{ \App\Models\CurrencyExchangeRate::format($providerDebtsAmount, 'RUB') }}
                        </div>
                        <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $providerDebtsAmount }}">
                                        <span class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                            </svg>
                                        </span>
                        </button>
                    </a>
                </div>

                <div class="separator separator-dashed my-3"></div>

                <div class="d-flex flex-stack">
                    <a class="pivot-box position-relative w-100 d-flex flex-stack" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&type_id%5B%5D={{ \App\Models\Debt\Debt::TYPE_SERVICE }}">
                        <div class="text-gray-700 fw-semibold fs-7 me-2">Долг за услуги</div>
                        <div class="ms-3 d-flex align-items-senter fw-bold {{ $serviceDebtsAmount < 0 ? 'text-danger' : 'text-success' }}">
                            {{ \App\Models\CurrencyExchangeRate::format($serviceDebtsAmount, 'RUB') }}
                        </div>
                        <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $serviceDebtsAmount }}">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                </svg>
                            </span>
                        </button>
                    </a>
                </div>

                <div class="separator separator-dashed my-3"></div>

                <div class="d-flex flex-stack">
                    <div class="pivot-box position-relative w-100 d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-7 me-2">Долг на зарплаты ИТР</div>
                        <div class="ms-3 d-flex align-items-senter fw-bold {{ $ITRSalaryDebt < 0 ? 'text-danger' : 'text-success' }}">
                            {{ \App\Models\CurrencyExchangeRate::format($ITRSalaryDebt, 'RUB') }}
                        </div>
                        <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $ITRSalaryDebt }}">
                                            <span class="svg-icon svg-icon-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                                </svg>
                                            </span>
                        </button>
                    </div>
                </div>

                <div class="separator separator-dashed my-3"></div>

                <div class="d-flex flex-stack">
                    <div class="pivot-box position-relative w-100 d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-7 me-2">
                            <div
                                    class="cursor-pointer"
                                    data-bs-custom-class="workers-salary-detailing-popover"
                                    data-bs-toggle="popover"
                                    data-bs-placement="top"
                                    data-bs-html="true"
                                    title="Детализация по долгу на зарплаты рабочим"
                                    data-bs-content='<div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>{{ $workSalaryDebtDetails['real']['date'] }}</strong>
                                            <span class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($workSalaryDebtDetails['real']['amount'], 'RUB') }}</span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong class="required">{{ $workSalaryDebtDetails['predict']['date'] }}</strong>
                                            <span class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($workSalaryDebtDetails['predict']['amount'], 'RUB') }}</span>
                                        </div>
                                    </div>
                                    <div class="predict text-muted"><span class="required"></span> прогнозируемый долг</div>
                                    </div>'
                            >
                                Долг на зарплаты рабочим
                            </div>
                        </div>
                        <div class="ms-3 d-flex align-items-senter fw-bold {{ $workSalaryDebt < 0 ? 'text-danger' : 'text-success' }}">
                            {{ \App\Models\CurrencyExchangeRate::format($workSalaryDebt, 'RUB') }}
                        </div>
                        <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $workSalaryDebt }}">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="me-11">
{{--                <div class="separator separator-dashed my-3"></div>--}}

{{--                <div class="d-flex flex-stack">--}}
{{--                    <a class="pivot-box position-relative w-100 d-flex flex-stack" href="{{ route('objects.contracts.index', $object) }}?object_id%5B%5D={{ $object->id }}">--}}
{{--                        <div class="text-gray-700 fw-semibold fs-7 me-2">Долг заказчиков</div>--}}
{{--                        <div class="ms-3 d-flex align-items-senter fw-bold {{ $customerDebt < 0 ? 'text-danger' : 'text-success' }}">--}}
{{--                            {{ \App\Models\CurrencyExchangeRate::format($customerDebt, 'RUB') }}--}}
{{--                        </div>--}}
{{--                        <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $customerDebt }}">--}}
{{--                                        <span class="svg-icon svg-icon-2">--}}
{{--                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">--}}
{{--                                                <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>--}}
{{--                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>--}}
{{--                                            </svg>--}}
{{--                                        </span>--}}
{{--                        </button>--}}
{{--                    </a>--}}
{{--                </div>--}}

                <div class="d-flex flex-stack">
                    <a class="pivot-box position-relative w-100 d-flex flex-stack" href="{{ route('objects.contracts.index', $object) }}?object_id%5B%5D={{ $object->id }}">
                        <div class="text-gray-700 fw-semibold fs-7 me-2">Долг Заказчика за выпол.работы</div>
                        <div class="ms-3 d-flex align-items-senter fw-bold {{ $dolgZakazchikovZaVipolnenieRaboti < 0 ? 'text-danger' : 'text-success' }}">
                            {{ \App\Models\CurrencyExchangeRate::format($dolgZakazchikovZaVipolnenieRaboti, 'RUB') }}
                        </div>
                        <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $dolgZakazchikovZaVipolnenieRaboti }}">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                    </svg>
                                </span>
                        </button>
                    </a>
                </div>

                <div class="separator separator-dashed my-3"></div>

                <div class="d-flex flex-stack">
                    <a class="pivot-box position-relative w-100 d-flex flex-stack" href="{{ route('objects.contracts.index', $object) }}?object_id%5B%5D={{ $object->id }}">
                        <div class="text-gray-700 fw-semibold fs-7 me-2">Долг Заказчика за ГУ (фактич.удерж.)</div>
                        <div class="ms-3 d-flex align-items-senter fw-bold {{ $dolgFactUderjannogoGU < 0 ? 'text-danger' : 'text-success' }}">
                            {{ \App\Models\CurrencyExchangeRate::format($dolgFactUderjannogoGU, 'RUB') }}
                        </div>
                        <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $dolgFactUderjannogoGU }}">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                    </svg>
                                </span>
                        </button>
                    </a>
                </div>

                <div class="separator separator-dashed my-3"></div>

                <div class="d-flex flex-stack">
                    <div class="pivot-box position-relative w-100 d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-7 me-2">Текущий Баланс объекта</div>
                        <div class="ms-3 d-flex align-items-senter fw-bold {{ $objectBalance < 0 ? 'text-danger' : 'text-success' }}">
                            {{ \App\Models\CurrencyExchangeRate::format($objectBalance, 'RUB') }}
                        </div>
                        <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $objectBalance }}">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="">
                <div class="d-flex flex-stack">
                    <div class="pivot-box position-relative w-100 d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-7 me-2">Сумма договоров с Заказчиком</div>
                        <div class="ms-3 d-flex align-items-senter fw-bold {{ $contractsTotalAmount < 0 ? 'text-danger' : 'text-success' }}">
                            {{ \App\Models\CurrencyExchangeRate::format($contractsTotalAmount, 'RUB') }}
                        </div>
                        <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $contractsTotalAmount }}">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>

                <div class="separator separator-dashed my-3"></div>

                <div class="d-flex flex-stack">
                    <div class="pivot-box position-relative w-100 d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-7 me-2">Остаток неотработанного аванса</div>
                        <div class="ms-3 d-flex align-items-senter fw-bold {{ $ostatokNeotrabotannogoAvansa < 0 ? 'text-danger' : 'text-success' }}">
                            {{ \App\Models\CurrencyExchangeRate::format($ostatokNeotrabotannogoAvansa, 'RUB') }}
                        </div>
                        <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $ostatokNeotrabotannogoAvansa }}">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>

                <div class="separator separator-dashed my-3"></div>

                <div class="d-flex flex-stack">
                    <a class="pivot-box position-relative w-100 d-flex flex-stack" href="{{ route('objects.contracts.index', $object) }}?object_id%5B%5D={{ $object->id }}">
                        <div class="text-gray-700 fw-semibold fs-7 me-2">Остаток к получ. от заказчика (в т.ч. ГУ)</div>
                        <div class="ms-3 d-flex align-items-senter fw-bold {{ $ostatokPoDogovoruSZakazchikom < 0 ? 'text-danger' : 'text-success' }}">
                            {{ \App\Models\CurrencyExchangeRate::format($ostatokPoDogovoruSZakazchikom, 'RUB') }}
                        </div>
                        <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $ostatokPoDogovoruSZakazchikom }}">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                </svg>
                            </span>
                        </button>
                    </a>
                </div>

                <div class="separator separator-dashed my-3"></div>

                <div class="d-flex flex-stack">
                    <a class="pivot-box position-relative w-100 d-flex flex-stack" href="{{ route('objects.contracts.index', $object) }}?object_id%5B%5D={{ $object->id }}">
                        <div class="text-gray-700 fw-semibold fs-7 me-2">Прогнозируемый Баланс объекта</div>
                        <div class="ms-3 d-flex align-items-senter fw-bold {{ $prognozBalance < 0 ? 'text-danger' : 'text-success' }}">
                            {{ \App\Models\CurrencyExchangeRate::format($prognozBalance, 'RUB') }}
                        </div>
                        <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $prognozBalance }}">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                </svg>
                            </span>
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
