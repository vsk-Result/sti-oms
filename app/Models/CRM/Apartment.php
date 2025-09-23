<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    protected $table = 'apartments';

    protected $connection = 'mysql_crm';
}
