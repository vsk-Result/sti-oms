<?php

namespace App\Services\AccruedTax;

use App\Helpers\Sanitizer;
use App\Models\AccruedTax\AccruedTax;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class AccruedTaxService
{
    const TAX_NAMES = [
        'НДС',
        'Налог на прибыль',
    ];

    const YEARS = [
        '2022',
        '2023',
        '2024',
        '2025',
    ];

    public function __construct(private Sanitizer $sanitizer) {}

    public function getTaxes(): Collection
    {
        return AccruedTax::all();
    }

    public function getNames(): array
    {
        return self::TAX_NAMES;
    }

    public function getDates(): array
    {
        $dates = [];
        $years = self::YEARS;

        foreach ($years as $year) {
            for ($i = 0; $i < 12; $i++) {
                $dates[$year][] = [
                    'month' => translate_month(Carbon::parse($year . '-01-01')->addMonthsNoOverflow($i)->format('F')),
                    'date' => Carbon::parse($year . '-01-01')->addMonthsNoOverflow($i)->format('Y-m-d')
                ];
            }
        }

        return $dates;
    }

    public function createTax(array $requestData): AccruedTax
    {
        return AccruedTax::create([
            'name' => $requestData['name'],
            'date' => $requestData['date'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
        ]);
    }

    public function updateTax(array $requestData): void
    {
        $tax = $this->findTax($requestData);

        if (!$tax){
            $this->createTax([
                'name' => $requestData['name'],
                'date' => $requestData['date'],
                'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            ]);

            return;
        }

        $tax->update([
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get()
        ]);
    }

    public function findTax(array $requestData): AccruedTax | null
    {
       return AccruedTax::where('name', $requestData['name'])->where('date', $requestData['date'])->first();
    }
}
