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
            'id' => 23,
            'name' => 'КУБ ЦОД',
            'logo' => '/images/banks/kub.png',
            'visible' => true
        ],
        [
            'id' => 13,
            'name' => 'ВТБ Тинькофф',
            'logo' => '/images/banks/vtb.png',
            'visible' => true
        ],
        [
            'id' => 15,
            'name' => 'ВТБ Камчатка',
            'logo' => '/images/banks/vtb.png',
            'visible' => true
        ],
        [
            'id' => 18,
            'name' => 'ВТБ Запасной',
            'logo' => '/images/banks/vtb.png',
            'visible' => true
        ],
        [
            'id' => 19,
            'name' => 'ВТБ Спецсчёт',
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
            'name' => 'АО “КУБ" Спецсчет',
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
            'id' => 14,
            'name' => 'АО "Райффайзенбанк"',
            'logo' => '/images/banks/raifaizen.png',
            'visible' => true
        ],
        [
            'id' => 16,
            'name' => 'ВТБ Аэрострой',
            'logo' => '/images/banks/vtb.png',
            'visible' => true
        ],
        [
            'id' => 17,
            'name' => 'АО "Газпромбанк"',
            'logo' => '/images/banks/vtb.png',
            'visible' => true
        ],
        [
            'id' => 20,
            'name' => 'АО "КУБ"',
            'logo' => '/images/banks/kub.png',
            'visible' => true
        ],
        [
            'id' => 21,
            'name' => 'Т-банк',
            'logo' => '/images/banks/vtb.png',
            'visible' => true
        ],
        [
            'id' => 22,
            'name' => 'ВТБ Кольцово Камчатка',
            'logo' => '/images/banks/vtb.png',
            'visible' => true
        ],
        [
            'id' => 24,
            'name' => 'РосДорБанк',
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
        $findBank = self::getById($bankId);
        return $findBank ? $findBank['name'] : '';
    }

    public static function getBankLogo(int $bankId): string
    {
        $findBank = self::getById($bankId);
        return $findBank ? $findBank['logo'] : '';
    }

    private static function getById(int $bankId)
    {
        $findBank = null;
        foreach (static::$banks as $bank) {
            if ($bank['visible'] && $bank['id'] === $bankId) {
                $findBank = $bank;
                break;
            }
        }
        return $findBank;
    }
}
