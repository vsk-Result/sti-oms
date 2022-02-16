<div class="d-flex flex-wrap flex-sm-nowrap mb-6">
    <div class="me-7 mb-4">
        <div class="symbol symbol-150px">
            <div class="symbol-label" style="background-image:url({{ $object->getPhoto() }})"></div>
        </div>
    </div>
    <div class="flex-grow-1">
        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
            <div class="d-flex flex-column">
                <div class="d-flex align-items-center mb-1">
                    <a href="{{ route('objects.show', $object) }}" class="text-gray-800 text-hover-primary fs-2 fw-bolder me-3">{{ $object->getName() }}</a>
                    <span class="badge badge-light-success me-auto">Активен</span>
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
                @endcan
                <a href="{{ route('objects.payments.index', $object) }}" class="btn btn-primary btn-active-light-primary btn-sm">Оплаты</a>
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-start">
            <div class="d-flex flex-wrap">
{{--                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">--}}
{{--                    <div class="d-flex align-items-center">--}}
{{--                        <div class="fs-4 fw-bolder">{{ $object->getEmployeesCount() }}</div>--}}
{{--                    </div>--}}
{{--                    <div class="fw-bold fs-6 text-gray-400">Рабочих</div>--}}
{{--                </div>--}}

                @if ($object->code === '288')
                    @php
                        $general = $object->payments->where('object_worktype_id', 7)->sum('amount');
                        $one =  $object->payments->where('object_worktype_id', 1)->sum('amount');
                        $twoFour = $object->payments->whereIn('object_worktype_id', [2, 4])->sum('amount');
                        $oneBalance = ($one / ($one + $twoFour) * $general) + $one;
                        $twoFourBalance = ($twoFour / ($one + $twoFour) * $general) + $twoFour;
                    @endphp

                    <div class="pivot-box border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3 position-relative">
                        <a href="{{ route('objects.payments.index', $object) }}">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bolder {{ $object->total_balance < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($object->total_balance, 2, '.', ' ') }}</div>
                            </div>
                            <div class="fw-bold fs-6 text-gray-400">
                                Баланc
                            </div>
                        </a>
                        <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $object->total_balance }}">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                </svg>
                            </span>
                        </button>
                    </div>

                    <div class="pivot-box border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3 position-relative">
                        <a href="{{ route('objects.payments.index', $object) }}?object_worktype_id%5B%5D=1">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bolder {{ $oneBalance < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($oneBalance, 2, '.', ' ') }}</div>
                            </div>
                            <div class="fw-bold fs-6 text-gray-400">
                                Баланс (1)
                            </div>
                        </a>
                        <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $oneBalance }}">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                </svg>
                            </span>
                        </button>
                    </div>

                    <div class="pivot-box border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3 position-relative">
                        <a href="{{ route('objects.payments.index', $object) }}?object_worktype_id%5B%5D=2&object_worktype_id%5B%5D=4">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bolder {{ $twoFourBalance < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($twoFourBalance, 2, '.', ' ') }}</div>
                            </div>
                            <div class="fw-bold fs-6 text-gray-400">Баланс (2 и 4)</div>
                        </a>
                        <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $twoFourBalance }}">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                @else
                    <div class="pivot-box border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3 position-relative">
                        <a href="{{ route('objects.payments.index', $object) }}">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bolder {{ $object->total_balance < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($object->total_balance, 2, '.', ' ') }}</div>
                            </div>
                            <div class="fw-bold fs-6 text-gray-400">
                                Баланc
                            </div>
                        </a>
                        <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $object->total_balance }}">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                @endif

                <div class="pivot-box border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3 position-relative">
                    <a href="{{ route('objects.payments.index', $object) }}?amount_expression=<0">
                        <div class="d-flex align-items-center">
                            <div class="fs-4 fw-bolder text-danger">{{ number_format($object->total_pay, 2, '.', ' ') }}</div>
                        </div>
                        <div class="fw-bold fs-6 text-gray-400">Расходы</div>
                    </a>
                    <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $object->total_pay }}">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                            </svg>
                        </span>
                    </button>
                </div>
                <div class="pivot-box border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3 position-relative">
                    <a href="{{ route('objects.payments.index', $object) }}?amount_expression=>%3D0">
                        <div class="d-flex align-items-center">
                            <div class="fs-4 fw-bolder text-success">{{ number_format($object->total_receive, 2, '.', ' ') }}</div>
                        </div>
                        <div class="fw-bold fs-6 text-gray-400">Приходы</div>
                    </a>
                    <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $object->total_receive }}">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                            </svg>
                        </span>
                    </button>
                </div>
{{--                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">--}}
{{--                    <div class="d-flex align-items-center">--}}
{{--                        <div class="fs-4 fw-bolder text-danger">{{ number_format($object->getContractorDebtsAmount(), 2, '.', ' ') }}</div>--}}
{{--                    </div>--}}
{{--                    <div class="fw-bold fs-6 text-gray-400">Долг подрядчикам</div>--}}
{{--                </div>--}}
{{--                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">--}}
{{--                    <div class="d-flex align-items-center">--}}
{{--                        <div class="fs-4 fw-bolder text-danger">{{ number_format($object->getProviderDebtsAmount(), 2, '.', ' ') }}</div>--}}
{{--                    </div>--}}
{{--                    <div class="fw-bold fs-6 text-gray-400">Долг поставщикам</div>--}}
{{--                </div>--}}
            </div>
        </div>
    </div>
</div>
