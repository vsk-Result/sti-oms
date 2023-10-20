<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PivotObjectDebt extends Model
{
    use SoftDeletes;

    protected $table = 'pivot_object_debts';

    protected $fillable = [
        'date', 'object_id', 'provider', 'contractor', 'service'
    ];
}
