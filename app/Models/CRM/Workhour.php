<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class Workhour extends Model
{
    protected $table = 'workhours';

    protected $connection = 'mysql_crm';

    public $timestamps = false;

    protected $fillable = ['hours', 'absence', 'shtraf', 'isConfirm', 'foreman_id'];
}
