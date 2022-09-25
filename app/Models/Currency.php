<?php

namespace App\Models;

class Currency
{
    private static array $currencies = [
        'RUB' => 'RUB',
        'USD' => 'USD',
        'EUR' => 'EUR'
    ];

    public static function getCurrencies(): array
    {
        return self::$currencies;
    }
}
