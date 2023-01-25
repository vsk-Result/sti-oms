<?php

namespace App\Services;

use App\Models\CurrencyExchangeRate;
use Carbon\Carbon;
use DOMDocument;

class CurrencyExchangeRateService
{
    public function getExchangeRate(string $date, string $currency): ?CurrencyExchangeRate
    {
        if (Carbon::now()->isMonday()) {
            $prevDate = Carbon::parse($date)->subDayS(3)->format('Y-m-d');
        } else if (Carbon::now()->isSunday()) {
            $prevDate = Carbon::parse($date)->subDayS(2)->format('Y-m-d');
        } else {
            $prevDate = Carbon::parse($date)->subDay()->format('Y-m-d');
        }

        $exchangeRate = CurrencyExchangeRate::where('date', $date)->where('currency', $currency)->first();

        if (! $exchangeRate) {

            $canCreateRate = true;
            if (Carbon::parse($date)->greaterThan(Carbon::now())) {
                $start = Carbon::createFromTimeString('18:00');
                $end = Carbon::createFromTimeString('23:59');
                if (! Carbon::now()->between($start, $end)) {
                    $canCreateRate = false;
                }
            }

            if (! $canCreateRate) {
                return null;
            }

            $rate = $this->parseRateFromCBR($date, $currency);
            $prevRate = $this->parseRateFromCBR($prevDate, $currency);

            if ($rate && $prevRate) {
                $exchangeRate = new CurrencyExchangeRate();
                $exchangeRate->currency = $currency;
                $exchangeRate->date = $date;
                $exchangeRate->rate = $rate;
                $exchangeRate->diff_rate = $rate - $prevRate;
                $exchangeRate->save();
            }
        }

        return $exchangeRate;
    }

    public function parseRateFromCBR(string $date, string $currency): ?float
    {
        $currenciesList = ['USD' => 0, 'EUR' => 0];

        $xml = new DOMDocument();
        $url = 'http://www.cbr.ru/scripts/XML_daily.asp?date_req=' . Carbon::parse($date)->format('d.m.Y');

        if (@$xml->load($url)) {
            $root = $xml->documentElement;
            $items = $root->getElementsByTagName('Valute');

            foreach ($items as $item) {
                $code = $item->getElementsByTagName('CharCode')->item(0)->nodeValue;

                if (isset($currenciesList[$code])) {
                    $curs = $item->getElementsByTagName('Value')->item(0)->nodeValue;
                    $currenciesList[$code] = floatval(str_replace(',', '.', $curs));
                }
            }
        }

        return $currenciesList[$currency] ?? 0;
    }
}
