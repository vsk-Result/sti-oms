<?php

namespace App\Services;

use App\Models\CurrencyExchangeRate;
use Carbon\Carbon;

class CurrencyExchangeRateService
{
    const CBRAPIUrl = 'http://www.cbr.ru/scripts/XML_daily.asp';

    public function getExchangeRate(string $date, string $currency): ?CurrencyExchangeRate
    {
        $prevDate = Carbon::parse($date)->subDay()->format('Y-m-d');

        $exchangeRate = CurrencyExchangeRate::where('date', $date)->where('currency', $currency)->first();

        if (! $exchangeRate) {
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

    private function parseRateFromCBR(string $date, string $currency): ?float
    {
        $date = Carbon::parse($date)->format('d/m/Y');
        $requestUrl = self::CBRAPIUrl . '?' . http_build_query(['date_req' => $date]);
        $parseXMLData = simplexml_load_file($requestUrl);

        if (! empty($parseXMLData)) {
            foreach ($parseXMLData as $value) {
                if ((string) $value->{'CharCode'} === $currency) {
                    return (float) str_replace(',', '.', (string) $value->{'Value'});
                }
            }
        }

        return null;
    }
}
