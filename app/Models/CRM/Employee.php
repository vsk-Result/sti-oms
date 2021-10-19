<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';

    protected $connection = 'mysql_crm';

    public function getFullname()
    {
        return "$this->secondname $this->firstname $this->thirdname";
    }
}
