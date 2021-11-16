<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyExchangeRate extends Model
{
    protected $table = 'currency_exchange_rates';

    protected $fillable = ['currency', 'date', 'rate', 'diff_rate'];

    public $timestamps = false;
}
