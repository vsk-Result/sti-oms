<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $connection = 'mysql_crm';
}
