<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $connection = 'mysql_crm';
}
