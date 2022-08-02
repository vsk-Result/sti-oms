<?php

namespace App\Models;

class Bank
{
    private static array $banks = [
        [
            'id' => 1,
            'name' => 'ПАО "ВТБ"',
        ],
        [
            'id' => 2,
            'name' => 'ПАО "Промсвязьбанк"',
        ],
        [
            'id' => 3,
            'name' => 'ПАО "Сбербанк"',
        ],
        [
            'id' => 4,
            'name' => 'ПАО "Совкомбанк"',
        ],
        [
            'id' => 5,
            'name' => 'ПАО "Росбанк"',
        ],
        [
            'id' => 6,
            'name' => 'АО "КУБ"',
        ],
        [
            'id' => 7,
            'name' => 'ПАО "МКБ"',
        ],
        [
            'id' => 8,
            'name' => 'АО "ОТП"',
        ],
        [
            'id' => 9,
            'name' => 'АО "ЮниКредит Банк"',
        ],
        [
            'id' => 10,
            'name' => 'АКБ "АБСОЛЮТ БАНК" (ПАО)',
        ]
    ];

    public static function getBanks(): array
    {
        $banks = [];
        foreach (static::$banks as $bank) {
            $banks[$bank['id']] = $bank['name'];
        }
        return $banks;
    }

    public static function getBankName(int $bankId): string
    {
        return static::$banks[$bankId - 1]['name'];
    }
}
