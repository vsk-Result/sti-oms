<tr class="{{ isset($isNewPayment) ? 'new-row' : '' }}" data-payment-update-url="{{ route('payments.update', $payment) }}">
    <td class="ps-4">
        <input name="object_code" type="text" class="form-control form-control-sm form-control-solid db-field" value="{{ $payment->getObject() }}" autocomplete="off" data-index="0" />
    </td>
    <td>
        <input name="code" type="text" class="form-control form-control-sm form-control-solid db-field" value="{{ $payment->code }}" autocomplete="off" data-index="1"/>
    </td>
    <td>
        @php
            $organization = $payment->organizationSender;
            if ($payment->amount < 0) {
                $organization = $payment->organizationReceiver;
            }

            $hasMoreWithINN = false;
            if ($organization && !empty($organization->inn)) {
                $moreOrganizations = \App\Models\Organization::where('id', '!=', $organization->id)->where('inn', $organization->inn)->get();

                if ($moreOrganizations->count() > 0) {
                    $hasMoreWithINN = true;
                }
            }
        @endphp

        @if ($hasMoreWithINN)
            <span
                class="text-danger border-bottom-dashed"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                data-bs-html="true"
                data-bs-delay-hide="1000"
                title="С ИНН {{ $organization->inn }} в базе есть еще контрагенты: {{ implode(', ', $moreOrganizations->pluck('name')->toArray()) }}"
            >{{ $organization?->name ?? '' }}</span>
        @else
            <span>{{ $organization?->name ?? '' }}</span>
        @endif
    </td>
    <td>
        <input name="description" type="text" class="form-control form-control-sm form-control-solid db-field" value="{{ $payment->description }}" />
    </td>
    <td>
        @php
            $textClass = $payment->amount >= 0 ? 'text-success' : 'text-danger';
        @endphp
        <input
            name="amount"
            type="text"
            class="form-control form-control-sm form-control-solid {{ $textClass }} db-field"
            value="{{ number_format($payment->currency === 'RUB' ? $payment->amount : $payment->currency_amount, 2, '.', '') }}"
            autocomplete="off"
            data-index="2"
        />
    </td>
    <td>
        <select
            name="category"
            class="form-select form-select-solid form-select-sm"
            data-control="select2"
            data-placeholder="-"
            data-allow-clear="true"
            data-hide-search="true"
        >
            <option></option>
            @foreach($categories as $category)
                <option value="{{ $category }}" {{ $category === $payment->category ? 'selected' : '' }}>{{ $category }}</option>
            @endforeach
        </select>
    </td>
    <td class="text-end">
        @include('partials.status', ['status' => $payment->getStatus()])
    </td>
    <td>
        @if ($payment->isNeedSplit())
            <a title="Разбить по базе CRM" href="javascript:void(0);" data-payment-amount="{{ $payment->amount }}" data-split-payment-url="{{ route('payments.split.store', [$payment]) }}" class="split-payment btn btn-sm btn-icon btn-white btn-active-color-dark fs-8"><i class="fas fa-divide"></i></a>
        @else
            <a title="Дублировать" href="javascript:void(0);" data-payment-id="{{ $payment->id }}" class="clone-payment btn btn-sm btn-icon btn-white btn-active-color-dark fs-8"><i class="fas fa-clone"></i></a>
        @endif
        <a title="Удалить" href="javascript:void(0);" data-payment-destroy-url="{{ route('payments.destroy', $payment) }}" class="destroy-payment btn btn-sm btn-icon btn-white btn-active-color-danger fs-8"><i class="fas fa-trash-alt"></i></a>
    </td>
</tr>
