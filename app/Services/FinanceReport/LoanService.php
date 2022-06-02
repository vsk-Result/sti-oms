<?php

namespace App\Services\FinanceReport;

use App\Models\Company;
use App\Models\Organization;
use App\Models\Payment;
use App\Services\CurrencyExchangeRateService;
use Carbon\Carbon;

class LoanService
{
    private CurrencyExchangeRateService $rateService;

    public function __construct(CurrencyExchangeRateService $rateService)
    {
        $this->rateService = $rateService;
    }

    public function getLoans(string|Carbon $date, Company $company): array
    {
        $DTOrganization = Organization::where('name', 'ООО "ДТ ТЕРМО ГРУПП"')->first();
        $PTIOrganization = Organization::where('name', 'ООО "ПРОМТЕХИНЖИНИРИНГ"')->first();

        $totalLoanDTAmount = -Payment::where('date', '>=', '2020-12-17')
            ->where('date', '<=', $date)
            ->where('type_id', Payment::TYPE_TRANSFER)
            ->where('bank_id', '!=', 6)
            ->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)
            ->where(function ($q) use ($DTOrganization) {
                $q->where('organization_receiver_id', $DTOrganization->id);
                $q->orWhere('organization_sender_id', $DTOrganization->id);
            })
            ->sum('amount');

        $totalLoanPTIAmount = -Payment::where('date', '>=', '2021-02-12')
            ->where('date', '<=', $date)
            ->where('company_id', $company->id)
            ->where('type_id', Payment::TYPE_TRANSFER)
            ->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)
            ->where('description', '!=', 'PATRIOT')
            ->where('description', 'NOT LIKE', '%Оплата процентов%')
            ->where(function ($q) {
                $q->where('bank_id', 1);
                $q->orWhere('bank_id', 3);
                $q->orWhere('bank_id', null);
            })
            ->where(function ($q) use ($PTIOrganization) {
                $q->where('organization_receiver_id', $PTIOrganization->id);
                $q->orWhere('organization_sender_id', $PTIOrganization->id);
            })
            ->sum('amount');

        $rate = $this->rateService->getExchangeRate($date, 'EUR')->rate;

        $loans = [
            'ООО "Велесстрой"' => -277000000,
            'ООО «ПСБ БИЗНЕС»' => -49990000,
            'Завидово займ' => -30000000,
            'ООО "ЭКСКВИЗИТ"' => -11000000,
            'Белензия (1 243 164 €)' => -1243164 * $rate,
            'ООО "ДТ ТЕРМО ГРУПП"' => $totalLoanDTAmount,
            'ООО "ПРОМТЕХИНЖИНИРИНГ"' => $totalLoanPTIAmount,
            'ООО "Мечтариум"' => 31585073 - Payment::where('description', 'LIKE', '%по Делу А40-93849/202%')->sum('amount'),
            'ООО "АМТРЕЙД ИНЖЕНЕРИНГ"' => 8146061,
            'Локальные займы' => 3000000,
            'Белензия (389 523 €)' => 389523 * $rate,
        ];

        return $loans;
    }
}
