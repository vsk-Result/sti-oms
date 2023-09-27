<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class SalaryPaidMonth extends Model
{
    protected $table = 'salary_paid_months';

    protected $connection = 'mysql_crm';
}
