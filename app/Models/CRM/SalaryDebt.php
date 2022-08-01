<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class SalaryDebt extends Model
{
    protected $table = 'pivot_salary_debt';

    protected $connection = 'mysql_crm';
}
