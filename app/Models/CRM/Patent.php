<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class Patent extends Model
{
    protected $table = 'employee_patents';

    protected $connection = 'mysql_crm';
}
