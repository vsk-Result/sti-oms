<?php

namespace App\Models\SERVICE;

use Illuminate\Database\Eloquent\Model;

class WorkhourPivot extends Model
{
    protected $table = 'workhour_pivot';

    protected $connection = 'mysql_service';

    public $timestamps = false;

    protected $fillable = ['code', 'worktype', 'is_main', 'employees', 'hours', 'amount', 'rate', 'date'];
}
