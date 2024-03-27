<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class Difference extends Model
{
    protected $table = 'differences';

    protected $connection = 'mysql_crm';

    protected $fillable = ['finance_flag'];

    public $timestamps = false;
}
