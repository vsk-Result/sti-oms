<?php

namespace App\Services;

use App\Models\CurrencyExchangeRate;
use Carbon\Carbon;

class CurrencyExchangeRateService
{
    const CBRAPIUrl = 'http://www.cbr.ru/scripts/XML_daily.asp';

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
//        $date = Carbon::parse($date)->format('d/m/Y');
//        $requestUrl = self::CBRAPIUrl . '?date_req=' . $date;
//        $parseXMLData = simplexml_load_file($requestUrl);
//
//        if (! empty($parseXMLData)) {
//            foreach ($parseXMLData as $value) {
//                if ((string) $value->{'CharCode'} === $currency) {
//                    return (float) str_replace(',', '.', (string) $value->{'Value'});
//                }
//            }
//        }

        return null;
    }
}
