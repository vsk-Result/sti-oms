<?php

namespace App\Models;

class Bank
{
    private static array $banks = [
        [
            'id' => 1,
            'name' => 'ПАО "ВТБ"',
            'logo' => '/images/banks/vtb.png',
            'visible' => true
        ],
        [
            'id' => 2,
            'name' => 'ПАО "Промсвязьбанк"',
            'logo' => '/images/banks/promsvyazbank.png',
            'visible' => true
        ],
        [
            'id' => 3,
            'name' => 'ПАО "Сбербанк"',
            'logo' => '/images/banks/sber.png',
            'visible' => true
        ],
        [
            'id' => 4,
            'name' => 'ПАО "Совкомбанк"',
            'logo' => '/images/banks/sovkombank.jpg',
            'visible' => true
        ],
        [
            'id' => 5,
            'name' => 'ПАО "Росбанк"',
            'logo' => '/images/banks/rosbank.png',
            'visible' => true
        ],
        [
            'id' => 6,
            'name' => 'АО "КУБ"',
            'logo' => '/images/banks/kub.png',
            'visible' => true
        ],
        [
            'id' => 7,
            'name' => 'ПАО "МКБ"',
            'logo' => '/images/banks/mkb.png',
            'visible' => true
        ],
        [
            'id' => 8,
            'name' => 'АО "ОТП"',
            'logo' => '/images/banks/otp.jpg',
            'visible' => true
        ],
        [
            'id' => 9,
            'name' => 'АО "ЮниКредит Банк"',
            'logo' => '/images/banks/unicredit.jpg',
            'visible' => true
        ],
        [
            'id' => 10,
            'name' => 'АКБ "АБСОЛЮТ БАНК"',
            'logo' => '/images/banks/ab.png',
            'visible' => true
        ],
        [
            'id' => 11,
            'name' => 'АО "АЛЬФА БАНК"',
            'logo' => '/images/banks/alfa.jpg',
            'visible' => true
        ],
        [
            'id' => 12,
            'name' => 'ВТБ Тинькофф',
            'logo' => '/images/banks/vtb.png',
            'visible' => false
        ],
        [
            'id' => 13,
            'name' => 'ВТБ Тинькофф',
            'logo' => '/images/banks/vtb.png',
            'visible' => true
        ],
    ];

    public static function getBanks(): array
    {
        $banks = [];
        foreach (static::$banks as $bank) {
            if ($bank['visible']) {
                $banks[$bank['id']] = $bank['name'];
            }
        }
        return $banks;
    }

    public static function getBankName(int $bankId): string
    {
        return static::$banks[$bankId - 1]['name'];
    }

    public static function getBankLogo(int $bankId): string
    {
        return static::$banks[$bankId - 1]['logo'];
    }
}
