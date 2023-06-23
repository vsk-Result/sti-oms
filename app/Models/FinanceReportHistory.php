<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceReportHistory extends Model
{
    use SoftDeletes;

    protected $table = 'finance_report_history';

    protected $fillable = [
        'date', 'balances', 'credits', 'loans', 'deposits', 'objects'
    ];
}
