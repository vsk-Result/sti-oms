<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyExchangeRate extends Model
{
    protected $table = 'currency_exchange_rates';

    protected $fillable = ['currency', 'date', 'rate', 'diff_rate'];

    public $timestamps = false;

    public static function format(string $amount, string $currency, int $decimals = 0, bool $dashEmpty = false): string
    {
        if ($dashEmpty && $amount == 0) {
            return '-';
        }

        $amount = number_format($amount, $decimals, '.', ' ');
        return match ($currency) {
            'EUR' => $amount . ' €',
            'USD' => '$ ' . $amount,
            'RUB' => $amount . ' ₽',
            default => $amount,
        };
    }
}
