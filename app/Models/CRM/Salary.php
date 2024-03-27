<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $table = 'employee_salaries';

    protected $connection = 'mysql_crm';
}
