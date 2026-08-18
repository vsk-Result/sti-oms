<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\Status;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
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
        [
            'id' => 25,
            'name' => 'Сбербанк Спецсчёт',
            'logo' => '/images/banks/sber.png',
            'visible' => true
        ],
        [
            'id' => 26,
            'name' => 'БСПБ',
            'logo' => '/images/banks/sber.png',
            'visible' => true
        ],
        [
            'id' => 27,
            'name' => 'АКБ "ФОРА-БАНК"',
            'logo' => '/images/banks/sber.png',
            'visible' => true
        ],
    ];

    public function run()
    {
        foreach (self::$banks as $bank) {
            Bank::create(
                [
                    'id' => $bank['id'],
                    'created_by_user_id' => 1,
                    'name' => $bank['name'],
                    'logo' => $bank['logo'],
                    'balance_amount' => 0,
                    'status_id' => $bank['visible'] ? Status::STATUS_ACTIVE : Status::STATUS_BLOCKED,
                ]
            );
        }
    }
}
