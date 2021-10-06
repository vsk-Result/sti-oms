<?php

namespace App\Models;

use App\Imports\Bank\PSBImport;
use App\Imports\Bank\SberbankImport;
use App\Imports\Bank\SovkombankImport;
use App\Imports\Bank\VTBImport;

class Bank
{
    private static array $banks = [
        [
            'id' => 1,
            'name' => 'ПАО "ВТБ"',
            'class' => VTBImport::class,
        ],
        [
            'id' => 2,
            'name' => 'ПАО "Промсвязьбанк"',
            'class' => PSBImport::class,
        ],
        [
            'id' => 3,
            'name' => 'ПАО "Сбербанк"',
            'class' => SberbankImport::class,
        ],
        [
            'id' => 4,
            'name' => 'ПАО "Совкомбанк"',
            'class' => SovkombankImport::class,
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

    public static function getBankImportClass(int $bankId): string
    {
        return static::$banks[$bankId - 1]['class'];
    }

    public static function getBankName(int $bankId): string
    {
        return static::$banks[$bankId - 1]['name'];
    }
}
